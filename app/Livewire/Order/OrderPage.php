<?php

namespace App\Livewire\Order;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Livewire\Actions\Order\ProcessWalletPaymentAction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemCustomization;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\View\View;
use Livewire\Component;

final class OrderPage extends Component
{
    public array $cart = [];
    public array $customizations = [];
    public ?Product $currentProduct = null;
    public bool $isCustomizing = false;
    public string $specialInstructions = '';
    public string $personalMessage = '';
    public string $redemptionType = 'in-store'; // 'in-store' or 'online'
    public int $pointsToRedeem = 0;
    public string $paymentMethod = 'cash'; // 'cash', 'card', 'wallet'
    public bool $insufficientBalance = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        // Initialize the cart
        $this->resetCustomization();

        // Set default payment method to wallet if user is logged in
        if (auth()->check()) {
            $this->paymentMethod = 'wallet';
        }
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $products = Product::where('is_available', true)
            ->orderBy('display_order')
            ->get();

        $cartTotal = $this->calculateCartTotal();
        $subtotal = $cartTotal;
        $tax = $subtotal * 0.10; // Assuming 10% tax
        $discount = $this->pointsToRedeem * 0.10; // Assuming 10 cents per point
        $totalAmount = $subtotal + $tax - $discount;

        // Get user wallet balance if authenticated
        $walletBalance = auth()->check() ? auth()->user()->balanceFloat : 0;
        $hasEnoughBalance = $walletBalance >= $totalAmount;

        return view('livewire.order.order-page', [
            'products' => $products,
            'cartTotal' => $cartTotal,
            'walletBalance' => $walletBalance,
            'hasEnoughBalance' => $hasEnoughBalance,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function startCustomizing(Product $product): void
    {
        $this->currentProduct = $product;
        $this->isCustomizing = true;
    }

    public function addToCart(): void
    {
        if (! $this->currentProduct) {
            return;
        }

        $productId = $this->currentProduct->id;
        $cartItem = [
            'product_id' => $productId,
            'product_name' => $this->currentProduct->name,
            'quantity' => 1,
            'unit_price' => $this->currentProduct->base_price,
            'total_price' => $this->currentProduct->base_price,
            'customization_cost' => 0,
            'customizations' => $this->customizations[$productId] ?? [],
            'special_instructions' => $this->specialInstructions,
        ];

        // Calculate additional costs from customizations
        if (isset($this->customizations[$productId])) {
            foreach ($this->customizations[$productId] as $category => $optionId) {
                $option = ProductOption::find($optionId);
                if ($option) {
                    $cartItem['customization_cost'] += $option->additional_price;
                    $cartItem['total_price'] += $option->additional_price;
                }
            }
        }

        // Add to cart or update quantity if already exists
        $found = false;
        foreach ($this->cart as $key => $item) {
            if (
                $item['product_id'] === $productId &&
                $item['customizations'] === $cartItem['customizations'] &&
                $item['special_instructions'] === $cartItem['special_instructions']
            ) {
                $this->cart[$key]['quantity']++;
                $this->cart[$key]['total_price'] = $this->cart[$key]['quantity'] *
                    ($this->cart[$key]['unit_price'] + $this->cart[$key]['customization_cost']);
                $found = true;
                break;
            }
        }

        if (! $found) {
            $this->cart[] = $cartItem;
        }

        $this->resetCustomization();
        $this->dispatch('cart-updated');
    }

    /**
     * Add a product to the cart directly by its ID.
     * This prevents type mismatch errors when adding from the product list.
     */
    public function addProductToCart(int $productId): void
    {
        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        $this->currentProduct = $product;
        $this->addToCart();
    }

    public function updateQuantity($index, $change): void
    {
        if (isset($this->cart[$index])) {
            $newQuantity = $this->cart[$index]['quantity'] + $change;

            if ($newQuantity <= 0) {
                $this->removeFromCart($index);
            } else {
                $this->cart[$index]['quantity'] = $newQuantity;
                $this->cart[$index]['total_price'] = $newQuantity *
                    ($this->cart[$index]['unit_price'] + $this->cart[$index]['customization_cost']);
            }

            $this->dispatch('cart-updated');
        }
    }

    public function removeFromCart($index): void
    {
        if (isset($this->cart[$index])) {
            array_splice($this->cart, $index, 1);
            $this->dispatch('cart-updated');
        }
    }

    public function setCustomization($productId, $category, $optionId): void
    {
        if (! isset($this->customizations[$productId])) {
            $this->customizations[$productId] = [];
        }

        $this->customizations[$productId][$category] = $optionId;
    }

    public function resetCustomization(): void
    {
        $this->currentProduct = null;
        $this->isCustomizing = false;
        $this->specialInstructions = '';
    }

    /**
     * Update payment method and validate wallet balance if selected.
     */
    public function updatedPaymentMethod(): void
    {
        if ($this->paymentMethod === 'wallet') {
            $this->validateWalletBalance();
        } else {
            $this->insufficientBalance = false;
        }
    }

    /**
     * Place an order with selected payment method.
     */
    public function placeOrder()
    {
        if (empty($this->cart)) {
            $this->addError('cart', 'Your cart is empty');

            return redirect()->back();
        }

        // Validate wallet balance if wallet payment is selected
        if ($this->paymentMethod === 'wallet' && ! $this->validateWalletBalance()) {
            return redirect()->back();
        }

        // Calculate totals
        $subtotal = $this->calculateCartTotal();
        // $tax = $subtotal * 0.0;
        // $discount = $this->pointsToRedeem * 0.10; // Assuming 10 cents per point
        // $totalAmount = $subtotal + $tax - $discount;
        $totalAmount = $subtotal; // Adjust this based on your business logic

        // Get the default branch (adjust this based on your business logic)
        $defaultBranch = \App\Models\Branch::first();

        if (! $defaultBranch) {
            $this->addError('branch', 'No branch is available for processing orders');

            return redirect()->back();
        }

        // Create order
        $order = Order::create([
            'user_id' => auth()->id() ?? null, // Optional: if user is logged in
            'branch_id' => $defaultBranch->id, // Add the required branch_id field
            'status' => OrderStatus::PENDING,
            'payment_status' => $this->paymentMethod === 'wallet' ? PaymentStatus::PAID : PaymentStatus::PENDING,
            'payment_method' => $this->paymentMethod,
            'subtotal' => $subtotal,
            'tax' => $tax ?? 0.00,
            'discount' => $discount ?? 0.00,
            'total_amount' => $totalAmount,
            'points_redeemed' => $this->pointsToRedeem,
            'points_earned' => (int) floor($totalAmount), // Example: 1 point per dollar
            'special_instructions' => $this->personalMessage,
        ]);

        // Create order items
        foreach ($this->cart as $item) {
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'customization_cost' => $item['customization_cost'],
                'total_price' => $item['total_price'],
                'special_instructions' => $item['special_instructions'],
            ]);

            // Create order item customizations
            if (! empty($item['customizations'])) {
                foreach ($item['customizations'] as $category => $optionId) {
                    // Retrieve the product option to get its price
                    $productOption = ProductOption::find($optionId);
                    if ($productOption) {
                        OrderItemCustomization::create([
                            'order_item_id' => $orderItem->id,
                            'product_option_id' => $optionId,
                            'option_price' => $productOption->additional_price ?? 0.00,
                        ]);
                    }
                }
            }
        }

        // Process wallet payment if selected
        if ($this->paymentMethod === 'wallet' && auth()->check()) {
            app(ProcessWalletPaymentAction::class)->handle(
                user: auth()->user(),
                order: $order,
                amount: $totalAmount
            );
        }

        // Reset cart and redirect
        $this->cart = [];
        $this->personalMessage = '';
        $this->pointsToRedeem = 0;
        $this->paymentMethod = auth()->check() ? 'wallet' : 'cash';

        // Redirect to order confirmation

        return redirect()->route('orders.track', ['orderId' => $order->id]);
    }

    /**
     * Validate if user has enough wallet balance for the current order.
     */
    private function validateWalletBalance(): bool
    {
        if (! auth()->check()) {
            $this->addError('payment', 'Please log in to use wallet payment');

            return false;
        }

        $totalAmount = $this->calculateOrderTotal();
        $walletBalance = auth()->user()->balanceFloat;

        if ($walletBalance < $totalAmount) {
            $this->insufficientBalance = true;
            $this->addError('payment', 'Insufficient wallet balance');

            return false;
        }

        $this->insufficientBalance = false;

        return true;
    }

    /**
     * Calculate the total order amount including tax and discounts.
     */
    private function calculateOrderTotal(): float
    {
        $subtotal = $this->calculateCartTotal();
        $tax = $subtotal * 0.10; // Assuming 10% tax
        $discount = $this->pointsToRedeem * 0.10; // Assuming 10 cents per point

        return $subtotal + $tax - $discount;
    }

    private function calculateCartTotal(): float
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['total_price'];
        }

        return $total;
    }
}

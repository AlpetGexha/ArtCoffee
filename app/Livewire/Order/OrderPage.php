<?php

namespace App\Livewire\Order;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Enum\ProductCategory;
use App\Livewire\Actions\Order\ProcessWalletPaymentAction;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemCustomization;
use App\Models\Product;
use App\Models\ProductOption;
use App\Services\LoyaltyService;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Filament\Notifications\Notification;


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
    public bool $usePoints = false; // New property for loyalty points payment
    public int $availablePoints = 0; // New property for user's available points
    public int $requiredPoints = 0; // New property for points required for purchase

    #[Url]
    public string $search = '';

    #[Url]
    public ?string $categoryFilter = null;

    #[Url]
    public ?int $menuFilter = null;

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
            $this->availablePoints = auth()->user()->loyalty_points ?? 0;
        }
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        // Query to get products based on search and category filters
        $productsQuery = Product::where('is_available', true);

        // Apply menu filter if provided
        if ($this->menuFilter) {
            $menu = Menu::with('products')->find($this->menuFilter);
            if ($menu) {
                $productIds = $menu->products->pluck('id')->toArray();
                $productsQuery->whereIn('id', $productIds);
            }
        }

        // Apply search filter if provided
        if ($this->search) {
            $productsQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply category filter if provided
        if ($this->categoryFilter) {
            $productsQuery->where('category', $this->categoryFilter);
        }

        // Get filtered products
        $products = $productsQuery->orderBy('display_order')->get();

        // Get all available categories
        $categories = ProductCategory::cases();

        // Get all available menus
        $menus = Menu::all();

        $cartTotal = $this->calculateCartTotal();
        $subtotal = $cartTotal;

        // Tax calculation - 18% is already included in the price
        $taxRate = 0.18;
        $preTaxAmount = $subtotal / (1 + $taxRate);
        $tax = $subtotal - $preTaxAmount;

        // Get loyalty service
        $loyaltyService = app(LoyaltyService::class);

        // Update available points if user is authenticated
        if (auth()->check()) {
            $this->availablePoints = auth()->user()->loyalty_points ?? 0;
        }

        // Calculate required points for the current order
        $this->requiredPoints = $loyaltyService->calculatePointsEarned($subtotal);
        $hasEnoughLoyaltyPoints = auth()->check() && $this->availablePoints >= $this->requiredPoints;

        // Calculate discount if using loyalty points
        $discount = 0;
        if ($this->usePoints && $hasEnoughLoyaltyPoints) {
            $discount = $subtotal; // Full discount if using points for the entire order
        } else {
            $discount = $this->pointsToRedeem * 0.10; // Assuming 10 cents per point as before
        }

        $totalAmount = $subtotal - $discount;

        // Get user wallet balance if authenticated
        $walletBalance = auth()->check() ? auth()->user()->balanceFloat : 0;
        $hasEnoughBalance = $walletBalance >= $totalAmount;

        // Format points value as dollars for display
        $pointsValueFormatted = $loyaltyService->formatPointsAsDollars($this->availablePoints);

        return view('livewire.order.order-page', [
            'products' => $products,
            'categories' => $categories,
            'menus' => $menus,
            'cartTotal' => $cartTotal,
            'subtotal' => $subtotal,
            'preTaxAmount' => $preTaxAmount,
            'tax' => $tax,
            'taxRate' => $taxRate,
            'walletBalance' => $walletBalance,
            'hasEnoughBalance' => $hasEnoughBalance,
            'totalAmount' => $totalAmount,
            'availablePoints' => $this->availablePoints,
            'requiredPoints' => $this->requiredPoints,
            'hasEnoughLoyaltyPoints' => $hasEnoughLoyaltyPoints,
            'pointsValueFormatted' => $pointsValueFormatted,
        ]);
    }

    /**
     * Set menu filter
     */
    public function setMenu(?int $menuId): void
    {
        $this->menuFilter = $menuId;
    }

    /**
     * Set category filter
     */
    public function setCategory(?string $category): void
    {
        $this->categoryFilter = $category;
    }

    /**
     * Reset all filters
     */
    public function resetFilters(): void
    {
        $this->reset(['search', 'categoryFilter', 'menuFilter']);
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

        // Reset usePoints if not paying with points
        if ($this->paymentMethod !== 'points') {
            $this->usePoints = false;
        }
    }

    /**
     * Handle the loyalty points payment option toggle
     */
    public function updatedUsePoints(): void
    {
        // If turning on points payment, validate we have enough points
        if ($this->usePoints) {
            $this->validateLoyaltyPoints();
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

        // Validate loyalty points if using points payment
        if ($this->usePoints && ! $this->validateLoyaltyPoints()) {
            return redirect()->back();
        }

        // Calculate totals
        $subtotal = $this->calculateCartTotal();

        // Calculate tax - 18% is already included in the price
        $taxRate = 0.18;
        $preTaxAmount = $subtotal / (1 + $taxRate);
        $tax = $subtotal - $preTaxAmount;

        // Calculate discount based on payment method
        $discount = 0;
        if ($this->usePoints && $this->validateLoyaltyPoints()) {
            $discount = $subtotal; // Full discount if using points
        } else {
            $discount = $this->pointsToRedeem * 0.10; // 10 cents per point if partially using points
        }

        $totalAmount = $subtotal - $discount;

        // Get the default branch
        $defaultBranch = \App\Models\Branch::first();
        if (! $defaultBranch) {
            $this->addError('branch', 'No branch is available for processing orders');

            return redirect()->back();
        }

        // Create order
        $order = Order::create([
            'user_id' => auth()->id() ?? null,
            'branch_id' => $defaultBranch->id,
            'status' => OrderStatus::PENDING,
            'payment_status' => ($this->paymentMethod === 'wallet' || $this->usePoints)
                ? PaymentStatus::PAID
                : PaymentStatus::PENDING,
            'payment_method' => $this->usePoints ? 'loyalty_points' : $this->paymentMethod,
            'subtotal' => $preTaxAmount, // Store pre-tax amount as subtotal
            'tax' => $tax,
            'discount' => $discount,
            'total_amount' => $totalAmount,
            'points_redeemed' => $this->usePoints ? $this->requiredPoints : $this->pointsToRedeem,
            'points_earned' => $this->usePoints ? 0 : (int) floor($totalAmount), // No points earned if paying with points
            'special_instructions' => $this->personalMessage,
        ]);

        // Send notification to customer
        // Notification::make()
        //     ->title('Order Placed Successfully')
        //     ->success()
        //     ->sendToDatabase($order);


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
                auth()->user(),
                $order,
                $totalAmount
            );
        }

        // Process loyalty points payment if selected
        if ($this->usePoints && auth()->check()) {
            app(LoyaltyService::class)->redeemPoints(
                auth()->user(),
                $subtotal
            );
        } elseif (auth()->check() && $totalAmount > 0) {
            // Add loyalty points for the purchase if not paying with points
            app(LoyaltyService::class)->addPoints(
                auth()->user(),
                $totalAmount
            );
        }

        // Reset cart and redirect
        $this->cart = [];
        $this->personalMessage = '';
        $this->pointsToRedeem = 0;
        $this->usePoints = false;
        $this->paymentMethod = auth()->check() ? 'wallet' : 'cash';

        // Redirect to order confirmation
        return redirect()->route('orders.track', ['orderId' => $order->id]);
    }

    /**
     * Check if a product is in the cart.
     */
    public function isProductInCart(int $productId): bool
    {
        foreach ($this->cart as $item) {
            if ($item['product_id'] === $productId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the total quantity of a product in the cart.
     */
    public function getProductQuantityInCart(int $productId): int
    {
        $quantity = 0;

        foreach ($this->cart as $item) {
            if ($item['product_id'] === $productId) {
                $quantity += $item['quantity'];
            }
        }

        return $quantity;
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
     * Validate if user has enough loyalty points for the current order.
     */
    private function validateLoyaltyPoints(): bool
    {
        if (! auth()->check()) {
            $this->addError('points', 'Please log in to use loyalty points');
            $this->usePoints = false;

            return false;
        }

        $subtotal = $this->calculateCartTotal();
        $loyaltyService = app(LoyaltyService::class);

        if (! $loyaltyService->hasEnoughPoints(auth()->user(), $subtotal)) {
            $this->addError('points', 'You do not have enough loyalty points for this purchase');
            $this->usePoints = false;

            return false;
        }

        return true;
    }

    /**
     * Calculate the total order amount including tax and discounts.
     */
    private function calculateOrderTotal(): float
    {
        $subtotal = $this->calculateCartTotal();

        // Tax is already included in the price (18%)
        $discount = 0;
        if ($this->usePoints && auth()->check()) {
            $loyaltyService = app(LoyaltyService::class);
            if ($loyaltyService->hasEnoughPoints(auth()->user(), $subtotal)) {
                $discount = $subtotal; // Full discount if using points
            }
        } else {
            $discount = $this->pointsToRedeem * 0.10; // Assuming 10 cents per point
        }

        return $subtotal - $discount;
    }

    /**
     * Send notifications to admin users about new order.
     */
    private function notifyAdmins(Order $order): void
    {
        // Get all admin users
        $admins = \App\Models\User::where('is_admin', true)->get();

        // Create a notification for each admin
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewOrderNotification($order));
        }
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

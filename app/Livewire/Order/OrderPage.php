<?php

namespace App\Livewire\Order;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Enum\ProductCategory;
use App\Livewire\Actions\Order\ProcessWalletPaymentAction;
use App\Models\Branch;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemCustomization;
use App\Models\Product;
use App\Models\ProductOption;
use App\Services\CartService;
use App\Services\LoyaltyService;
use Filament\Notifications\Notification;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Lazy()]
final class OrderPage extends Component
{
    public ?Product $currentProduct = null;
    public bool $isCustomizing = false;
    public array $customizations = [];
    public string $specialInstructions = '';
    public string $personalMessage = '';
    public string $redemptionType = 'in-store'; // 'in-store' or 'online'
    public int $pointsToRedeem = 0;
    public string $paymentMethod = 'cash'; // 'cash', 'card', 'wallet'
    public bool $insufficientBalance = false;
    public bool $usePoints = false; // Property for loyalty points payment
    public int $availablePoints = 0; // Property for user's available points
    public int $requiredPoints = 0; // Property for points required for purchase

    private readonly CartService $cartService;

    #[Url]
    public string $search = '';

    #[Url]
    public ?string $categoryFilter = null;

    #[Url]
    public ?int $menuFilter = null;

    protected $casts = [
        'customizations' => 'array',
    ];

    /**
     * Mount the component.
     */
    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        // Initialize the component
        $this->resetCustomization();

        // Set default payment method to wallet if user is logged in
        if (auth()->check()) {
            $this->paymentMethod = 'wallet';
            $this->availablePoints = auth()->user()->loyalty_points ?? 0;
        }
    }

    /**
     * Get filtered products based on search and filters.
     */
    #[Computed]
    public function products(): mixed
    {
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

        return $productsQuery->get();
    }

    /**
     * Get all available categories.
     */
    #[Computed]
    public function categories(): array
    {
        return ProductCategory::cases();
    }

    /**
     * Get all available menus.
     */
    #[Computed]
    public function menus(): mixed
    {
        return Menu::all();
    }

    /**
     * Get cart items from the CartService.
     */
    #[Computed]
    public function cart(): array
    {
        return $this->cartService->getCartItems();
    }

    /**
     * Get cart total from the CartService.
     */
    #[Computed]
    public function cartTotal(): float
    {
        return $this->cartService->calculateCartTotal();
    }

    /**
     * Get subtotal (same as cart total in this case).
     */
    #[Computed]
    public function subtotal(): float
    {
        return $this->cartTotal;
    }

    /**
     * Get pre-tax amount.
     */
    #[Computed]
    public function preTaxAmount(): float
    {
        return $this->subtotal / (1 + $this->taxRate);
    }

    /**
     * Get tax amount.
     */
    #[Computed]
    public function tax(): float
    {
        return $this->subtotal - $this->preTaxAmount;
    }

    /**
     * Get tax rate.
     */
    #[Computed]
    public function taxRate(): float
    {
        return 0.18; // 18% tax rate
    }

    /**
     * Get user's wallet balance.
     */
    #[Computed]
    public function walletBalance(): float
    {
        return auth()->check() ? auth()->user()->balanceFloat : 0;
    }

    /**
     * Check if user has enough wallet balance
     */
    #[Computed]
    public function hasEnoughBalance(): bool
    {
        return $this->walletBalance >= $this->totalAmount;
    }

    /**
     * Get discount amount based on loyalty points.
     */
    #[Computed]
    public function discount(): float
    {
        if ($this->usePoints && $this->hasEnoughLoyaltyPoints) {
            return $this->subtotal; // Full discount if using points for the entire order
        }

        return $this->pointsToRedeem * 0.10; // Assuming 10 cents per point as before
    }

    /**
     * Get total order amount after discounts.
     */
    #[Computed]
    public function totalAmount(): float
    {
        return $this->subtotal - $this->discount;
    }

    /**
     * Check if user has enough loyalty points.
     */
    #[Computed]
    public function hasEnoughLoyaltyPoints(): bool
    {
        return auth()->check() && $this->availablePoints >= $this->requiredPoints;
    }

    /**
     * Get points value formatted as dollars.
     */
    #[Computed]
    public function pointsValueFormatted(): string
    {
        return app(LoyaltyService::class)->formatPointsAsDollars($this->availablePoints);
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        // Update available points if user is authenticated
        if (auth()->check()) {
            $this->availablePoints = auth()->user()->loyalty_points ?? 0;
        }

        // Calculate required points for the current order
        $loyaltyService = app(LoyaltyService::class);
        $this->requiredPoints = $loyaltyService->calculatePointsEarned($this->subtotal);

        return view('livewire.order.order-page',[
            'products' => $this->products(),
            'categories' => $this->categories(),
            'menus' => $this->menus(),
            'cartTotal' => $this->cartTotal(),
            'subtotal' => $this->subtotal(),
            'preTaxAmount' => $this->preTaxAmount(),
            'tax' => $this->tax(),
            'taxRate' => $this->taxRate(),
            'walletBalance' => $this->walletBalance(),
            'hasEnoughBalance' => $this->hasEnoughBalance(),
            'totalAmount' => $this->totalAmount(),
            'availablePoints' => $this->availablePoints,
            'requiredPoints' => $this->requiredPoints,
            'hasEnoughLoyaltyPoints' => $this->hasEnoughLoyaltyPoints(),
            'pointsValueFormatted' => $this->pointsValueFormatted(),
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

    /**
     * Start the customization process for a product.
     */
    public function startCustomizing(Product $product): void
    {
        $this->currentProduct = $product;
        $this->isCustomizing = true;
    }

    /**
     * Add the current product to cart.
     */
    public function addToCart(): void
    {
        if (! $this->currentProduct) {
            return;
        }

        $productId = $this->currentProduct->id;
        $customizations = $this->customizations[$productId] ?? [];

        $this->cartService->addToCart(
            $this->currentProduct,
            $customizations,
            $this->specialInstructions
        );

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

    /**
     * Update the quantity of an item in the cart.
     */
    public function updateQuantity(int $index, int $change): void
    {
        $this->cartService->updateQuantity($index, $change);
        $this->dispatch('cart-updated');
    }

    /**
     * Remove an item from the cart.
     */
    public function removeFromCart(int $index): void
    {
        $this->cartService->removeFromCart($index);
        $this->dispatch('cart-updated');
    }

    /**
     * Set a customization option for a product.
     */
    public function setCustomization(int $productId, string $category, int $optionId): void
    {
        if (! isset($this->customizations[$productId])) {
            $this->customizations[$productId] = [];
        }

        $this->customizations[$productId][$category] = $optionId;
    }

    /**
     * Reset customization state.
     */
    public function resetCustomization(): void
    {
        $this->currentProduct = null;
        $this->isCustomizing = false;
        $this->specialInstructions = '';
    }

    /**
     * Handle payment method updates.
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
        $cart = $this->cartService->getCartItems();

        if (empty($cart)) {
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
        $subtotal = $this->cartService->calculateCartTotal();

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
        $defaultBranch = Branch::first();
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

        // Create order items
        foreach ($cart as $item) {
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
        $this->cartService->clearCart();
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
        return $this->cartService->isProductInCart($productId);
    }

    /**
     * Get the total quantity of a product in the cart.
     */
    public function getProductQuantityInCart(int $productId): int
    {
        return $this->cartService->getProductQuantityInCart($productId);
    }

    public function placeholder()
    {
        return view('livewire.order.partials.product-skeleton');
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

        $subtotal = $this->cartService->calculateCartTotal();
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
        $subtotal = $this->cartService->calculateCartTotal();

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
}

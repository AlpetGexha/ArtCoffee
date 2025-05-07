<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;

final class CartService
{
    /**
     * The session key used for storing cart data.
     */
    private const CART_SESSION_KEY = 'coffee_art_shop_cart';

    /**
     * Create a new CartService instance.
     */
    public function __construct(
        private readonly SessionManager $session
    ) {
    }

    /**
     * Get all items in the cart.
     */
    public function getCartItems(): array
    {
        return $this->session->get(self::CART_SESSION_KEY, []);
    }

    /**
     * Add a product to the cart.
     */
    public function addToCart(Product $product, array $customizations = [], string $specialInstructions = ''): void
    {
        $cart = $this->getCartItems();
        $productId = $product->id;
        
        $cartItem = [
            'product_id' => $productId,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => $product->base_price,
            'total_price' => $product->base_price,
            'customization_cost' => 0,
            'customizations' => $customizations,
            'special_instructions' => $specialInstructions,
        ];

        // Calculate additional costs from customizations
        if (!empty($customizations)) {
            foreach ($customizations as $category => $optionId) {
                $option = ProductOption::find($optionId);
                if ($option) {
                    $cartItem['customization_cost'] += $option->additional_price;
                    $cartItem['total_price'] += $option->additional_price;
                }
            }
        }

        // Add to cart or update quantity if already exists
        $found = false;
        foreach ($cart as $key => $item) {
            if (
                $item['product_id'] === $productId &&
                $item['customizations'] === $cartItem['customizations'] &&
                $item['special_instructions'] === $cartItem['special_instructions']
            ) {
                $cart[$key]['quantity']++;
                $cart[$key]['total_price'] = $cart[$key]['quantity'] *
                    ($cart[$key]['unit_price'] + $cart[$key]['customization_cost']);
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = $cartItem;
        }

        $this->session->put(self::CART_SESSION_KEY, $cart);
    }

    /**
     * Update item quantity in the cart.
     */
    public function updateQuantity(int $index, int $change): void
    {
        $cart = $this->getCartItems();
        
        if (isset($cart[$index])) {
            $newQuantity = $cart[$index]['quantity'] + $change;

            if ($newQuantity <= 0) {
                $this->removeFromCart($index);
            } else {
                $cart[$index]['quantity'] = $newQuantity;
                $cart[$index]['total_price'] = $newQuantity *
                    ($cart[$index]['unit_price'] + $cart[$index]['customization_cost']);
                
                $this->session->put(self::CART_SESSION_KEY, $cart);
            }
        }
    }

    /**
     * Remove an item from the cart.
     */
    public function removeFromCart(int $index): void
    {
        $cart = $this->getCartItems();
        
        if (isset($cart[$index])) {
            array_splice($cart, $index, 1);
            $this->session->put(self::CART_SESSION_KEY, $cart);
        }
    }

    /**
     * Calculate the total amount in the cart.
     */
    public function calculateCartTotal(): float
    {
        $total = 0;
        $cart = $this->getCartItems();
        
        foreach ($cart as $item) {
            $total += $item['total_price'];
        }

        return $total;
    }
    
    /**
     * Check if a product is in the cart.
     */
    public function isProductInCart(int $productId): bool
    {
        $cart = $this->getCartItems();
        
        foreach ($cart as $item) {
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
        $cart = $this->getCartItems();
        
        foreach ($cart as $item) {
            if ($item['product_id'] === $productId) {
                $quantity += $item['quantity'];
            }
        }

        return $quantity;
    }

    /**
     * Clear the cart.
     */
    public function clearCart(): void
    {
        $this->session->forget(self::CART_SESSION_KEY);
    }
}
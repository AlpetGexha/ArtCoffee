<?php

namespace App\Actions\Order;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Livewire\Actions\Order\ProcessWalletPaymentAction;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemCustomization;
use App\Models\ProductOption;
use App\Models\User;
use App\Services\LoyaltyService;

final class PlaceOrderAction
{
    public function __construct(
        private readonly LoyaltyService $loyaltyService,
        private readonly ProcessWalletPaymentAction $processWalletPaymentAction
    ) {
    }

    /**
     * Handle the order placement process.
     */
    public function handle(
        array $cart,
        ?User $user,
        string $paymentMethod,
        bool $usePoints,
        int $pointsToRedeem,
        string $personalMessage,
        string $redemptionType
    ): Order {
        // Calculate totals
        $subtotal = $this->calculateCartTotal($cart);

        // Calculate tax (18% included in price)
        $taxRate = 0.18;
        $preTaxAmount = $subtotal / (1 + $taxRate);
        $tax = $subtotal - $preTaxAmount;

        // Calculate discount based on payment method
        $discount = 0;
        $requiredPoints = 0;

        if ($user && $usePoints) {
            $requiredPoints = $this->loyaltyService->calculatePointsEarned($subtotal);

            if ($this->loyaltyService->hasEnoughPoints($user, $subtotal)) {
                $discount = $subtotal; // Full discount if using points
            }
        } elseif ($pointsToRedeem > 0) {
            $discount = $pointsToRedeem * 0.10; // 10 cents per point if partially using points
        }

        $totalAmount = $subtotal - $discount;

        // Get the default branch
        $defaultBranch = Branch::first();

        if (!$defaultBranch) {
            throw new \Exception('No branch is available for processing orders');
        }

        // Create order
        $order = Order::create([
            'user_id' => $user?->id,
            'branch_id' => $defaultBranch->id,
            'status' => OrderStatus::PENDING,
            'payment_status' => ($paymentMethod === 'wallet' || $usePoints)
                ? PaymentStatus::PAID
                : PaymentStatus::PENDING,
            'payment_method' => $usePoints ? 'loyalty_points' : $paymentMethod,
            'subtotal' => $preTaxAmount, // Store pre-tax amount as subtotal
            'tax' => $tax,
            'discount' => $discount,
            'total_amount' => $totalAmount,
            'points_redeemed' => $usePoints ? $requiredPoints : $pointsToRedeem,
            'points_earned' => $usePoints ? 0 : (int) floor($totalAmount), // No points earned if paying with points
            'special_instructions' => $personalMessage,
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
            if (!empty($item['customizations'])) {
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
        if ($paymentMethod === 'wallet' && $user) {
            $this->processWalletPaymentAction->handle($user, $order, $totalAmount);
        }

        // Process loyalty points payment if selected
        if ($usePoints && $user) {
            $this->loyaltyService->redeemPoints($user, $requiredPoints);
        } elseif ($user && $totalAmount > 0) {
            // Add loyalty points for the purchase if not paying with points
            $this->loyaltyService->addPoints($user, (int) floor($totalAmount * 10)); // 10 points per dollar
        }

        return $order;
    }

    /**
     * Calculate the total cost of the cart.
     */
    private function calculateCartTotal(array $cart): float
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['total_price'];
        }

        return $total;
    }
}

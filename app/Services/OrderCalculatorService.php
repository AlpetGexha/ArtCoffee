<?php

namespace App\Services;

use App\Models\User;

final class OrderCalculatorService
{
    public function __construct(
        private readonly LoyaltyService $loyaltyService
    ) {
    }

    /**
     * Calculate order totals including tax, discount, and total amount.
     */
    public function calculateOrderTotals(
        float $subtotal,
        bool $usePoints = false,
        int $pointsToRedeem = 0,
        ?User $user = null
    ): array {
        // Tax calculation - 18% is already included in the price
        $taxRate = 0.18;
        $preTaxAmount = $subtotal / (1 + $taxRate);
        $tax = $subtotal - $preTaxAmount;

        // Calculate discount
        $discount = 0;
        $requiredPoints = 0;
        $hasEnoughLoyaltyPoints = false;

        if ($user && $usePoints) {
            $requiredPoints = $this->loyaltyService->calculatePointsEarned($subtotal);
            $hasEnoughLoyaltyPoints = $this->loyaltyService->hasEnoughPoints($user, $subtotal);

            if ($hasEnoughLoyaltyPoints) {
                $discount = $subtotal; // Full discount if using points for the entire order
            }
        } elseif ($pointsToRedeem > 0) {
            $discount = $pointsToRedeem * 0.10; // Assuming 10 cents per point
        }

        $totalAmount = $subtotal - $discount;

        return [
            'subtotal' => $subtotal,
            'preTaxAmount' => $preTaxAmount,
            'tax' => $tax,
            'taxRate' => $taxRate,
            'discount' => $discount,
            'totalAmount' => $totalAmount,
            'requiredPoints' => $requiredPoints,
            'hasEnoughLoyaltyPoints' => $hasEnoughLoyaltyPoints,
        ];
    }

    /**
     * Check if user has enough wallet balance for the current order.
     */
    public function validateWalletBalance(float $totalAmount, User $user): bool
    {
        return $user->balanceFloat >= $totalAmount;
    }

    /**
     * Validate if user has enough loyalty points for the current order.
     */
    public function validateLoyaltyPoints(float $subtotal, User $user): bool
    {
        return $this->loyaltyService->hasEnoughPoints($user, $subtotal);
    }
}

<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

final class LoyaltyService
{
    /**
     * The conversion rate for loyalty points (dollars to points).
     */
    private const POINTS_PER_DOLLAR = 10;

    /**
     * The conversion rate for loyalty points (points to dollars).
     */
    private const DOLLARS_PER_POINT = 0.1;

    /**
     * Check if a user has enough loyalty points.
     */
    public function hasEnoughPoints(User $user, float $amount): bool
    {
        $requiredPoints = $this->calculatePointsEarned($amount);
        return $user->loyalty_points >= $requiredPoints;
    }

    /**
     * Calculate how many loyalty points are earned for a purchase amount.
     */
    public function calculatePointsEarned(float $amount): int
    {
        return (int) round($amount * self::POINTS_PER_DOLLAR);
    }

    /**
     * Add loyalty points to a user's account.
     */
    public function addPoints(User $user, int $points): void
    {
        $user->loyalty_points += $points;
        $user->save();
    }

    /**
     * Redeem loyalty points from a user's account.
     */
    public function redeemPoints(User $user, int $points): bool
    {
        if ($user->loyalty_points < $points) {
            return false;
        }

        $user->loyalty_points -= $points;
        $user->save();

        return true;
    }

    /**
     * Calculate the dollar value of loyalty points.
     */
    public function calculatePointsValue(int $points): float
    {
        return $points * self::DOLLARS_PER_POINT;
    }

    /**
     * Format points as dollar value for display.
     */
    public function formatPointsAsDollars(int $points): string
    {
        $value = $this->calculatePointsValue($points);
        return '$' . number_format($value, 2);
    }
}

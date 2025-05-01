<?php

namespace Database\Factories;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Models\Branch;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 5, 50);
        $taxRate = 0.08; // 8% tax
        $tax = round($subtotal * $taxRate, 2);
        $discount = $this->faker->randomElement([0, 0, 0, 5, 10]); // Most orders have no discount
        $discountAmount = $discount > 0 ? round(($subtotal * $discount / 100), 2) : 0;
        $totalAmount = $subtotal + $tax - $discountAmount;
        $pointsEarned = floor($totalAmount); // 1 point per dollar spent

        $status = $this->faker->randomElement(OrderStatus::cases());
        $completed = in_array($status, [OrderStatus::COMPLETED, OrderStatus::CANCELLED]);

        return [
            'user_id' => User::factory(),
            'branch_id' => Branch::factory(),
            'table_id' => $this->faker->boolean(70) ? Table::factory() : null, // 70% of orders are dine-in
            'status' => $status,
            'payment_status' => $this->faker->randomElement(PaymentStatus::cases()),
            'payment_method' => $this->faker->randomElement(['credit_card', 'cash', 'mobile_payment', 'gift_card']),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discountAmount,
            'total_amount' => $totalAmount,
            'points_earned' => $pointsEarned,
            'points_redeemed' => $this->faker->randomElement([0, 0, 0, 50, 100, 200]), // Most orders don't redeem points
            'special_instructions' => $this->faker->boolean(30) ? $this->faker->sentence() : null, // 30% have special instructions
            'completed_at' => $completed ? $this->faker->dateTimeBetween('-1 hour', 'now') : null,
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the order is processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::PROCESSING,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the order is ready.
     */
    public function ready(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::READY,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::COMPLETED,
            'payment_status' => PaymentStatus::PAID,
            'completed_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::CANCELLED,
            'completed_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
        ]);
    }
}

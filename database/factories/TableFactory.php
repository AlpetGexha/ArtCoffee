<?php

namespace Database\Factories;

use App\Enum\TableStatus;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Table>
 */
class TableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tableNumber = $this->faker->unique()->numberBetween(1, 50);

        return [
            'branch_id' => Branch::factory(),
            'table_number' => 'T' . str_pad($tableNumber, 2, '0', STR_PAD_LEFT),
            'qr_code' => Str::uuid()->toString(),
            'seating_capacity' => $this->faker->randomElement([2, 2, 4, 4, 4, 6, 8]),
            'location' => $this->faker->randomElement(['indoor-main', 'indoor-window', 'outdoor-patio', 'outdoor-garden', null]),
            'status' => $this->faker->randomElement([
                TableStatus::AVAILABLE,
                TableStatus::AVAILABLE,
                TableStatus::AVAILABLE,
                TableStatus::OCCUPIED,
                TableStatus::RESERVED,
            ]),
        ];
    }

    /**
     * Indicate that the table is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TableStatus::AVAILABLE,
        ]);
    }

    /**
     * Indicate that the table is occupied.
     */
    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TableStatus::OCCUPIED,
        ]);
    }

    /**
     * Indicate that the table is reserved.
     */
    public function reserved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TableStatus::RESERVED,
        ]);
    }

    /**
     * Indicate that the table is under maintenance.
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TableStatus::MAINTENANCE,
        ]);
    }
}

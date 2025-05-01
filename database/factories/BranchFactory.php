<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
final class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Café',
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'country' => $this->faker->country(),
            'postal_code' => $this->faker->postcode(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'timezone' => $this->faker->randomElement(['America/New_York', 'Europe/London', 'Asia/Tokyo', 'Australia/Sydney']),
            'opening_time' => '08:00:00',
            'closing_time' => '20:00:00',
            'business_hours' => [
                'monday' => ['08:00-20:00'],
                'tuesday' => ['08:00-20:00'],
                'wednesday' => ['08:00-20:00'],
                'thursday' => ['08:00-20:00'],
                'friday' => ['08:00-22:00'],
                'saturday' => ['09:00-22:00'],
                'sunday' => ['10:00-18:00'],
            ],
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the branch is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

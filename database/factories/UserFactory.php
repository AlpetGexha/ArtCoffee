<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_admin' => false,
            'phone' => fake()->phoneNumber(),
            'date_of_birth' => fake()->dateTimeBetween('-70 years', '-18 years'),
            'loyalty_points' => fake()->numberBetween(0, 2000),
            'preferences' => $this->generateRandomPreferences(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }

    /**
     * Generate random coffee preferences.
     */
    protected function generateRandomPreferences(): array
    {
        $preferences = [];

        // Some users have favorite drinks
        if (fake()->boolean(70)) {
            $preferences['favorite_drinks'] = fake()->randomElements(
                ['Latte', 'Cappuccino', 'Espresso', 'Americano', 'Mocha', 'Earl Grey', 'Green Tea', 'Chai Latte'],
                fake()->numberBetween(1, 3)
            );
        }

        // Milk preference
        if (fake()->boolean(80)) {
            $preferences['milk'] = fake()->randomElement(
                ['Whole Milk', 'Skim Milk', 'Oat Milk', 'Almond Milk', 'Soy Milk', 'None']
            );
        }

        // Sweetener preference
        if (fake()->boolean(60)) {
            $preferences['sweetener'] = fake()->randomElement(
                ['Sugar', 'Honey', 'Stevia', 'None']
            );
        }

        // Temperature preference
        if (fake()->boolean(40)) {
            $preferences['temperature'] = fake()->randomElement(
                ['Extra Hot', 'Regular', 'Warm', 'Iced']
            );
        }

        // Dietary restrictions
        if (fake()->boolean(30)) {
            $preferences['dietary'] = fake()->randomElements(
                ['Vegetarian', 'Vegan', 'Gluten-Free', 'Dairy-Free', 'Low-Sugar', 'Low-Caffeine'],
                fake()->numberBetween(1, 2)
            );
        }

        return $preferences;
    }
}

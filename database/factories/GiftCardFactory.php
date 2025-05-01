<?php

namespace Database\Factories;

use App\Models\GiftCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GiftCard>
 */
final class GiftCardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GiftCard::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'recipient_id' => null,
            'recipient_email' => $this->faker->safeEmail(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'activation_key' => Str::random(32),
            'message' => $this->faker->optional(0.7)->sentence(),
            'occasion' => $this->faker->optional(0.8)->randomElement(['birthday', 'anniversary', 'thank you', 'holiday', 'congratulations']),
            'is_active' => true,
            'expires_at' => now()->addMonths(random_int(3, 12)),
        ];
    }

    /**
     * Indicate the gift card is redeemed.
     */
    public function redeemed(?User $recipient = null): static
    {
        return $this->state(fn (array $attributes) => [
            'redeemed_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'recipient_id' => $recipient?->id ?? User::factory(),
        ]);
    }

    /**
     * Indicate the gift card is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('-6 months', '-1 day'),
        ]);
    }

    /**
     * Indicate the gift card is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate the gift card is for a registered user rather than email.
     */
    public function forUser(): static
    {
        return $this->state(function (array $attributes) {
            $user = User::factory()->create();

            return [
                'recipient_id' => $user->id,
                'recipient_email' => $user->email,
            ];
        });
    }
}

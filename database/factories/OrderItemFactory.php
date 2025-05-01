<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();
        $quantity = $this->faker->numberBetween(1, 3);
        $unitPrice = $product->base_price;
        $customizationCost = $product->is_customizable ? $this->faker->randomFloat(2, 0, 3) : 0;
        $totalPrice = ($unitPrice + $customizationCost) * $quantity;

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'customization_cost' => $customizationCost,
            'special_instructions' => $this->faker->boolean(20) ? $this->faker->sentence() : null,
        ];
    }

    /**
     * Configure the model factory for a coffee item.
     */
    public function coffee(): static
    {
        return $this->state(function () {
            $product = Product::factory()->coffee()->create();
            $quantity = $this->faker->numberBetween(1, 3);
            $unitPrice = $product->base_price;
            $customizationCost = $this->faker->randomFloat(2, 0.50, 3);
            $totalPrice = ($unitPrice + $customizationCost) * $quantity;

            return [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'customization_cost' => $customizationCost,
                'special_instructions' => $this->faker->boolean(30) ? $this->faker->randomElement([
                    'Extra hot please',
                    'Easy on the foam',
                    'Double cup',
                    'No sleeve',
                    'Light ice',
                ]) : null,
            ];
        });
    }

    /**
     * Configure the model factory for a tea item.
     */
    public function tea(): static
    {
        return $this->state(function () {
            $product = Product::factory()->tea()->create();
            $quantity = $this->faker->numberBetween(1, 2);
            $unitPrice = $product->base_price;
            $customizationCost = $this->faker->randomFloat(2, 0, 2);
            $totalPrice = ($unitPrice + $customizationCost) * $quantity;

            return [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'customization_cost' => $customizationCost,
                'special_instructions' => $this->faker->boolean(30) ? $this->faker->randomElement([
                    'Light steep',
                    'Extra hot water',
                    'Honey on the side',
                    'No sugar',
                    'Extra tea bag',
                ]) : null,
            ];
        });
    }

    /**
     * Configure the model factory for a pastry item.
     */
    public function pastry(): static
    {
        return $this->state(function () {
            $product = Product::factory()->pastry()->create();
            $quantity = $this->faker->numberBetween(1, 4);
            $unitPrice = $product->base_price;
            $totalPrice = $unitPrice * $quantity;

            return [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'customization_cost' => 0,
                'special_instructions' => $this->faker->boolean(20) ? $this->faker->randomElement([
                    'Slightly warmed',
                    'Cut in half',
                    'Butter on the side',
                ]) : null,
            ];
        });
    }

    /**
     * Configure the model factory for a snack item.
     */
    public function snack(): static
    {
        return $this->state(function () {
            $product = Product::factory()->snack()->create();
            $quantity = $this->faker->numberBetween(1, 2);
            $unitPrice = $product->base_price;
            $totalPrice = $unitPrice * $quantity;

            return [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'customization_cost' => 0,
                'special_instructions' => $this->faker->boolean(40) ? $this->faker->randomElement([
                    'No tomatoes',
                    'Extra cheese',
                    'Light on the salt',
                    'Gluten-free if available',
                    'Dressing on the side',
                ]) : null,
            ];
        });
    }
}

<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\ProductOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItemCustomization>
 */
class OrderItemCustomizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productOption = ProductOption::factory()->create();

        return [
            'order_item_id' => OrderItem::factory(),
            'product_option_id' => $productOption->id,
            'option_price' => $productOption->additional_price,
        ];
    }

    /**
     * Configure the factory for a milk type customization.
     */
    public function milkOption(): static
    {
        return $this->state(function () {
            $productOption = ProductOption::factory()->milkOption()->create();

            return [
                'product_option_id' => $productOption->id,
                'option_price' => $productOption->additional_price,
            ];
        });
    }

    /**
     * Configure the factory for a syrup flavor customization.
     */
    public function syrupOption(): static
    {
        return $this->state(function () {
            $productOption = ProductOption::factory()->syrupOption()->create();

            return [
                'product_option_id' => $productOption->id,
                'option_price' => $productOption->additional_price,
            ];
        });
    }

    /**
     * Configure the factory for a size customization.
     */
    public function sizeOption(): static
    {
        return $this->state(function () {
            $productOption = ProductOption::factory()->sizeOption()->create();

            return [
                'product_option_id' => $productOption->id,
                'option_price' => $productOption->additional_price,
            ];
        });
    }

    /**
     * Configure the factory for an extra shot customization.
     */
    public function extraShotOption(): static
    {
        return $this->state(function () {
            $productOption = ProductOption::factory()->extraShotOption()->create();

            return [
                'product_option_id' => $productOption->id,
                'option_price' => $productOption->additional_price,
            ];
        });
    }

    /**
     * Configure the factory for a whipped cream customization.
     */
    public function whippedCreamOption(): static
    {
        return $this->state(function () {
            $productOption = ProductOption::factory()->whippedCreamOption()->create();

            return [
                'product_option_id' => $productOption->id,
                'option_price' => $productOption->additional_price,
            ];
        });
    }
}

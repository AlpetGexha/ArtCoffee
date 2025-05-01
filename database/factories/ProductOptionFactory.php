<?php

namespace Database\Factories;

use App\Enum\ProductCategory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductOption>
 */
class ProductOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Default to a milk option for coffee/tea
        $optionCategory = $this->faker->randomElement(['milk_type', 'syrup_flavor', 'size']);

        return [
            'product_id' => Product::factory()->coffee(),
            'option_category' => $optionCategory,
            'option_name' => $this->getOptionNameByCategory($optionCategory),
            'additional_price' => $this->getAdditionalPriceByCategory($optionCategory),
            'is_available' => $this->faker->boolean(95), // 95% availability
            'display_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    /**
     * Get random option name by category
     */
    protected function getOptionNameByCategory(string $category): string
    {
        return match ($category) {
            'milk_type' => $this->faker->randomElement([
                'Whole Milk',
                'Skim Milk',
                'Oat Milk',
                'Almond Milk',
                'Soy Milk',
                'Coconut Milk',
                'Lactose-Free Milk',
            ]),
            'syrup_flavor' => $this->faker->randomElement([
                'Vanilla',
                'Caramel',
                'Hazelnut',
                'Chocolate',
                'White Chocolate',
                'Peppermint',
                'Cinnamon',
                'Lavender',
                'Pumpkin Spice',
            ]),
            'size' => $this->faker->randomElement([
                'Small (8oz)',
                'Medium (12oz)',
                'Large (16oz)',
                'Extra Large (20oz)',
            ]),
            'extra_shot' => 'Extra Espresso Shot',
            'add_whipped_cream' => 'Add Whipped Cream',
            'add_drizzle' => $this->faker->randomElement([
                'Caramel Drizzle',
                'Chocolate Drizzle',
                'Honey Drizzle',
            ]),
            'temperature' => $this->faker->randomElement([
                'Extra Hot',
                'Regular Temperature',
                'Less Hot',
                'Iced',
            ]),
            default => 'Standard',
        };
    }

    /**
     * Get additional price by category
     */
    protected function getAdditionalPriceByCategory(string $category): float
    {
        return match ($category) {
            'milk_type' => $this->faker->randomElement([0.00, 0.50, 0.75, 0.00, 0.00, 1.00]),
            'syrup_flavor' => $this->faker->randomElement([0.50, 0.75, 0.50, 0.50]),
            'size' => $this->faker->randomElement([0.00, 0.50, 1.00, 1.50]),
            'extra_shot' => 1.00,
            'add_whipped_cream' => 0.50,
            'add_drizzle' => 0.75,
            'temperature' => 0.00,
            default => 0.00,
        };
    }

    /**
     * Configure the model factory for a milk option.
     */
    public function milkOption(): static
    {
        return $this->state(fn (array $attributes) => [
            'option_category' => 'milk_type',
            'option_name' => $this->getOptionNameByCategory('milk_type'),
            'additional_price' => $this->getAdditionalPriceByCategory('milk_type'),
        ]);
    }

    /**
     * Configure the model factory for a syrup flavor option.
     */
    public function syrupOption(): static
    {
        return $this->state(fn (array $attributes) => [
            'option_category' => 'syrup_flavor',
            'option_name' => $this->getOptionNameByCategory('syrup_flavor'),
            'additional_price' => $this->getAdditionalPriceByCategory('syrup_flavor'),
        ]);
    }

    /**
     * Configure the model factory for a size option.
     */
    public function sizeOption(): static
    {
        return $this->state(fn (array $attributes) => [
            'option_category' => 'size',
            'option_name' => $this->getOptionNameByCategory('size'),
            'additional_price' => $this->getAdditionalPriceByCategory('size'),
        ]);
    }

    /**
     * Configure the model factory for an extra shot option.
     */
    public function extraShotOption(): static
    {
        return $this->state(fn (array $attributes) => [
            'option_category' => 'extra_shot',
            'option_name' => 'Extra Espresso Shot',
            'additional_price' => 1.00,
        ]);
    }

    /**
     * Configure the model factory for a whipped cream option.
     */
    public function whippedCreamOption(): static
    {
        return $this->state(fn (array $attributes) => [
            'option_category' => 'add_whipped_cream',
            'option_name' => 'Add Whipped Cream',
            'additional_price' => 0.50,
        ]);
    }
}

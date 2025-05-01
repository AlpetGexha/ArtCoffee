<?php

namespace Database\Factories;

use App\Enum\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            // Coffee
            'Espresso', 'Americano', 'Cappuccino', 'Latte', 'Mocha',
            'Macchiato', 'Flat White', 'Cold Brew', 'Affogato',
            // Tea
            'Earl Grey', 'English Breakfast', 'Green Tea', 'Chai Latte',
            'Matcha Latte', 'Herbal Infusion', 'Iced Tea',
            // Pastry
            'Croissant', 'Pain au Chocolat', 'Blueberry Muffin',
            'Almond Danish', 'Cinnamon Roll', 'Banana Bread',
            // Snack
            'Avocado Toast', 'Breakfast Sandwich', 'Fruit Bowl',
            'Granola Parfait', 'Cheese Plate',
            // Merchandise
            'Coffee Beans', 'Ceramic Mug', 'Travel Tumbler', 'Pour-over Kit'
        ]);

        $category = $this->determineCategory($name);
        $price = $this->determinePriceByCategory($category);
        $isCustomizable = $this->isCustomizable($category);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->generateDescription($name, $category),
            'base_price' => $price,
            'category' => $category,
            'image_path' => "products/{$category->value}/" . Str::slug($name) . '.jpg',
            'is_customizable' => $isCustomizable,
            'is_available' => $this->faker->boolean(90), // 90% are available
            'preparation_time_minutes' => $this->determinePrepTime($category),
            'ingredients' => $this->generateIngredients($name, $category),
            'nutritional_info' => $this->generateNutritionInfo($category),
        ];
    }

    /**
     * Determine product category based on name.
     */
    protected function determineCategory(string $name): ProductCategory
    {
        $coffeeProducts = ['Espresso', 'Americano', 'Cappuccino', 'Latte', 'Mocha', 'Macchiato', 'Flat White', 'Cold Brew', 'Affogato'];
        $teaProducts = ['Earl Grey', 'English Breakfast', 'Green Tea', 'Chai Latte', 'Matcha Latte', 'Herbal Infusion', 'Iced Tea'];
        $pastryProducts = ['Croissant', 'Pain au Chocolat', 'Blueberry Muffin', 'Almond Danish', 'Cinnamon Roll', 'Banana Bread'];
        $snackProducts = ['Avocado Toast', 'Breakfast Sandwich', 'Fruit Bowl', 'Granola Parfait', 'Cheese Plate'];

        if (in_array($name, $coffeeProducts)) {
            return ProductCategory::COFFEE;
        } elseif (in_array($name, $teaProducts)) {
            return ProductCategory::TEA;
        } elseif (in_array($name, $pastryProducts)) {
            return ProductCategory::PASTRY;
        } elseif (in_array($name, $snackProducts)) {
            return ProductCategory::SNACK;
        } else {
            return ProductCategory::MERCHANDISE;
        }
    }

    /**
     * Determine if product is customizable based on category.
     */
    protected function isCustomizable(ProductCategory $category): bool
    {
        return match ($category) {
            ProductCategory::COFFEE, ProductCategory::TEA => true,
            default => false,
        };
    }

    /**
     * Determine price based on category.
     */
    protected function determinePriceByCategory(ProductCategory $category): float
    {
        return match ($category) {
            ProductCategory::COFFEE => $this->faker->randomFloat(2, 3.50, 6.00),
            ProductCategory::TEA => $this->faker->randomFloat(2, 3.00, 5.00),
            ProductCategory::PASTRY => $this->faker->randomFloat(2, 2.50, 4.50),
            ProductCategory::SNACK => $this->faker->randomFloat(2, 5.00, 9.00),
            ProductCategory::MERCHANDISE => $this->faker->randomFloat(2, 12.00, 30.00),
        };
    }

    /**
     * Determine preparation time based on category.
     */
    protected function determinePrepTime(ProductCategory $category): int
    {
        return match ($category) {
            ProductCategory::COFFEE => $this->faker->numberBetween(2, 5),
            ProductCategory::TEA => $this->faker->numberBetween(3, 6),
            ProductCategory::PASTRY, ProductCategory::MERCHANDISE => 0, // No prep time for pastries or merchandise
            ProductCategory::SNACK => $this->faker->numberBetween(5, 10),
        };
    }

    /**
     * Generate realistic description based on product name and category.
     */
    protected function generateDescription(string $name, ProductCategory $category): string
    {
        $descriptions = [
            ProductCategory::COFFEE => [
                'Espresso' => 'Our signature espresso is a rich and full-bodied shot with perfect crema.',
                'Americano' => 'Espresso diluted with hot water, creating a coffee similar to drip but with a distinctive espresso taste.',
                'Cappuccino' => 'Equal parts espresso, steamed milk, and milk foam for a perfect balance of flavors.',
                'Latte' => 'Smooth and creamy with espresso and steamed milk, topped with a light layer of foam.',
                'Mocha' => 'The perfect combination of espresso, chocolate, and steamed milk topped with whipped cream.',
                'Macchiato' => 'Espresso "stained" with a small amount of milk to cut the bitterness.',
                'Flat White' => 'Steamed milk poured over a shot of espresso creating a smooth, velvety texture.',
                'Cold Brew' => 'Coffee brewed with cold water over 24 hours for a smooth, less acidic taste.',
                'Affogato' => 'A scoop of vanilla gelato or ice cream "drowned" with a shot of hot espresso.',
            ],
            ProductCategory::TEA => [
                'Earl Grey' => 'Black tea infused with oil of bergamot for a citrusy, aromatic experience.',
                'English Breakfast' => 'A robust blend of black teas, perfect to start your day.',
                'Green Tea' => 'Delicate and fresh with subtle vegetal notes and numerous health benefits.',
                'Chai Latte' => 'Black tea infused with aromatic spices and steamed milk for a comforting treat.',
                'Matcha Latte' => 'Premium Japanese green tea powder whisked with steamed milk for a rich, earthy flavor.',
                'Herbal Infusion' => 'A soothing blend of herbs and botanicals, naturally caffeine-free.',
                'Iced Tea' => 'Refreshing tea brewed and chilled, served over ice with optional lemon.',
            ],
        ];

        if (isset($descriptions[$category][$name])) {
            return $descriptions[$category][$name];
        }

        // Generic descriptions by category if specific one not found
        return match ($category) {
            ProductCategory::COFFEE => 'A delicious coffee beverage made with our signature espresso blend.',
            ProductCategory::TEA => 'A delightful tea offering with unique character and flavor profile.',
            ProductCategory::PASTRY => 'Freshly baked daily in our kitchen using premium ingredients.',
            ProductCategory::SNACK => 'A delicious and satisfying offering prepared with fresh ingredients.',
            ProductCategory::MERCHANDISE => 'High-quality branded merchandise for coffee lovers.',
        };
    }

    /**
     * Generate ingredients list based on product.
     */
    protected function generateIngredients(string $name, ProductCategory $category): ?array
    {
        if ($category === ProductCategory::MERCHANDISE) {
            return null;
        }

        $commonIngredients = [
            ProductCategory::COFFEE => ['Espresso', 'Water'],
            ProductCategory::TEA => ['Tea Leaves', 'Water'],
            ProductCategory::PASTRY => ['Flour', 'Butter', 'Sugar'],
            ProductCategory::SNACK => ['Bread', 'Eggs', 'Salt'],
        ];

        $specificIngredients = [
            'Latte' => ['Espresso', 'Steamed Milk', 'Milk Foam'],
            'Cappuccino' => ['Espresso', 'Steamed Milk', 'Milk Foam'],
            'Mocha' => ['Espresso', 'Chocolate Syrup', 'Steamed Milk', 'Whipped Cream'],
            'Chai Latte' => ['Black Tea', 'Spices', 'Steamed Milk', 'Honey'],
            'Croissant' => ['Flour', 'Butter', 'Yeast', 'Sugar', 'Salt'],
            'Pain au Chocolat' => ['Flour', 'Butter', 'Yeast', 'Sugar', 'Chocolate'],
            'Avocado Toast' => ['Sourdough Bread', 'Avocado', 'Lemon Juice', 'Salt', 'Red Pepper Flakes'],
        ];

        if (isset($specificIngredients[$name])) {
            return $specificIngredients[$name];
        }

        // Return common ingredients for category with 1-3 random additions
        $ingredients = $commonIngredients[$category] ?? [];
        $additionalIngredients = ['Vanilla', 'Cinnamon', 'Nutmeg', 'Honey', 'Maple Syrup', 'Caramel', 'Cream', 'Milk', 'Almond Milk'];

        $additionalCount = $this->faker->numberBetween(0, 2);
        for ($i = 0; $i < $additionalCount; $i++) {
            $ingredients[] = $this->faker->randomElement($additionalIngredients);
        }

        return array_unique($ingredients);
    }

    /**
     * Generate nutrition info based on category.
     */
    protected function generateNutritionInfo(ProductCategory $category): ?array
    {
        if ($category === ProductCategory::MERCHANDISE) {
            return null;
        }

        $baseNutrition = [
            'calories' => 0,
            'protein' => 0,
            'fat' => 0,
            'carbs' => 0,
            'sugar' => 0,
            'serving_size' => '',
        ];

        return match ($category) {
            ProductCategory::COFFEE => array_merge($baseNutrition, [
                'calories' => $this->faker->numberBetween(5, 220),
                'protein' => $this->faker->numberBetween(0, 8),
                'fat' => $this->faker->numberBetween(0, 12),
                'carbs' => $this->faker->numberBetween(0, 24),
                'sugar' => $this->faker->numberBetween(0, 20),
                'serving_size' => '8oz (236ml)',
            ]),
            ProductCategory::TEA => array_merge($baseNutrition, [
                'calories' => $this->faker->numberBetween(0, 150),
                'protein' => $this->faker->numberBetween(0, 4),
                'fat' => $this->faker->numberBetween(0, 6),
                'carbs' => $this->faker->numberBetween(0, 20),
                'sugar' => $this->faker->numberBetween(0, 18),
                'serving_size' => '8oz (236ml)',
            ]),
            ProductCategory::PASTRY => array_merge($baseNutrition, [
                'calories' => $this->faker->numberBetween(250, 450),
                'protein' => $this->faker->numberBetween(5, 8),
                'fat' => $this->faker->numberBetween(12, 25),
                'carbs' => $this->faker->numberBetween(30, 50),
                'sugar' => $this->faker->numberBetween(8, 25),
                'serving_size' => '1 piece',
            ]),
            ProductCategory::SNACK => array_merge($baseNutrition, [
                'calories' => $this->faker->numberBetween(200, 600),
                'protein' => $this->faker->numberBetween(8, 20),
                'fat' => $this->faker->numberBetween(10, 30),
                'carbs' => $this->faker->numberBetween(20, 60),
                'sugar' => $this->faker->numberBetween(2, 15),
                'serving_size' => '1 portion',
            ]),
        };
    }

    /**
     * Indicate that the product is a coffee item.
     */
    public function coffee(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => ProductCategory::COFFEE,
            'is_customizable' => true,
        ]);
    }

    /**
     * Indicate that the product is a tea item.
     */
    public function tea(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => ProductCategory::TEA,
            'is_customizable' => true,
        ]);
    }

    /**
     * Indicate that the product is a pastry item.
     */
    public function pastry(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => ProductCategory::PASTRY,
            'is_customizable' => false,
        ]);
    }

    /**
     * Indicate that the product is a snack item.
     */
    public function snack(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => ProductCategory::SNACK,
            'is_customizable' => false,
        ]);
    }

    /**
     * Indicate that the product is a merchandise item.
     */
    public function merchandise(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => ProductCategory::MERCHANDISE,
            'is_customizable' => false,
            'ingredients' => null,
            'nutritional_info' => null,
        ]);
    }
}

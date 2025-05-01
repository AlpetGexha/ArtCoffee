<?php

namespace Database\Seeders;

use App\Enum\ProductCategory;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define products by category for more controlled seeding
        $this->seedCoffeeProducts();
        $this->seedTeaProducts();
        $this->seedPastryProducts();
        $this->seedSnackProducts();
        $this->seedMerchandiseProducts();
    }

    /**
     * Seed coffee products with their options
     */
    private function seedCoffeeProducts(): void
    {
        $coffees = [
            [
                'name' => 'Espresso',
                'description' => 'Our signature espresso is a rich and full-bodied shot with perfect crema.',
                'base_price' => 3.50,
                'preparation_time_minutes' => 2,
            ],
            [
                'name' => 'Americano',
                'description' => 'Espresso diluted with hot water, creating a coffee similar to drip but with a distinctive espresso taste.',
                'base_price' => 4.00,
                'preparation_time_minutes' => 3,
            ],
            [
                'name' => 'Cappuccino',
                'description' => 'Equal parts espresso, steamed milk, and milk foam for a perfect balance of flavors.',
                'base_price' => 4.75,
                'preparation_time_minutes' => 4,
            ],
            [
                'name' => 'Latte',
                'description' => 'Smooth and creamy with espresso and steamed milk, topped with a light layer of foam.',
                'base_price' => 4.50,
                'preparation_time_minutes' => 4,
            ],
            [
                'name' => 'Mocha',
                'description' => 'The perfect combination of espresso, chocolate, and steamed milk topped with whipped cream.',
                'base_price' => 5.25,
                'preparation_time_minutes' => 5,
            ],
        ];

        foreach ($coffees as $coffee) {
            $product = Product::create([
                'name' => $coffee['name'],
                'slug' => Str::slug($coffee['name']),
                'description' => $coffee['description'],
                'base_price' => $coffee['base_price'],
                'category' => ProductCategory::COFFEE,
                'is_customizable' => true,
                'is_available' => true,
                'preparation_time_minutes' => $coffee['preparation_time_minutes'],
                'ingredients' => json_encode(['Espresso', 'Water', 'Milk']),
                'nutritional_info' => json_encode([
                    'calories' => rand(80, 180),
                    'protein' => rand(3, 8),
                    'fat' => rand(2, 7),
                    'carbs' => rand(10, 20),
                    'sugar' => rand(5, 15),
                    'serving_size' => '8oz (236ml)',
                ]),
            ]);

            // Add customization options
            $this->addStandardCoffeeOptions($product);
        }
    }

    /**
     * Seed tea products with their options
     */
    private function seedTeaProducts(): void
    {
        $teas = [
            [
                'name' => 'Earl Grey',
                'description' => 'Black tea infused with oil of bergamot for a citrusy, aromatic experience.',
                'base_price' => 3.75,
            ],
            [
                'name' => 'English Breakfast',
                'description' => 'A robust blend of black teas, perfect to start your day.',
                'base_price' => 3.50,
            ],
            [
                'name' => 'Green Tea',
                'description' => 'Delicate and fresh with subtle vegetal notes and numerous health benefits.',
                'base_price' => 3.75,
            ],
            [
                'name' => 'Chai Latte',
                'description' => 'Black tea infused with aromatic spices and steamed milk for a comforting treat.',
                'base_price' => 4.50,
            ],
            [
                'name' => 'Matcha Latte',
                'description' => 'Premium Japanese green tea powder whisked with steamed milk for a rich, earthy flavor.',
                'base_price' => 5.00,
            ],
        ];

        foreach ($teas as $tea) {
            $product = Product::create([
                'name' => $tea['name'],
                'slug' => Str::slug($tea['name']),
                'description' => $tea['description'],
                'base_price' => $tea['base_price'],
                'category' => ProductCategory::TEA,
                'is_customizable' => true,
                'is_available' => true,
                'preparation_time_minutes' => rand(3, 5),
                'ingredients' => json_encode(['Tea Leaves', 'Water']),
                'nutritional_info' => json_encode([
                    'calories' => rand(0, 120),
                    'protein' => rand(0, 4),
                    'fat' => rand(0, 4),
                    'carbs' => rand(0, 20),
                    'sugar' => rand(0, 15),
                    'serving_size' => '8oz (236ml)',
                ]),
            ]);

            // Add customization options
            $this->addStandardTeaOptions($product);
        }
    }

    /**
     * Seed pastry products
     */
    private function seedPastryProducts(): void
    {
        $pastries = [
            [
                'name' => 'Croissant',
                'description' => 'Buttery and flaky pastry with a golden crust.',
                'base_price' => 3.25,
            ],
            [
                'name' => 'Pain au Chocolat',
                'description' => 'Flaky pastry filled with rich chocolate.',
                'base_price' => 3.75,
            ],
            [
                'name' => 'Blueberry Muffin',
                'description' => 'Moist muffin packed with juicy blueberries.',
                'base_price' => 3.50,
            ],
            [
                'name' => 'Cinnamon Roll',
                'description' => 'Warm, spiral pastry with cinnamon filling and vanilla glaze.',
                'base_price' => 4.25,
            ],
        ];

        foreach ($pastries as $pastry) {
            Product::create([
                'name' => $pastry['name'],
                'slug' => Str::slug($pastry['name']),
                'description' => $pastry['description'],
                'base_price' => $pastry['base_price'],
                'category' => ProductCategory::PASTRY,
                // '_path' => 'products/pastry/' . Str::slug($pastry['name']) . '.jpg',
                'is_customizable' => false,
                'is_available' => true,
                'preparation_time_minutes' => 0,
                'ingredients' => json_encode(['Flour', 'Butter', 'Sugar', 'Salt']),
                'nutritional_info' => json_encode([
                    'calories' => rand(250, 450),
                    'protein' => rand(3, 6),
                    'fat' => rand(12, 25),
                    'carbs' => rand(30, 50),
                    'sugar' => rand(10, 25),
                    'serving_size' => '1 piece',
                ]),
            ]);
        }
    }

    /**
     * Seed snack products
     */
    private function seedSnackProducts(): void
    {
        $snacks = [
            [
                'name' => 'Avocado Toast',
                'description' => 'Sourdough bread topped with mashed avocado, sea salt, and red pepper flakes.',
                'base_price' => 7.50,
            ],
            [
                'name' => 'Breakfast Sandwich',
                'description' => 'Egg, cheddar cheese, and choice of bacon or sausage on a toasted brioche bun.',
                'base_price' => 6.75,
            ],
            [
                'name' => 'Fruit Bowl',
                'description' => 'A refreshing mix of seasonal fruits.',
                'base_price' => 5.50,
            ],
        ];

        foreach ($snacks as $snack) {
            Product::create([
                'name' => $snack['name'],
                'slug' => Str::slug($snack['name']),
                'description' => $snack['description'],
                'base_price' => $snack['base_price'],
                'category' => ProductCategory::SNACK,
                'is_customizable' => false,
                'is_available' => true,
                'preparation_time_minutes' => rand(5, 10),
                'ingredients' => json_encode(['Bread', 'Eggs', 'Salt', 'Vegetables']),
                'nutritional_info' => json_encode([
                    'calories' => rand(200, 500),
                    'protein' => rand(8, 15),
                    'fat' => rand(10, 20),
                    'carbs' => rand(20, 40),
                    'sugar' => rand(2, 10),
                    'serving_size' => '1 portion',
                ]),
            ]);
        }
    }

    /**
     * Seed merchandise products
     */
    private function seedMerchandiseProducts(): void
    {
        $merchandise = [
            [
                'name' => 'Coffee Beans',
                'description' => 'Our signature blend available to enjoy at home. 12oz bag.',
                'base_price' => 15.00,
            ],
            [
                'name' => 'Ceramic Mug',
                'description' => 'Handcrafted ceramic mug with our logo. Dishwasher safe.',
                'base_price' => 12.50,
            ],
            [
                'name' => 'Travel Tumbler',
                'description' => 'Stainless steel tumbler that keeps your drink hot for hours.',
                'base_price' => 22.00,
            ],
        ];

        foreach ($merchandise as $item) {
            Product::create([
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'base_price' => $item['base_price'],
                'category' => ProductCategory::MERCHANDISE,
                'is_customizable' => false,
                'is_available' => true,
                'preparation_time_minutes' => 0,
                'ingredients' => null,
                'nutritional_info' => null,
            ]);
        }
    }

    /**
     * Add standard coffee customization options to a product
     */
    private function addStandardCoffeeOptions(Product $product): void
    {
        // Milk options
        $milkOptions = [
            ['option_name' => 'Whole Milk', 'additional_price' => 0.00],
            ['option_name' => 'Skim Milk', 'additional_price' => 0.00],
            ['option_name' => 'Oat Milk', 'additional_price' => 0.75],
            ['option_name' => 'Almond Milk', 'additional_price' => 0.75],
            ['option_name' => 'Soy Milk', 'additional_price' => 0.50],
        ];

        $displayOrder = 1;
        foreach ($milkOptions as $option) {
            ProductOption::create([
                'product_id' => $product->id,
                'option_category' => 'milk_type',
                'option_name' => $option['option_name'],
                'additional_price' => $option['additional_price'],
                'is_available' => true,
                'display_order' => $displayOrder++,
            ]);
        }

        // Syrup options
        $syrupOptions = [
            ['option_name' => 'Vanilla', 'additional_price' => 0.50],
            ['option_name' => 'Caramel', 'additional_price' => 0.50],
            ['option_name' => 'Hazelnut', 'additional_price' => 0.50],
            ['option_name' => 'Chocolate', 'additional_price' => 0.50],
        ];

        $displayOrder = 1;
        foreach ($syrupOptions as $option) {
            ProductOption::create([
                'product_id' => $product->id,
                'option_category' => 'syrup_flavor',
                'option_name' => $option['option_name'],
                'additional_price' => $option['additional_price'],
                'is_available' => true,
                'display_order' => $displayOrder++,
            ]);
        }

        // Size options
        $sizeOptions = [
            ['option_name' => 'Small (8oz)', 'additional_price' => 0.00],
            ['option_name' => 'Medium (12oz)', 'additional_price' => 0.50],
            ['option_name' => 'Large (16oz)', 'additional_price' => 1.00],
        ];

        $displayOrder = 1;
        foreach ($sizeOptions as $option) {
            ProductOption::create([
                'product_id' => $product->id,
                'option_category' => 'size',
                'option_name' => $option['option_name'],
                'additional_price' => $option['additional_price'],
                'is_available' => true,
                'display_order' => $displayOrder++,
            ]);
        }

        // Extra options
        ProductOption::create([
            'product_id' => $product->id,
            'option_category' => 'extra_shot',
            'option_name' => 'Extra Espresso Shot',
            'additional_price' => 1.00,
            'is_available' => true,
            'display_order' => 1,
        ]);

        ProductOption::create([
            'product_id' => $product->id,
            'option_category' => 'add_whipped_cream',
            'option_name' => 'Add Whipped Cream',
            'additional_price' => 0.50,
            'is_available' => true,
            'display_order' => 1,
        ]);
    }

    /**
     * Add standard tea customization options to a product
     */
    private function addStandardTeaOptions(Product $product): void
    {
        // Milk options for tea (only relevant for some teas)
        if (in_array($product->name, ['Chai Latte', 'Matcha Latte'])) {
            $milkOptions = [
                ['option_name' => 'Whole Milk', 'additional_price' => 0.00],
                ['option_name' => 'Skim Milk', 'additional_price' => 0.00],
                ['option_name' => 'Oat Milk', 'additional_price' => 0.75],
                ['option_name' => 'Almond Milk', 'additional_price' => 0.75],
                ['option_name' => 'Soy Milk', 'additional_price' => 0.50],
            ];

            $displayOrder = 1;
            foreach ($milkOptions as $option) {
                ProductOption::create([
                    'product_id' => $product->id,
                    'option_category' => 'milk_type',
                    'option_name' => $option['option_name'],
                    'additional_price' => $option['additional_price'],
                    'is_available' => true,
                    'display_order' => $displayOrder++,
                ]);
            }
        }

        // Sweetener options
        $sweetenerOptions = [
            ['option_name' => 'Honey', 'additional_price' => 0.25],
            ['option_name' => 'Sugar', 'additional_price' => 0.00],
            ['option_name' => 'Stevia', 'additional_price' => 0.00],
        ];

        $displayOrder = 1;
        foreach ($sweetenerOptions as $option) {
            ProductOption::create([
                'product_id' => $product->id,
                'option_category' => 'sweetener',
                'option_name' => $option['option_name'],
                'additional_price' => $option['additional_price'],
                'is_available' => true,
                'display_order' => $displayOrder++,
            ]);
        }

        // Size options
        $sizeOptions = [
            ['option_name' => 'Small (8oz)', 'additional_price' => 0.00],
            ['option_name' => 'Medium (12oz)', 'additional_price' => 0.50],
            ['option_name' => 'Large (16oz)', 'additional_price' => 1.00],
        ];

        $displayOrder = 1;
        foreach ($sizeOptions as $option) {
            ProductOption::create([
                'product_id' => $product->id,
                'option_category' => 'size',
                'option_name' => $option['option_name'],
                'additional_price' => $option['additional_price'],
                'is_available' => true,
                'display_order' => $displayOrder++,
            ]);
        }
    }
}

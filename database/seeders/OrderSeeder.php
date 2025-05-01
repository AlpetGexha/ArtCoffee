<?php

namespace Database\Seeders;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Enum\ProductCategory;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemCustomization;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pre-load data to avoid multiple queries in the loop
        $users = User::all();
        $branches = Branch::with('tables')->get();
        $products = Product::all();
        $productsByCategory = $products->groupBy('category');

        // Generate orders for the last 30 days
        for ($day = 0; $day < 30; $day++) {
            $date = Carbon::today()->subDays($day);
            $orderCount = $this->getOrderCountForDate($date);

            $this->createOrdersForDate($date, $orderCount, $users, $branches, $productsByCategory);
        }
    }

    /**
     * Create orders for a specific date
     */
    private function createOrdersForDate(Carbon $date, int $orderCount, $users, $branches, $productsByCategory): void
    {
        for ($i = 0; $i < $orderCount; $i++) {
            // Select random user and branch
            $user = $users->random();
            $branch = $branches->random();
            $table = mt_rand(0, 100) < 70 ? $branch->tables->random() : null; // 70% chance of being dine-in

            // Determine time based on branch hours - handling edge cases
            $openingHour = intval(substr($branch->opening_time, 0, 2));
            $closingHour = intval(substr($branch->closing_time, 0, 2));

            // Ensure valid range for mt_rand by setting a minimum gap of 1
            $orderHour = ($openingHour >= $closingHour)
                ? $openingHour
                : mt_rand($openingHour, max($openingHour, $closingHour - 1));
            $orderMinute = mt_rand(0, 59);

            $orderDate = Carbon::parse($date)->setHour($orderHour)->setMinute($orderMinute);

            // Order status biased toward completion for older orders
            $dayAgo = Carbon::today()->diffInDays($date);

            $status = $this->getOrderStatusBasedOnAge($dayAgo);
            $paymentStatus = ($status === OrderStatus::PENDING) ? PaymentStatus::PENDING : PaymentStatus::PAID;

            // Generate order items
            $itemCount = mt_rand(1, 4);
            $orderItems = $this->generateOrderItems($productsByCategory, $itemCount);

            $subtotal = $orderItems->sum('price_sum');
            $taxRate = 0.08; // 8% tax
            $tax = round($subtotal * $taxRate, 2);

            // Calculate discount based on loyalty tier (simplified)
            $discount = $this->calculateDiscount($user, $subtotal);
            $discountAmount = round($discount * $subtotal / 100, 2);
            $totalAmount = $subtotal + $tax - $discountAmount;

            // Estimate points (1 per dollar)
            $pointsEarned = floor($totalAmount);
            $pointsRedeemed = mt_rand(0, 100) < 15 ? mt_rand(1, 5) * 50 : 0; // 15% of orders redeem points

            // Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'table_id' => $table?->id,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $this->getRandomPaymentMethod(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discountAmount,
                'total_amount' => $totalAmount,
                'points_earned' => $pointsEarned,
                'points_redeemed' => $pointsRedeemed,
                'special_instructions' => mt_rand(0, 100) < 30 ? $this->getRandomSpecialInstruction() : null,
                'completed_at' => in_array($status, [OrderStatus::COMPLETED, OrderStatus::CANCELLED])
                    ? $orderDate->copy()->addMinutes(mt_rand(15, 45))
                    : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Create order items with their customizations
            foreach ($orderItems as $item) {
                $product = Product::find($item['product_id']);
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->base_price,
                    'customization_cost' => $item['customization_cost'],
                    'total_price' => $item['price_sum'],
                    'special_instructions' => $item['special_instructions'],
                ]);

                // Create customizations if applicable
                if ($product->is_customizable && !empty($item['options'])) {
                    foreach ($item['options'] as $optionId) {
                        $option = ProductOption::find($optionId);
                        if ($option) {
                            OrderItemCustomization::create([
                                'order_item_id' => $orderItem->id,
                                'product_option_id' => $option->id,
                                'option_price' => $option->additional_price,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Generate order items for an order
     */
    private function generateOrderItems($productsByCategory, int $itemCount): \Illuminate\Support\Collection
    {
        $orderItems = collect();

        // People often order 1 drink per person, and sometimes food
        $remainingItems = $itemCount;

        // Always include at least one drink if available
        if ($remainingItems > 0 &&
            ($productsByCategory->has(ProductCategory::COFFEE->value) ||
             $productsByCategory->has(ProductCategory::TEA->value))) {

            // Randomly select coffee or tea
            $drinkCategory = mt_rand(0, 100) < 70 ?
                ProductCategory::COFFEE->value :
                ProductCategory::TEA->value;

            // Fall back to the other category if the chosen one doesn't exist
            if (!$productsByCategory->has($drinkCategory)) {
                $drinkCategory = ($drinkCategory === ProductCategory::COFFEE->value) ?
                    ProductCategory::TEA->value :
                    ProductCategory::COFFEE->value;
            }

            // If we still don't have products, skip
            if ($productsByCategory->has($drinkCategory)) {
                $product = $productsByCategory[$drinkCategory]->random();

                $orderItems->push($this->createOrderItem($product));
                $remainingItems--;
            }
        }

        // Add remaining random items
        $categories = collect(ProductCategory::cases())->map->value;

        for ($i = 0; $i < $remainingItems; $i++) {
            // Randomly select a category
            $category = $categories->random();

            // Skip if no products in this category
            if (!$productsByCategory->has($category)) {
                continue;
            }

            $product = $productsByCategory[$category]->random();
            $orderItems->push($this->createOrderItem($product));
        }

        return $orderItems;
    }

    /**
     * Create a single order item
     */
    private function createOrderItem(Product $product): array
    {
        $quantity = mt_rand(1, 3);
        $customizationCost = 0;
        $options = [];
        $specialInstructions = null;

        // Add customizations if applicable
        if ($product->is_customizable) {
            // Get options for this product
            $productOptions = ProductOption::where('product_id', $product->id)->get();

            if ($productOptions->isNotEmpty()) {
                // Group options by category
                $optionsByCategory = $productOptions->groupBy('option_category');

                // For each category, randomly select an option
                foreach ($optionsByCategory as $category => $categoryOptions) {
                    // 60-90% chance to add customization depending on category
                    $addChance = match ($category) {
                        'milk_type' => 90,
                        'size' => 80,
                        'syrup_flavor' => 60,
                        'extra_shot' => 40,
                        'add_whipped_cream' => 50,
                        'sweetener' => 70,
                        default => 50,
                    };

                    if (mt_rand(1, 100) <= $addChance) {
                        $option = $categoryOptions->random();
                        $options[] = $option->id;
                        $customizationCost += $option->additional_price;
                    }
                }

                // Add special instructions with 30% probability for customizable items
                if (mt_rand(1, 100) <= 30) {
                    $specialInstructions = $this->getCustomizationInstructions($product);
                }
            }
        } else {
            // Add special instructions with 15% probability for non-customizable items
            if (mt_rand(1, 100) <= 15) {
                $specialInstructions = $this->getNonCustomizableInstructions($product);
            }
        }

        $unitTotal = $product->base_price + $customizationCost;
        $priceSum = $unitTotal * $quantity;

        return [
            'product_id' => $product->id,
            'quantity' => $quantity,
            'customization_cost' => $customizationCost,
            'price_sum' => $priceSum,
            'options' => $options,
            'special_instructions' => $specialInstructions,
        ];
    }

    /**
     * Determine order count based on date and day of week
     */
    private function getOrderCountForDate(Carbon $date): int
    {
        $dayOfWeek = $date->dayOfWeek;
        $isWeekend = in_array($dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);

        $baseCount = $isWeekend ? 35 : 25; // Busier on weekends

        // Adjust based on how long ago (more recent days might not be complete)
        $daysAgo = Carbon::today()->diffInDays($date);

        if ($daysAgo < 1) {
            // Today - fewer orders because day isn't complete
            $baseCount = (int)($baseCount * (Carbon::now()->hour / 24));
        } else if ($daysAgo < 2) {
            // Yesterday - slightly fewer orders for randomization
            $baseCount = (int)($baseCount * 0.9);
        }

        // Add randomness of +/- 20%
        $variance = mt_rand(-20, 20) / 100;
        return max(5, intval($baseCount * (1 + $variance)));
    }

    /**
     * Determine order status based on how old the order is
     */
    private function getOrderStatusBasedOnAge(int $daysAgo): OrderStatus
    {
        if ($daysAgo === 0) {
            // Today's orders are more likely to be in progress
            $rand = mt_rand(1, 100);
            if ($rand <= 30) return OrderStatus::PENDING;
            if ($rand <= 60) return OrderStatus::PROCESSING;
            if ($rand <= 80) return OrderStatus::READY;
            return OrderStatus::COMPLETED;
        } else if ($daysAgo === 1) {
            // Yesterday's orders are mostly completed
            $rand = mt_rand(1, 100);
            if ($rand <= 5) return OrderStatus::PROCESSING;
            if ($rand <= 15) return OrderStatus::READY;
            if ($rand <= 95) return OrderStatus::COMPLETED;
            return OrderStatus::CANCELLED;
        } else {
            // Older orders are completed or cancelled
            return mt_rand(1, 100) <= 95 ? OrderStatus::COMPLETED : OrderStatus::CANCELLED;
        }
    }

    /**
     * Get a random payment method
     */
    private function getRandomPaymentMethod(): string
    {
        return Arr::random([
            'credit_card',
            'credit_card',
            'credit_card', // Weighted for more credit card payments
            'cash',
            'cash',
            'mobile_payment',
            'mobile_payment',
            'gift_card',
        ]);
    }

    /**
     * Calculate discount percentage based on user loyalty
     */
    private function calculateDiscount(User $user, float $subtotal): float
    {
        // Simple random discount logic
        // In a real application, this would be based on user's loyalty tier
        if (mt_rand(1, 100) <= 10) { // 10% chance for discount
            return Arr::random([5, 10, 15]);
        }

        return 0;
    }

    /**
     * Get random special instruction for an order
     */
    private function getRandomSpecialInstruction(): string
    {
        return Arr::random([
            'Please deliver to the table quickly.',
            'Extra napkins please!',
            'This is a birthday celebration.',
            'We\'re in a rush, thank you!',
            'Need allergen information.',
            'Can you bring everything together?',
        ]);
    }

    /**
     * Get special instructions for customizable products
     */
    private function getCustomizationInstructions(Product $product): string
    {
        if ($product->category === ProductCategory::COFFEE) {
            return Arr::random([
                'Extra hot please.',
                'Light ice.',
                'Double cup.',
                'No sleeve.',
                'Easy on the foam.',
                'Extra foam please.',
                'Very little sugar.',
            ]);
        } elseif ($product->category === ProductCategory::TEA) {
            return Arr::random([
                'Light steep please.',
                'Extra hot water on the side.',
                'Honey on the side.',
                'No sugar.',
                'Extra tea bag.',
                'Light ice.',
            ]);
        }

        return 'Please prepare with care.';
    }

    /**
     * Get special instructions for non-customizable products
     */
    private function getNonCustomizableInstructions(Product $product): string
    {
        if ($product->category === ProductCategory::PASTRY) {
            return Arr::random([
                'Warmed please.',
                'Cut in half.',
                'Butter on the side.',
            ]);
        } elseif ($product->category === ProductCategory::SNACK) {
            return Arr::random([
                'No tomatoes please.',
                'Extra sauce on the side.',
                'Lightly toasted.',
                'No salt please.',
            ]);
        }

        return 'Please serve fresh.';
    }
}

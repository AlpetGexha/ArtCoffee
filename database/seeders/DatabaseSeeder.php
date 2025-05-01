<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create basic users first (required for orders)
        $this->call(UserSeeder::class);

        // Seed branches and their tables
        $this->call(BranchSeeder::class);

        // Seed products and their customization options
        $this->call(ProductSeeder::class);

        // Finally, seed orders with their items
        $this->call(OrderSeeder::class);
    }
}

<?php

namespace Database\Seeders;

use App\Models\GiftCard;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BranchSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);

        GiftCard::factory(20)->create();
    }
}

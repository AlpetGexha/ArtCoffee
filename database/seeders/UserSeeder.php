<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@coffeeart.com',
            'is_admin' => true,
        ]);

        // Create staff user
        $staff = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@coffeeart.com',
            'is_admin' => false,
        ]);

        $user = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'test@example.com',
            'is_admin' => true,
        ]);

        $user->deposit(1000);

        // Create regular customers
        User::factory(30)->create();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Table;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'CoffeeArt Downtown',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10001',
                'phone' => '212-555-1234',
                'email' => 'downtown@coffeeart.com',
                'timezone' => 'America/New_York',
                'opening_time' => '07:00:00',
                'closing_time' => '21:00:00',
                'business_hours' => [
                    'monday' => ['07:00-21:00'],
                    'tuesday' => ['07:00-21:00'],
                    'wednesday' => ['07:00-21:00'],
                    'thursday' => ['07:00-21:00'],
                    'friday' => ['07:00-23:00'],
                    'saturday' => ['08:00-23:00'],
                    'sunday' => ['09:00-20:00'],
                ],
                'tables' => [
                    ['table_number' => 'T01', 'seating_capacity' => 2, 'location' => 'window'],
                    ['table_number' => 'T02', 'seating_capacity' => 2, 'location' => 'window'],
                    ['table_number' => 'T03', 'seating_capacity' => 4, 'location' => 'center'],
                    ['table_number' => 'T04', 'seating_capacity' => 4, 'location' => 'center'],
                    ['table_number' => 'T05', 'seating_capacity' => 6, 'location' => 'corner'],
                    ['table_number' => 'T06', 'seating_capacity' => 2, 'location' => 'outdoor'],
                    ['table_number' => 'T07', 'seating_capacity' => 2, 'location' => 'outdoor'],
                    ['table_number' => 'T08', 'seating_capacity' => 4, 'location' => 'outdoor'],
                ]
            ],
            [
                'name' => 'CoffeeArt Midtown',
                'address' => '456 Park Avenue',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10022',
                'phone' => '212-555-5678',
                'email' => 'midtown@coffeeart.com',
                'timezone' => 'America/New_York',
                'opening_time' => '06:00:00',
                'closing_time' => '20:00:00',
                'business_hours' => [
                    'monday' => ['06:00-20:00'],
                    'tuesday' => ['06:00-20:00'],
                    'wednesday' => ['06:00-20:00'],
                    'thursday' => ['06:00-20:00'],
                    'friday' => ['06:00-20:00'],
                    'saturday' => ['07:00-19:00'],
                    'sunday' => ['08:00-18:00'],
                ],
                'tables' => [
                    ['table_number' => 'T01', 'seating_capacity' => 2, 'location' => 'window'],
                    ['table_number' => 'T02', 'seating_capacity' => 4, 'location' => 'window'],
                    ['table_number' => 'T03', 'seating_capacity' => 4, 'location' => 'center'],
                    ['table_number' => 'T04', 'seating_capacity' => 8, 'location' => 'corner'],
                    ['table_number' => 'T05', 'seating_capacity' => 2, 'location' => 'bar'],
                    ['table_number' => 'T06', 'seating_capacity' => 2, 'location' => 'bar'],
                ]
            ],
            [
                'name' => 'CoffeeArt Village',
                'address' => '789 Greenwich Ave',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => '10014',
                'phone' => '212-555-9012',
                'email' => 'village@coffeeart.com',
                'timezone' => 'America/New_York',
                'opening_time' => '07:30:00',
                'closing_time' => '22:00:00',
                'business_hours' => [
                    'monday' => ['07:30-22:00'],
                    'tuesday' => ['07:30-22:00'],
                    'wednesday' => ['07:30-22:00'],
                    'thursday' => ['07:30-22:00'],
                    'friday' => ['07:30-00:00'],
                    'saturday' => ['08:30-00:00'],
                    'sunday' => ['08:30-22:00'],
                ],
                'tables' => [
                    ['table_number' => 'T01', 'seating_capacity' => 2, 'location' => 'window'],
                    ['table_number' => 'T02', 'seating_capacity' => 2, 'location' => 'window'],
                    ['table_number' => 'T03', 'seating_capacity' => 2, 'location' => 'couch'],
                    ['table_number' => 'T04', 'seating_capacity' => 4, 'location' => 'center'],
                    ['table_number' => 'T05', 'seating_capacity' => 4, 'location' => 'center'],
                    ['table_number' => 'T06', 'seating_capacity' => 6, 'location' => 'garden'],
                    ['table_number' => 'T07', 'seating_capacity' => 2, 'location' => 'garden'],
                    ['table_number' => 'T08', 'seating_capacity' => 2, 'location' => 'garden'],
                    ['table_number' => 'T09', 'seating_capacity' => 4, 'location' => 'garden'],
                ]
            ],
        ];

        foreach ($branches as $branchData) {
            $tableData = $branchData['tables'];
            unset($branchData['tables']);

            $branch = Branch::create($branchData);

            // Create tables for this branch
            foreach ($tableData as $table) {
                Table::create([
                    'branch_id' => $branch->id,
                    'table_number' => $table['table_number'],
                    'qr_code' => sprintf('qr-%s-%s', $branch->id, $table['table_number']),
                    'seating_capacity' => $table['seating_capacity'],
                    'location' => $table['location'],
                    'status' => \App\Enum\TableStatus::AVAILABLE,
                ]);
            }
        }
    }
}

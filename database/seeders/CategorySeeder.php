<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electrical',
                'description' => 'Electrical issues and repairs',
                'icon' => 'fa-bolt',
                'color' => '#e74c3c',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Internet',
                'description' => 'WiFi and network issues',
                'icon' => 'fa-wifi',
                'color' => '#3498db',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maintenance',
                'description' => 'General maintenance requests',
                'icon' => 'fa-wrench',
                'color' => '#f39c12',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plumbing',
                'description' => 'Plumbing and water issues',
                'icon' => 'fa-faucet',
                'color' => '#1abc9c',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cleaning',
                'description' => 'Cleaning and sanitation',
                'icon' => 'fa-broom',
                'color' => '#9b59b6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Security',
                'description' => 'Security concerns',
                'icon' => 'fa-shield-alt',
                'color' => '#e67e22',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Other',
                'description' => 'Other complaints',
                'icon' => 'fa-ellipsis-h',
                'color' => '#95a5a6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}

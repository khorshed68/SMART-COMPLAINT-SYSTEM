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
                'name' => 'Mess & Food',
                'description' => 'Cafeteria, hostel mess, and food service issues',
                'icon' => 'fa-utensils',
                'color' => '#d35400',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carpentry & Furniture',
                'description' => 'Furniture repairs, doors, windows, and desks',
                'icon' => 'fa-couch',
                'color' => '#8e44ad',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Academic Facilities',
                'description' => 'Classroom equipment, library issues, and seminar rooms',
                'icon' => 'fa-graduation-cap',
                'color' => '#27ae60',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IT & Lab Equipment',
                'description' => 'Computer labs, projectors, software, and hardware',
                'icon' => 'fa-desktop',
                'color' => '#2c3e50',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Medical & Health',
                'description' => 'First aid, clinic services, and emergency medical requests',
                'icon' => 'fa-briefcase-medical',
                'color' => '#c0392b',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Transportation & Parking',
                'description' => 'Shuttles, parking permits, and vehicle issues',
                'icon' => 'fa-car-side',
                'color' => '#16a085',
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

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(
                ['name' => $cat['name']],
                [
                    'description' => $cat['description'],
                    'icon' => $cat['icon'],
                    'color' => $cat['color'],
                    'created_at' => $cat['created_at'],
                    'updated_at' => $cat['updated_at'],
                ]
            );
        }
    }
}

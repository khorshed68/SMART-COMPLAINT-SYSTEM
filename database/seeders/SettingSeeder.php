<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'setting_key' => 'site_name',
                'setting_value' => 'Smart Complaint System',
                'setting_group' => 'general',
                'description' => 'Website name',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'site_email',
                'setting_value' => 'admin@complaint.system',
                'setting_group' => 'general',
                'description' => 'Site admin email',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'complaints_per_page',
                'setting_value' => '10',
                'setting_group' => 'complaints',
                'description' => 'Number of complaints per page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'enable_email_notifications',
                'setting_value' => '1',
                'setting_group' => 'email',
                'description' => 'Enable email notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'enable_auto_assignment',
                'setting_value' => '0',
                'setting_group' => 'complaints',
                'description' => 'Auto-assign complaints to admins',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'maintenance_mode',
                'setting_value' => '0',
                'setting_group' => 'system',
                'description' => 'Enable maintenance mode',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'max_file_size',
                'setting_value' => '5242880',
                'setting_group' => 'system',
                'description' => 'Maximum file upload size in bytes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'allowed_file_types',
                'setting_value' => 'jpg,jpeg,png,gif,pdf,doc,docx',
                'setting_group' => 'system',
                'description' => 'Allowed file extensions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('settings')->insert($settings);
    }
}

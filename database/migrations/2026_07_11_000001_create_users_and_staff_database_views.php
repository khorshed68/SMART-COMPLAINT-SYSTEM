<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop views if they already exist
        DB::statement("DROP VIEW IF EXISTS student_users");
        DB::statement("DROP VIEW IF EXISTS staff_users");

        // Create student_users view
        DB::statement("
            CREATE VIEW student_users AS 
            SELECT id, name, email, phone, department, status, avatar, created_at, updated_at 
            FROM users 
            WHERE role = 'user'
        ");

        // Create staff_users view
        DB::statement("
            CREATE VIEW staff_users AS 
            SELECT id, name, email, phone, department, status, avatar, created_at, updated_at 
            FROM users 
            WHERE role = 'staff'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS student_users");
        DB::statement("DROP VIEW IF EXISTS staff_users");
    }
};

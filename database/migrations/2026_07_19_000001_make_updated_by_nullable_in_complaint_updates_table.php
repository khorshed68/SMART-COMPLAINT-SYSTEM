<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('complaint_updates', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['updated_by']);
            
            // Make column nullable
            $table->unsignedBigInteger('updated_by')->nullable()->change();
            
            // Re-add foreign key with nullOnDelete()
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaint_updates', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->unsignedBigInteger('updated_by')->change();
            $table->foreign('updated_by')->references('id')->on('users')->restrictOnDelete();
        });
    }
};

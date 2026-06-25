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
        Schema::create('complaint_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users')->restrictOnDelete();
            $table->enum('old_status', ['Pending', 'In Progress', 'Resolved', 'Rejected'])->nullable();
            $table->enum('new_status', ['Pending', 'In Progress', 'Resolved', 'Rejected']);
            $table->longText('comment')->nullable();
            $table->enum('update_type', ['status_change', 'comment', 'assignment', 'priority_change'])->default('status_change');
            $table->timestamp('created_at')->nullable();

            $table->index(['complaint_id', 'updated_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_updates');
    }
};

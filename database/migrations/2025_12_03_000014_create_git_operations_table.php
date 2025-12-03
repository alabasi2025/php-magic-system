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
        Schema::create('git_operations', function (Blueprint $table) {
            $table->id();
            $table->string('operation_type', 50)->index();
            $table->text('description')->nullable();
            $table->json('files_changed')->nullable();
            $table->integer('lines_added')->default(0);
            $table->integer('lines_deleted')->default(0);
            $table->string('commit_hash')->nullable();
            $table->text('commit_message')->nullable();
            $table->string('branch_name')->nullable()->index();
            $table->string('author')->nullable();
            $table->string('status', 50)->default('pending')->index();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('git_operations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'cashiers' table.
 * This table stores information about the cashiers in the system.
 * 
 * Task ID: 2001
 * Category: Database
 * Gene: Cashiers
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('cashiers', function (Blueprint $table) {
            // Primary Key and Identification
            $table->id(); // Auto-incrementing primary key

            // Cashier Information
            $table->string('name', 100)->comment('Full name of the cashier.');
            $table->string('username', 50)->unique()->comment('Unique username for login.');
            $table->string('email', 100)->unique()->nullable()->comment('Email address of the cashier (optional).');
            $table->string('phone', 20)->unique()->nullable()->comment('Phone number of the cashier (optional).');
            $table->string('password')->comment('Hashed password for the cashier.');
            
            // Status and Control
            $table->boolean('is_active')->default(true)->comment('Indicates if the cashier account is active.');
            $table->timestamp('last_login_at')->nullable()->comment('Timestamp of the last successful login.');
            
            // Foreign Keys (if applicable, e.g., to a branch or user model)
            // Assuming a basic structure for now. If integration with a main 'users' table is needed, this might change.
            // For Gene Architecture, we keep it self-contained unless explicit integration is required.

            // Standard Laravel Timestamps
            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at for soft deletion
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cashiers');
    }
};
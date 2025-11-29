<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the 'roles' table.
 * This table stores user roles for a robust Role-Based Access Control (RBAC) system.
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
        // Create the 'roles' table with specified columns
        Schema::create('roles', function (Blueprint $table) {
            // Primary key ID
            $table->id();

            // Internal name of the role (e.g., 'admin', 'super-user'). Must be unique.
            $table->string('name')->unique()->comment('Internal, unique name of the role (e.g., admin)');

            // Human-readable name of the role (e.g., 'Administrator', 'Super User').
            $table->string('display_name')->comment('Human-readable name of the role');

            // Detailed description of the role's purpose and permissions.
            $table->text('description')->nullable()->comment('Detailed description of the role');

            // Flag to mark system-critical roles that should not be deleted or modified via the application.
            $table->boolean('is_system')->default(false)->comment('Flag for system-critical roles');

            // Timestamps for creation and last update.
            $table->timestamps();

            // Optional: Add an index on display_name for faster lookups, though 'name' is the primary lookup field.
            // $table->index('display_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'roles' table if the migration is rolled back
        Schema::dropIfExists('roles');
    }
};
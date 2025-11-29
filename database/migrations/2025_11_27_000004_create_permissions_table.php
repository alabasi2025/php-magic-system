<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'permissions' table.
 * This table stores system permissions, typically used for role-based access control (RBAC).
 *
 * Columns:
 * - id: Primary key.
 * - name: Unique internal name for the permission (e.g., 'create-user').
 * - display_name: Human-readable name for the UI (e.g., 'Create User').
 * - description: Detailed explanation of the permission.
 * - module: Grouping for permissions (e.g., 'users', 'settings').
 * - timestamps: created_at and updated_at.
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
        // Create the 'permissions' table
        Schema::create('permissions', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Permission Name (Internal use, must be unique)
            // Using a string of 100 characters for the name.
            $table->string('name', 100)->unique()->comment('Unique internal name for the permission (e.g., create-user)');

            // Display Name (Human-readable for UI)
            // Using a string of 150 characters.
            $table->string('display_name', 150)->nullable()->comment('Human-readable name for the UI');

            // Description (Detailed explanation)
            // Using a text column for potentially longer descriptions.
            $table->text('description')->nullable()->comment('Detailed explanation of the permission');

            // Module (Grouping permissions)
            // Using a string of 50 characters to group permissions (e.g., users, posts, settings).
            $table->string('module', 50)->index()->comment('Grouping for permissions (e.g., users, settings)');

            // Timestamps
            $table->timestamps();

            // Optional: Add a combined unique index for name and module to prevent duplicate names within the same module,
            // although 'name' is already globally unique. Keeping 'name' globally unique is standard for simplicity.
            // $table->unique(['name', 'module']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'permissions' table if it exists
        Schema::dropIfExists('permissions');
    }
};
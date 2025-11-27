<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'role_user' pivot table.
 * This table establishes the many-to-many relationship between roles and users.
 * It includes foreign key constraints for data integrity and a unique composite index
 * to prevent duplicate role assignments for a single user.
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
        // Create the pivot table for the many-to-many relationship
        Schema::create('role_user', function (Blueprint $table) {
            // Define the foreign key for the 'roles' table
            // Using foreignId() is the recommended Laravel 12 way for foreign keys
            $table->foreignId('role_id')
                  ->constrained('roles') // Assumes a 'roles' table exists
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('Foreign key to the roles table.');

            // Define the foreign key for the 'users' table
            $table->foreignId('user_id')
                  ->constrained('users') // Assumes a 'users' table exists
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('Foreign key to the users table.');

            // Add timestamps (created_at and updated_at) as required
            $table->timestamps();

            // Create a unique composite index to ensure a user can only have a specific role once
            // This is crucial for pivot table integrity
            $table->unique(['role_id', 'user_id'], 'role_user_role_id_user_id_unique');

            // Optionally, add an index for faster lookups by user_id, which is common
            $table->index('user_id', 'role_user_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the table if the migration is rolled back
        Schema::dropIfExists('role_user');
    }
};
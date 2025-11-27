<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the 'project_members' table.
 * This table stores the relationship between projects and users,
 * defining the role and specific permissions of a user within a project.
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
        Schema::create('project_members', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'projects' table
            // Ensures data integrity and proper relationship with the Project model.
            $table->foreignId('project_id')
                  ->constrained() // Assumes 'projects' table exists
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Foreign Key to the 'users' table
            // Links the project member to a specific user.
            $table->foreignId('user_id')
                  ->constrained() // Assumes 'users' table exists
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Role of the user in the project (e.g., 'admin', 'editor', 'viewer').
            // Using a string for flexibility, but an enum or separate roles table might be better for complex systems.
            $table->string('role', 50)->default('viewer');

            // JSON column to store granular permissions for the user in this specific project.
            // This allows for flexible, non-standard permission sets per member.
            $table->json('permissions')->nullable();

            // Ensures that a user can only be a member of a project once.
            $table->unique(['project_id', 'user_id']);

            // Timestamps (created_at and updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
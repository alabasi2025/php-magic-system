<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'departments' table.
 * This table stores organizational departments, including hierarchical structure
 * and links to the parent unit and the department manager.
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
        Schema::create('departments', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the parent unit (assuming a 'units' table exists)
            // Using constrained() for convention-based foreign key to 'units' table
            $table->foreignId('unit_id')
                  ->comment('The ID of the parent organizational unit.')
                  ->constrained('units')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Department Details
            $table->string('name', 100)->comment('The name of the department.');
            $table->string('code', 20)->unique()->comment('A unique code for the department.');

            // Foreign Key to the manager (assuming a 'users' table exists)
            // The manager is optional, hence nullable()
            $table->foreignId('manager_id')
                  ->nullable()
                  ->comment('The ID of the user who manages this department.')
                  ->constrained('users') // Explicitly reference 'users' table
                  ->onUpdate('cascade')
                  ->onDelete('set null'); // Set manager_id to NULL if the user is deleted

            // Self-referencing Foreign Key for hierarchical structure
            // The parent department is optional, hence nullable()
            $table->foreignId('parent_id')
                  ->nullable()
                  ->comment('The ID of the parent department for hierarchical structure.')
                  ->constrained('departments') // Self-reference
                  ->onUpdate('cascade')
                  ->onDelete('cascade'); // If parent is deleted, children are also deleted (or can be set to null depending on business logic, but cascade is common for strong hierarchy)

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index('code');
            $table->index('name');
            $table->index('unit_id');
            $table->index('manager_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

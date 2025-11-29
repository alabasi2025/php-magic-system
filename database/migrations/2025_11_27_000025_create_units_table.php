<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'units' table.
 * This table stores information about organizational units, such as departments or branches,
 * within the SEMOP Magic System.
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
        // Create the 'units' table with specified columns and constraints.
        Schema::create('units', function (Blueprint $table) {
            // Primary key ID
            $table->id();

            // Foreign key to the 'organizations' table.
            // Assuming a many-to-one relationship where a unit belongs to an organization.
            $table->foreignId('organization_id')
                  ->constrained('organizations') // Assumes 'organizations' table exists
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('The organization this unit belongs to.');

            // Unit name (e.g., "Sales Department", "Riyadh Branch")
            $table->string('name', 255)->comment('The name of the unit.');

            // Unique code for the unit (e.g., "SALES-001", "R-BR-002")
            $table->string('code', 50)->unique()->comment('A unique code for the unit.');

            // Type of the unit (e.g., "Department", "Branch", "Warehouse")
            $table->string('type', 50)->comment('The type of the unit (e.g., Department, Branch).');

            // Location details (can be a simple string or a JSON column for complex data)
            // Using a string for simplicity, but a separate 'locations' table or JSON could be used for more complexity.
            $table->string('location', 255)->nullable()->comment('The physical location or address of the unit.');

            // Standard Laravel timestamps (created_at and updated_at)
            $table->timestamps();

            // Add indexes for frequently searched columns to improve performance.
            $table->index('organization_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'units' table if the migration is rolled back.
        Schema::dropIfExists('units');
    }
};

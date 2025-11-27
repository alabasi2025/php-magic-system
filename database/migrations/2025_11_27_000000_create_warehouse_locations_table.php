<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the warehouse_locations table.
 *
 * This table stores information about specific locations within a warehouse,
 * such as shelves, zones, or bins, to facilitate inventory management.
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
        Schema::create('warehouse_locations', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'warehouses' table
            // Assumes a 'warehouses' table exists.
            $table->foreignId('warehouse_id')
                  ->constrained('warehouses')
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('The parent warehouse this location belongs to.');

            // Unique identifier for the location (e.g., Aisle-01-Shelf-05)
            $table->string('code', 50)->unique()->comment('Unique code for the warehouse location.');

            // Human-readable name for the location
            $table->string('name', 100)->comment('Descriptive name of the location.');

            // Type of location (e.g., 'shelf', 'zone', 'bin', 'staging')
            $table->string('type', 50)->index()->comment('The type of location (e.g., shelf, zone).');

            // Capacity of the location (e.g., maximum number of items or volume)
            $table->unsignedInteger('capacity')->default(0)->comment('The storage capacity of the location.');

            // Ensure the combination of warehouse_id and code is unique, although 'code' is already unique globally.
            // This index is good practice for common lookups.
            $table->unique(['warehouse_id', 'code']);

            // Timestamps for creation and last update
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
        Schema::dropIfExists('warehouse_locations');
    }
};

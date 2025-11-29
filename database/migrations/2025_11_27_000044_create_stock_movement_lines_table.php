<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'stock_movement_lines' table.
 * This table stores the details of each item moved in a stock movement.
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
        // Create the 'stock_movement_lines' table
        Schema::create('stock_movement_lines', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key to the 'stock_movements' table
            // Assumes 'stock_movements' table uses an auto-incrementing big integer primary key
            $table->foreignId('stock_movement_id')
                  ->constrained('stock_movements') // Assumes the parent table is 'stock_movements'
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('Foreign key to the stock_movements table');

            // Foreign key to the 'items' table
            // Assumes 'items' table uses an auto-incrementing big integer primary key
            $table->foreignId('item_id')
                  ->constrained('items') // Assumes the parent table is 'items'
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // Restrict deletion of an item if it has movement lines
                  ->comment('Foreign key to the items table');

            // Quantity of the item moved. Using decimal for precision.
            // Precision: 13 total digits, 4 digits after the decimal point.
            $table->decimal('quantity', 13, 4)->comment('Quantity of the item moved');

            // Unit cost of the item at the time of movement. Using decimal for currency precision.
            // Precision: 13 total digits, 4 digits after the decimal point.
            $table->decimal('unit_cost', 13, 4)->comment('Unit cost of the item at the time of movement');

            // Foreign key to the 'locations' table (e.g., warehouse, shelf)
            // Assumes 'locations' table uses an auto-incrementing big integer primary key
            $table->foreignId('location_id')
                  ->constrained('locations') // Assumes the parent table is 'locations'
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // Restrict deletion of a location if it has movement lines
                  ->comment('Foreign key to the locations table (source/destination)');

            // Timestamps for creation and last update
            $table->timestamps();

            // Optional: Add a unique index to prevent duplicate lines for the same movement and item,
            // though typically a stock movement can have multiple lines for the same item if it's
            // moved to/from different locations or batches. We'll omit a unique index here
            // to allow for flexibility, but it's a consideration for specific business logic.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'stock_movement_lines' table if it exists
        Schema::dropIfExists('stock_movement_lines');
    }
};
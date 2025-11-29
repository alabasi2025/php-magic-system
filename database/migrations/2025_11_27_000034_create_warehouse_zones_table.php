<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'warehouse_zones' table.
 * This table stores information about different zones within a warehouse.
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
        Schema::create('warehouse_zones', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key to the 'warehouses' table
            // Assuming a 'warehouses' table exists and 'warehouse_id' is a big integer
            $table->foreignId('warehouse_id')
                  ->constrained('warehouses') // Assumes the foreign table is 'warehouses'
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('The ID of the warehouse this zone belongs to.');

            // Zone name (e.g., "Receiving Area", "Shelf A1")
            $table->string('name', 100)->comment('The unique name of the warehouse zone.');
            
            // Optional description for the zone
            $table->text('description')->nullable()->comment('Detailed description of the warehouse zone.');

            // Timestamps (created_at and updated_at)
            $table->timestamps();

            // Add a unique index to prevent duplicate zone names within the same warehouse
            $table->unique(['warehouse_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_zones');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'item_units' table.
 * This table stores information about different units of measure for items,
 * including base units and conversion factors for derived units.
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
        Schema::create('item_units', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Unit Name
            // Stores the human-readable name of the unit (e.g., "Piece", "Kilogram").
            $table->string('name', 100)->comment('The human-readable name of the unit.');

            // Unit Code
            // Stores a unique, short code for the unit (e.g., "PC", "KG").
            $table->string('code', 10)->unique()->comment('A unique, short code for the unit.');

            // Base Unit Relationship
            // The ID of the base unit if this unit is a derived unit.
            // Nullable: A unit is a base unit if this column is null.
            $table->foreignId('base_unit_id')
                  ->nullable()
                  ->constrained('item_units') // Self-referencing foreign key
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // Prevent deletion of a base unit if it's referenced
                  ->comment('The ID of the base unit. Null if this is a base unit.');

            // Conversion Factor
            // The factor to convert this unit to the base unit.
            // For base units, this factor is 1.0.
            $table->decimal('conversion_factor', 10, 4)->default(1.0000)->comment('The factor to convert this unit to its base unit.');

            // Timestamps
            $table->timestamps();

            // Indexes for optimization
            $table->index('base_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('item_units');
    }
};

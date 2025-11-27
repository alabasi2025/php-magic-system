<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the item_variants table.
 * This table stores different variations of an item, such as size, color, or material.
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
        Schema::create('item_variants', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the items table
            // Assumes an 'items' table exists with an 'id' column.
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('The ID of the parent item.');

            // Stock Keeping Unit (SKU) - must be unique for each variant
            $table->string('sku')->unique()->comment('Unique Stock Keeping Unit for the variant.');

            // Variant attributes (e.g., {"color": "red", "size": "L"})
            $table->json('attributes')->comment('JSON object containing variant attributes (e.g., size, color).');

            // Price of the variant
            // Using decimal for precise monetary values.
            $table->decimal('price', 10, 2)->comment('The price of this specific variant.');

            // Stock quantity
            $table->integer('stock')->default(0)->comment('The current stock level for this variant.');

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
        Schema::dropIfExists('item_variants');
    }
};

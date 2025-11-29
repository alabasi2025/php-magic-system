<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'items' table.
 * This table stores information about all items/products in the system.
 * It includes details like unique code, name, categorization, pricing, and status.
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
        Schema::create('items', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Item Identification and Core Data
            // Unique code for the item (e.g., SKU, internal reference).
            $table->string('code', 50)->unique()->comment('Unique item code (SKU or internal reference)');
            // Name of the item.
            $table->string('name', 255)->comment('Name of the item');

            // Categorization and Units
            // Foreign key to the categories table. Items must belong to a category.
            $table->foreignId('category_id')
                  ->constrained('categories') // Assumes a 'categories' table exists
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // Prevent deletion of category if items are linked
                  ->comment('Foreign key to the categories table');

            // Foreign key to the units table. Defines the unit of measure (e.g., piece, kg, liter).
            $table->foreignId('unit_id')
                  ->constrained('units') // Assumes a 'units' table exists
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // Prevent deletion of unit if items are linked
                  ->comment('Foreign key to the units table (Unit of Measure)');

            // Item Type
            // Defines the nature of the item (e.g., product, service, raw_material).
            $table->enum('type', ['product', 'service', 'raw_material', 'asset'])
                  ->default('product')
                  ->comment('Type of item: product, service, raw_material, or asset');

            // Pricing and Cost
            // Selling price of the item. Using decimal for precise monetary values.
            $table->decimal('price', 10, 2)->default(0.00)->comment('Selling price of the item');
            // Cost price of the item.
            $table->decimal('cost', 10, 2)->default(0.00)->comment('Cost price of the item');

            // Inventory and Tracking
            // Barcode for quick scanning and identification. Can be nullable.
            $table->string('barcode', 100)->nullable()->index()->comment('Barcode for the item');

            // Status
            // Flag to indicate if the item is currently active and available.
            $table->boolean('is_active')->default(true)->comment('Item active status (true/false)');

            // Timestamps
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
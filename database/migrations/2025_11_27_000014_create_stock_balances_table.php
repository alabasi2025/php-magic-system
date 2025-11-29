<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Create the 'stock_balances' table to track inventory levels in different locations.
        Schema::create('stock_balances', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key to the 'warehouses' table (assuming it exists)
            // This links the balance to a specific warehouse.
            $table->foreignId('warehouse_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Foreign key to the 'items' table (assuming it exists)
            // This links the balance to a specific inventory item.
            $table->foreignId('item_id')
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Foreign key to the 'locations' table (optional, nullable)
            // This allows tracking stock within a specific location inside the warehouse (e.g., shelf, bin).
            $table->foreignId('location_id')
                  ->nullable()
                  ->constrained()
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            // The actual quantity of the item in stock. Using decimal for precision.
            $table->decimal('quantity', 12, 4)->default(0.0000)->comment('Current physical stock quantity.');

            // The quantity of the item that is reserved for orders or transfers.
            $table->decimal('reserved_quantity', 12, 4)->default(0.0000)->comment('Quantity reserved for future use (e.g., open orders).');

            // Ensure a unique combination of warehouse, item, and location for a single balance record.
            $table->unique(['warehouse_id', 'item_id', 'location_id'], 'stock_balance_unique');

            // Timestamps for creation and last update.
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
        Schema::dropIfExists('stock_balances');
    }
};

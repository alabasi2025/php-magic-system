<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'stock_movements' table.
 *
 * This table records all movements of stock, including incoming, outgoing,
 * and internal adjustments, linking them to a specific warehouse.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'warehouses' table.
            // This links the movement to the specific warehouse where it occurred.
            $table->foreignId('warehouse_id')
                  ->constrained() // Assumes 'warehouses' table exists
                  ->onUpdate('cascade')
                  ->onDelete('restrict'); // Prevent deletion of a warehouse if movements exist

            // Type of stock movement (e.g., 'in', 'out', 'adjustment').
            // Using a simple enum for defined types.
            $table->enum('type', ['in', 'out', 'adjustment'])->comment('Type of movement: in, out, or adjustment');

            // A unique reference number for the movement (e.g., invoice number, transfer ID).
            $table->string('reference')->unique()->nullable()->comment('Unique reference number for the movement');

            // The date the stock movement occurred.
            $table->date('date')->comment('The date of the stock movement');

            // Status of the movement (e.g., 'pending', 'completed', 'cancelled').
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending')->comment('Current status of the movement');

            // Optional notes or description for the movement.
            $table->text('notes')->nullable()->comment('Additional notes or description');

            // Timestamps for creation and last update.
            $table->timestamps();

            // Adding indexes for frequently queried columns for performance optimization.
            $table->index(['warehouse_id', 'date']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 100)->unique()->comment('Stock Keeping Unit (unique identifier)');
            $table->string('name', 200)->comment('Item name');
            $table->text('description')->nullable();
            $table->foreignId('unit_id')->constrained('item_units')->restrictOnDelete()->comment('Primary unit of measurement');
            $table->decimal('min_stock', 15, 2)->default(0)->comment('Minimum stock level (alert threshold)');
            $table->decimal('max_stock', 15, 2)->default(0)->comment('Maximum stock level');
            $table->decimal('unit_price', 15, 2)->default(0)->comment('Unit price for accounting');
            $table->string('barcode', 100)->nullable()->unique();
            $table->string('image_path', 500)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('sku');
            $table->index('barcode');
            $table->index('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

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
        Schema::create('item_warehouse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->decimal('quantity', 15, 3)->default(0)->comment('الكمية الإجمالية');
            $table->decimal('reserved_quantity', 15, 3)->default(0)->comment('الكمية المحجوزة');
            $table->decimal('available_quantity', 15, 3)->default(0)->comment('الكمية المتاحة');
            $table->decimal('average_cost', 15, 4)->default(0)->comment('متوسط التكلفة');
            $table->decimal('last_purchase_price', 15, 4)->nullable()->comment('آخر سعر شراء');
            $table->date('last_purchase_date')->nullable()->comment('تاريخ آخر شراء');
            $table->timestamps();
            
            // فهرس فريد لضمان عدم تكرار الصنف في نفس المخزن
            $table->unique(['item_id', 'warehouse_id'], 'item_warehouse_unique');
            
            // فهارس لتحسين الأداء
            $table->index('item_id');
            $table->index('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_warehouse');
    }
};

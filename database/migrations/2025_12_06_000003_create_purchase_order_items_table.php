<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for Purchase Order Items Table
 * جدول أصناف أوامر الشراء
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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete()->comment('أمر الشراء');
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete()->comment('الصنف');
            $table->decimal('quantity', 15, 2)->comment('الكمية المطلوبة');
            $table->decimal('received_quantity', 15, 2)->default(0)->comment('الكمية المستلمة');
            $table->decimal('unit_price', 15, 2)->comment('سعر الوحدة');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('نسبة الضريبة');
            $table->decimal('discount_rate', 5, 2)->default(0)->comment('نسبة الخصم');
            $table->decimal('total_amount', 15, 2)->comment('المجموع');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('purchase_order_id');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة (إنشاء جدول Stock Out Details).
     */
    public function up(): void
    {
        Schema::create('stock_out_details', function (Blueprint $table) {
            $table->id();
            // ربط بتفاصيل إذن الإخراج
            $table->foreignId('stock_out_id')->constrained('stock_outs')->onDelete('cascade')->comment('معرف إذن الإخراج');
            // الصنف/المنتج الذي تم إخراجه
            $table->foreignId('item_id')->constrained('items')->comment('معرف الصنف');
            // الكمية المخرجة
            $table->decimal('quantity', 10, 2)->comment('الكمية المخرجة');
            // سعر الوحدة عند الإخراج
            $table->decimal('unit_price', 10, 2)->comment('سعر الوحدة');
            // إجمالي سعر البند (الكمية * سعر الوحدة)
            $table->decimal('total_price', 10, 2)->comment('إجمالي سعر البند');
            $table->timestamps();

            // ضمان عدم تكرار الصنف في نفس إذن الإخراج
            $table->unique(['stock_out_id', 'item_id']);
        });
    }

    /**
     * عكس الهجرة (حذف جدول Stock Out Details).
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_out_details');
    }
};

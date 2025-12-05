<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * هجرة لإنشاء جدول تفاصيل تحويلات المخزون (stock_transfer_details).
 */
return new class extends Migration
{
    /**
     * تشغيل الهجرة.
     */
    public function up(): void
    {
        Schema::create('stock_transfer_details', function (Blueprint $table) {
            $table->id();
            
            // ربط بجدول التحويلات
            $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->onDelete('cascade')->comment('رقم تحويل المخزون');
            
            // المادة المحولة
            $table->foreignId('item_id')->constrained('items')->comment('المادة المحولة');
            
            // الكمية
            $table->unsignedDecimal('quantity', 10, 2)->comment('الكمية المحولة');
            
            $table->timestamps();
            
            // ضمان عدم تكرار المادة في نفس التحويل
            $table->unique(['stock_transfer_id', 'item_id']);
        });
    }

    /**
     * عكس الهجرة.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_details');
    }
};

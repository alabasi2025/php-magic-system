<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرات (إنشاء جدول تفاصيل حركات الإدخال).
     */
    public function up(): void
    {
        Schema::create('stock_in_details', function (Blueprint $table) {
            $table->id();
            
            // مفتاح خارجي يربط بالتفاصيل بحركة الإدخال الرئيسية
            $table->foreignId('stock_in_id')->constrained('stock_ins')->onDelete('cascade')->comment('إذن الإدخال');
            
            // مفتاح خارجي للصنف الذي تم إدخاله
            $table->foreignId('item_id')->constrained('items')->comment('الصنف');
            
            // الكمية المدخلة
            $table->decimal('quantity', 10, 2)->comment('الكمية');
            
            // سعر الوحدة
            $table->decimal('unit_price', 10, 2)->comment('سعر الوحدة');
            
            // إجمالي سعر الصنف (الكمية * سعر الوحدة)
            $table->decimal('total_price', 10, 2)->comment('الإجمالي');

            $table->timestamps();
            
            // ضمان عدم تكرار الصنف في نفس إذن الإدخال
            $table->unique(['stock_in_id', 'item_id']);
        });
    }

    /**
     * عكس الهجرات (حذف جدول تفاصيل حركات الإدخال).
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_in_details');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة (إنشاء الجدول).
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            // مفتاح خارجي للمنتج
            $table->foreignId('product_id')->constrained()->comment('المنتج الذي تمت عليه الحركة');
            // مفتاح خارجي للمخزن المصدر (يجب أن يكون موجوداً)
            $table->foreignId('from_warehouse_id')->constrained('warehouses')->comment('المخزن المصدر للحركة');
            // مفتاح خارجي للمخزن الوجهة (يمكن أن يكون NULL إذا كانت الحركة "خارج")
            $table->foreignId('to_warehouse_id')->nullable()->constrained('warehouses')->comment('المخزن الوجهة للحركة');
            // نوع الحركة: 'in', 'out', 'transfer'
            $table->enum('type', ['in', 'out', 'transfer'])->comment('نوع الحركة (إدخال، إخراج، تحويل)');
            // كمية الحركة
            $table->integer('quantity')->comment('كمية المنتج في الحركة');
            // تاريخ ووقت الحركة
            $table->timestamp('movement_date')->useCurrent()->comment('تاريخ ووقت الحركة');
            // ملاحظات إضافية
            $table->text('notes')->nullable()->comment('ملاحظات حول الحركة');
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

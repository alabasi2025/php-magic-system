<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرات (إنشاء جدول حركات الإدخال).
     */
    public function up(): void
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            // رقم إذن الإدخال، يجب أن يكون فريداً
            $table->string('number')->unique()->comment('رقم إذن الإدخال');
            
            // مفتاح خارجي للمخزن
            $table->foreignId('warehouse_id')->constrained('warehouses')->comment('المخزن');
            
            // مفتاح خارجي للمورد
            $table->foreignId('supplier_id')->constrained('suppliers')->comment('المورد');
            
            // تاريخ حركة الإدخال
            $table->date('date')->comment('تاريخ الإدخال');
            
            // مرجع خارجي مثل رقم فاتورة المورد
            $table->string('reference')->nullable()->comment('المرجع الخارجي (مثل رقم الفاتورة)');
            
            // ملاحظات حول الحركة
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            // إجمالي المبلغ للحركة
            $table->decimal('total_amount', 10, 2)->default(0)->comment('إجمالي المبلغ');
            
            // حالة الحركة (مثل مسودة، مكتملة، ملغاة)
            $table->string('status')->default('Draft')->comment('حالة الإذن');
            
            // مفتاح خارجي للمستخدم الذي أنشأ الحركة
            $table->foreignId('created_by')->constrained('users')->comment('المستخدم المنشئ');

            $table->timestamps();
        });
    }

    /**
     * عكس الهجرات (حذف جدول حركات الإدخال).
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};

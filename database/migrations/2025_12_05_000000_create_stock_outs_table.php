<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة (إنشاء جدول Stock Outs).
     */
    public function up(): void
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            // رقم إذن الإخراج، يجب أن يكون فريداً
            $table->string('number')->unique()->comment('رقم إذن الإخراج');
            // المخزن الذي تم منه الإخراج
            $table->foreignId('warehouse_id')->constrained('warehouses')->comment('معرف المخزن');
            // العميل الذي تم الإخراج له
            $table->foreignId('customer_id')->constrained('customers')->comment('معرف العميل');
            // تاريخ الإخراج
            $table->date('date')->comment('تاريخ الإخراج');
            // مرجع خارجي (مثل رقم فاتورة أو طلب)
            $table->string('reference')->nullable()->comment('مرجع خارجي');
            // ملاحظات حول عملية الإخراج
            $table->text('notes')->nullable()->comment('ملاحظات');
            // إجمالي المبلغ (قد يستخدم لحسابات التكلفة أو المبيعات)
            $table->decimal('total_amount', 10, 2)->default(0)->comment('إجمالي المبلغ');
            // حالة الإذن (مثل: معلق، مكتمل، ملغي)
            $table->string('status')->default('pending')->comment('حالة الإذن');
            // المستخدم الذي أنشأ الإذن
            $table->foreignId('created_by')->constrained('users')->comment('معرف المستخدم المنشئ');
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة (حذف جدول Stock Outs).
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};

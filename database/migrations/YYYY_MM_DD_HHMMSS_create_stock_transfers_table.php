<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * هجرة لإنشاء جدول تحويلات المخزون (stock_transfers).
 */
return new class extends Migration
{
    /**
     * تشغيل الهجرة.
     */
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            // رقم التحويل، يمكن أن يكون تسلسلياً أو فريداً
            $table->string('number')->unique()->comment('رقم التحويل الفريد');
            
            // المخزن المصدر (من)
            $table->foreignId('from_warehouse_id')->constrained('warehouses')->comment('المخزن المصدر');
            
            // المخزن المستقبل (إلى)
            $table->foreignId('to_warehouse_id')->constrained('warehouses')->comment('المخزن المستقبل');
            
            // تاريخ التحويل
            $table->date('date')->comment('تاريخ طلب التحويل');
            
            // مرجع خارجي (اختياري)
            $table->string('reference')->nullable()->comment('مرجع خارجي للتحويل');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات حول التحويل');
            
            // حالة التحويل (قيد الانتظار، موافق عليه، مرفوض، مكتمل)
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending')->comment('حالة التحويل');
            
            // المستخدم الذي أنشأ التحويل
            $table->foreignId('created_by')->constrained('users')->comment('المستخدم المنشئ');
            
            // المستخدم الذي وافق على التحويل (يُملأ عند الموافقة)
            $table->foreignId('approved_by')->nullable()->constrained('users')->comment('المستخدم الموافق');
            
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};

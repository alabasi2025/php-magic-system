<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرات (الإنشاء).
     */
    public function up(): void
    {
        Schema::create('stock_counts', function (Blueprint $table) {
            $table->id();
            // رقم الجرد، يجب أن يكون فريداً
            $table->string('number')->unique()->comment('رقم عملية الجرد');
            // ربط الجرد بالمخزن (نفترض وجود جدول warehouses)
            $table->foreignId('warehouse_id')->constrained('warehouses')->comment('المخزن الذي تم جرده');
            // تاريخ الجرد
            $table->date('date')->comment('تاريخ الجرد');
            // حالة الجرد: مسودة، قيد المراجعة، مكتمل، تم التعديل
            $table->enum('status', ['Draft', 'Pending Approval', 'Completed', 'Adjusted'])->default('Draft')->comment('حالة الجرد');
            // ملاحظات عامة على عملية الجرد
            $table->text('notes')->nullable()->comment('ملاحظات عامة');
            // من قام بإنشاء الجرد (نفترض وجود جدول users)
            $table->foreignId('created_by')->constrained('users')->comment('المستخدم الذي أنشأ الجرد');
            // من قام بالموافقة على الجرد وتعديل المخزون بناءً عليه
            $table->foreignId('approved_by')->nullable()->constrained('users')->comment('المستخدم الذي وافق على الجرد');
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرات (الحذف).
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_counts');
    }
};

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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            // رمز المخزن، يجب أن يكون فريداً ومطلوباً
            $table->string('code')->unique()->comment('رمز المخزن الفريد');
            // اسم المخزن، مطلوب
            $table->string('name')->comment('اسم المخزن');
            // الموقع الجغرافي أو الوصف
            $table->string('location')->nullable()->comment('الموقع الجغرافي أو الوصف');
            // العنوان التفصيلي
            $table->string('address')->nullable()->comment('العنوان التفصيلي');
            // رقم الهاتف
            $table->string('phone')->nullable()->comment('رقم الهاتف');
            // البريد الإلكتروني
            $table->string('email')->nullable()->comment('البريد الإلكتروني');
            // معرف مدير المخزن، مفتاح خارجي اختياري
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null')->comment('معرف مدير المخزن');
            // حالة التفعيل، القيمة الافتراضية نشط (true)
            $table->boolean('is_active')->default(true)->comment('حالة التفعيل');
            // السعة التخزينية، يمكن أن تكون فارغة
            $table->unsignedInteger('capacity')->nullable()->comment('السعة التخزينية للمخزن');
            // القيمة الحالية للمخزون، بقيمة افتراضية صفر
            $table->decimal('current_stock_value', 15, 2)->default(0.00)->comment('القيمة الحالية للمخزون');

            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};

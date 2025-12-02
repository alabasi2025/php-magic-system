<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// استخدام ميزات PHP 8.4 (نلتزم بالمعايير الحديثة)

return new class extends Migration
{
    /**
     * اسم الجدول
     * @var string
     */
    protected string $tableName = 'alabasi_generated_reports';

    /**
     * تشغيل الترحيل (إنشاء الجدول).
     */
    public function up(): void
    {
        // إنشاء جدول التقارير المولدة
        Schema::create($this->tableName, function (Blueprint $table) {
            // المعرف الأساسي للتقرير
            $table->id();

            // معرف القالب الذي تم توليد التقرير بناءً عليه (للتكامل مع جين التقارير)
            // نفترض وجود جدول report_templates
                  ->comment('معرف قالب التقرير')
                  ->cascadeOnDelete();

            // عنوان التقرير
            $table->string('title', 255)->comment('عنوان التقرير');

            // معرف المستخدم الذي قام بتوليد التقرير
                  ->comment('معرف المستخدم الذي قام بالتوليد')
                  ->cascadeOnDelete();

            // تاريخ بداية الفترة التي يغطيها التقرير
            $table->date('period_from')->comment('تاريخ بداية الفترة');

            // تاريخ نهاية الفترة التي يغطيها التقرير
            $table->date('period_to')->comment('تاريخ نهاية الفترة');

            // بيانات إضافية للتقرير (مثل المعايير، أو ملخص البيانات)
            $table->json('data')->nullable()->comment('بيانات التقرير الإضافية (JSON)');

            // مسار الملف الناتج (PDF, Excel, إلخ)
            $table->string('file_path', 512)->nullable()->comment('مسار ملف التقرير');

            // حالة التقرير (قيد الانتظار، مكتمل، فشل)
            $table->string('status', 50)->default('pending')->comment('حالة التقرير');

            // حقول الطابع الزمني (تاريخ الإنشاء والتحديث)
            $table->timestamps();

            // إضافة فهرس لتحسين البحث حسب الحالة والمستخدم
            $table->index(['status', 'generated_by']);
        });
    }

    /**
     * عكس الترحيل (حذف الجدول).
     */
    public function down(): void
    {
        // حذف جدول التقارير المولدة
        Schema::dropIfExists($this->tableName);
    }
};

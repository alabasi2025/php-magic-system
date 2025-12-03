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
        Schema::create('ai_error_logs', function (Blueprint $table) {
            // المعرف الأساسي للجدول
            $table->id();

            // ربط الخطأ بمستخدم معين (اختياري). يتم تعيينه إلى NULL إذا تم حذف المستخدم.
            // يفترض وجود جدول 'users'
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null')
                  ->comment('معرف المستخدم الذي واجه الخطأ');

            // مصدر الخطأ (مثل: API, Web, Job, CLI)
            $table->string('source', 50)->index()->comment('مصدر حدوث الخطأ');

            // مستوى خطورة الخطأ (مثل: debug, info, warning, error, critical)
            $table->enum('level', ['debug', 'info', 'warning', 'error', 'critical'])
                  ->default('error')
                  ->index()
                  ->comment('مستوى خطورة الخطأ');

            // رمز الخطأ المحدد (مثل: HTTP Status Code أو رمز داخلي)
            $table->string('error_code', 100)->nullable()->index()->comment('رمز الخطأ المحدد');

            // رسالة الخطأ الموجزة
            $table->string('message', 500)->comment('رسالة الخطأ الموجزة');

            // التفاصيل الكاملة للخطأ، مثل تتبع المكدس (Stack Trace)
            $table->text('details')->comment('التفاصيل الكاملة للخطأ وتتبع المكدس');

            // بيانات السياق الإضافية بصيغة JSON (مثل: مدخلات الطلب، متغيرات البيئة)
            $table->json('context')->nullable()->comment('بيانات السياق الإضافية بصيغة JSON');

            // طوابع زمنية لإنشاء وتحديث السجل
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_error_logs');
    }
};

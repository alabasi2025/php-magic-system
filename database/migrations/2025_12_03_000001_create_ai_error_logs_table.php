<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرات (إنشاء الجدول).
     */
    public function up(): void
    {
        Schema::create('ai_error_logs', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي التلقائي
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم الذي واجه الخطأ (يمكن أن يكون فارغًا إذا كان خطأ نظام)
            $table->text('error_message'); // رسالة الخطأ الموجزة
            $table->text('stack_trace')->nullable(); // تتبع المكدس الكامل للخطأ
            $table->string('severity', 20)->default('low'); // مستوى خطورة الخطأ (مثل: low, medium, high, critical)
            $table->timestamps(); // حقلا created_at و updated_at

            // إضافة فهارس لتحسين أداء الاستعلامات
            $table->index('user_id');
            $table->index('severity');
        });
    }

    /**
     * عكس الهجرات (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_error_logs');
    }
};

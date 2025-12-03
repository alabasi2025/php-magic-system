<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرات (Migrations).
     */
    public function up(): void
    {
        Schema::create('ai_rate_limits', function (Blueprint $table) {
            // المعرف الأساسي للجدول
            $table->id()->comment('المعرف الأساسي لقاعدة الحد الأقصى للمعدل');

            // تحديد الكيان الذي ينطبق عليه الحد (مثل مستخدم أو فريق) باستخدام علاقة تعدد الأشكال (Polymorphic)
            $table->morphs('limitable')->comment('تحديد الكيان الذي ينطبق عليه الحد الأقصى للمعدل (مثل user_id و user_type)');

            // اسم الميزة أو الأداة التي يتم تحديد معدلها
            $table->string('feature', 100)->comment('اسم الميزة أو الأداة التي يتم تحديد معدلها (مثل: image_generation)');
            
            // الحد الأقصى لعدد الطلبات المسموح بها
            $table->unsignedInteger('max_requests')->comment('الحد الأقصى لعدد الطلبات المسموح بها في الفترة المحددة');
            
            // الفترة الزمنية لتطبيق الحد بالدقائق
            $table->unsignedInteger('period_minutes')->comment('الفترة الزمنية بالدقائق التي يتم خلالها تطبيق الحد (مثل: 60 دقيقة)');

            // حالة الحد (نشط/غير نشط)
            $table->boolean('is_active')->default(true)->comment('حالة قاعدة الحد الأقصى للمعدل (نشط أو غير نشط)');

            // الطوابع الزمنية لإنشاء وتحديث السجل
            $table->timestamps();

            // إضافة فهرس فريد لضمان عدم تكرار قاعدة الحد لنفس الميزة والكيان
            $table->unique(['limitable_type', 'limitable_id', 'feature'], 'ai_rate_limits_unique_rule');
            
            // إضافة فهرس على عمود الميزة لتحسين البحث
            $table->index('feature');
        });
    }

    /**
     * عكس الهجرات (Rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_rate_limits');
    }
};

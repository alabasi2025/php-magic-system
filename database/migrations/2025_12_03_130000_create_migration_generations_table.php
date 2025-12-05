<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: MIGRATION_GENERATOR
 * Migration: إنشاء جدول توليد الـ migrations
 * 
 * 💡 الفكرة:
 * جدول لتخزين وإدارة عمليات توليد الـ migrations بشكل ذكي
 * يحتفظ بسجل كامل لكل migration تم توليده مع البيانات المستخدمة
 * 
 * @version 1.0.0
 * @since 2025-12-03
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('migration_generations')) {
            Schema::create('migration_generations', function (Blueprint $table) {
            $table->id();
            
            // معلومات أساسية
            $table->string('name', 255)->comment('اسم الـ migration');
            $table->text('description')->nullable()->comment('وصف الـ migration');
            $table->string('table_name', 100)->comment('اسم الجدول');
            
            // نوع العملية
            $table->string('migration_type', 50)->comment('نوع العملية: create, alter, drop');
            $table->string('input_method', 50)->comment('طريقة الإدخال: web, api, cli, json');
            
            // البيانات
            $table->jsonb('input_data')->comment('البيانات المدخلة');
            $table->text('generated_content')->comment('محتوى الـ migration المولد');
            $table->string('file_path', 500)->nullable()->comment('مسار الملف المولد');
            
            // الحالة
            $table->string('status', 50)->default('draft')->comment('الحالة: draft, generated, tested, applied');
            
            // الذكاء الاصطناعي
            $table->jsonb('ai_suggestions')->nullable()->comment('اقتراحات الذكاء الاصطناعي');
            $table->jsonb('validation_results')->nullable()->comment('نتائج التحقق');
            
            // من أنشأ وعدّل
            $table->foreignId('created_by')->nullable()->constrained('users')->comment('المستخدم الذي أنشأ');
            $table->foreignId('updated_by')->nullable()->constrained('users')->comment('المستخدم الذي عدّل');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('table_name');
            $table->index('migration_type');
            $table->index('status');
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('migration_generations');
    }
};

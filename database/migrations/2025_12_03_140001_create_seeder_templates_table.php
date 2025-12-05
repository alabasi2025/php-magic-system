<?php

/**
 * 🧬 Gene: CreateSeederTemplatesTable Migration
 * 
 * جدول لتخزين قوالب الـ Seeders الجاهزة
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Migrations
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الـ migration
     */
    public function up(): void
    {
        if (!Schema::hasTable('seeder_templates')) {
            Schema::create('seeder_templates', function (Blueprint $table) {
            // المعرف الفريد
            $table->id();
            
            // معلومات أساسية
            $table->string('name')->comment('اسم القالب');
            $table->text('description')->nullable()->comment('وصف القالب');
            $table->string('category')->default('other')->comment('فئة القالب');
            $table->string('table_name')->comment('اسم الجدول');
            $table->string('model_name')->nullable()->comment('اسم الـ Model');
            $table->integer('default_count')->default(10)->comment('العدد الافتراضي للسجلات');
            
            // بنية البيانات
            $table->json('schema')->comment('بنية البيانات (JSON Schema)');
            
            // الحالة والإحصائيات
            $table->boolean('is_active')->default(true)->comment('نشط؟');
            $table->integer('usage_count')->default(0)->comment('عدد مرات الاستخدام');
            
            // التواريخ
            $table->timestamps();
            
            // الفهارس
            $table->index('category');
            $table->index('is_active');
            $table->index('usage_count');
            $table->index('created_at');
        });
        }
    }

    /**
     * التراجع عن الـ migration
     */
    public function down(): void
    {
        Schema::dropIfExists('seeder_templates');
    }
};

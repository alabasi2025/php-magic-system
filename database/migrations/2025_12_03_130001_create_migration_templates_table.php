<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: MIGRATION_TEMPLATES
 * Migration: إنشاء جدول قوالب الـ migrations
 * 
 * 💡 الفكرة:
 * جدول لتخزين قوالب جاهزة للـ migrations يمكن إعادة استخدامها
 * يساعد في تسريع عملية التطوير وضمان الاتساق
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
        if (!Schema::hasTable('migration_templates')) {
            Schema::create('migration_templates', function (Blueprint $table) {
            $table->id();
            
            // معلومات القالب
            $table->string('name', 255)->comment('اسم القالب');
            $table->text('description')->nullable()->comment('وصف القالب');
            $table->string('category', 100)->nullable()->comment('الفئة: basic, accounting, ecommerce, etc');
            
            // محتوى القالب
            $table->text('template_content')->comment('محتوى القالب');
            $table->jsonb('variables')->nullable()->comment('المتغيرات القابلة للتخصيص');
            
            // الحالة والإحصائيات
            $table->boolean('is_active')->default(true)->comment('هل القالب نشط');
            $table->integer('usage_count')->default(0)->comment('عدد مرات الاستخدام');
            
            // من أنشأ
            $table->foreignId('created_by')->nullable()->constrained('users')->comment('المستخدم الذي أنشأ');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('category');
            $table->index('is_active');
            $table->index('usage_count');
            $table->index('created_by');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('migration_templates');
    }
};

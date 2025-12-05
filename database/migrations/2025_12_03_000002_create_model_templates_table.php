<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Model Templates Table
 * 
 * جدول قوالب الـ Models
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
        if (!Schema::hasTable('model_templates')) {
            Schema::create('model_templates', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->string('name');                          // اسم القالب
            $table->string('slug')->unique();                // Slug
            $table->text('description')->nullable();         // الوصف
            $table->string('category')->nullable();          // الفئة
            $table->string('icon')->nullable();              // الأيقونة
            
            // Template Content
            $table->longText('template_content');            // محتوى القالب
            $table->json('template_variables')->nullable();  // المتغيرات
            $table->json('placeholders')->nullable();        // Placeholders
            
            // Features
            $table->json('features')->nullable();            // الميزات المدعومة
            $table->json('default_traits')->nullable();      // Traits افتراضية
            $table->json('default_casts')->nullable();       // Casts افتراضية
            $table->json('default_relations')->nullable();   // علاقات افتراضية
            $table->json('default_scopes')->nullable();      // Scopes افتراضية
            
            // Configuration
            $table->boolean('has_timestamps')->default(true); // Timestamps
            $table->boolean('has_soft_deletes')->default(false); // Soft Deletes
            $table->boolean('generate_observer')->default(false); // توليد Observer
            $table->boolean('generate_factory')->default(false); // توليد Factory
            $table->boolean('generate_seeder')->default(false); // توليد Seeder
            $table->boolean('generate_policy')->default(false); // توليد Policy
            
            // Usage & Statistics
            $table->boolean('is_active')->default(true);     // نشط
            $table->boolean('is_default')->default(false);   // افتراضي
            $table->boolean('is_system')->default(false);    // قالب نظام
            $table->integer('usage_count')->default(0);      // عدد الاستخدامات
            $table->integer('success_count')->default(0);    // عدد النجاحات
            $table->integer('failure_count')->default(0);    // عدد الفشل
            $table->decimal('success_rate', 5, 2)->default(0); // نسبة النجاح
            
            // Ratings & Reviews
            $table->decimal('rating', 3, 2)->default(0);     // التقييم
            $table->integer('rating_count')->default(0);     // عدد التقييمات
            
            // Metadata
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('name');
            $table->index('slug');
            $table->index('category');
            $table->index('is_active');
            $table->index('is_default');
            $table->index('is_system');
            $table->index('usage_count');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_templates');
    }
};

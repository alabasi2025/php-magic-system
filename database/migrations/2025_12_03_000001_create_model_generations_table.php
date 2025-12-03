<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Model Generations Table
 * 
 * جدول سجلات توليد الـ Models
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
        Schema::create('model_generations', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->string('name');                          // اسم الـ Model
            $table->text('description')->nullable();         // الوصف
            $table->string('table_name');                    // اسم الجدول
            $table->string('namespace')->default('App\\Models'); // Namespace
            $table->string('extends')->default('Model');    // الكلاس الأب
            
            // Input Method
            $table->enum('input_method', [
                'text',      // وصف نصي
                'json',      // JSON Schema
                'database',  // قاعدة البيانات
                'migration', // Migration File
                'ai'         // AI Generation
            ]);
            $table->json('input_data')->nullable();          // البيانات المدخلة
            
            // Generated Content
            $table->longText('generated_content')->nullable(); // المحتوى المولد
            $table->json('generated_files')->nullable();      // الملفات المولدة
            $table->string('file_path')->nullable();         // مسار الملف
            
            // AI Enhancement
            $table->boolean('use_ai')->default(false);       // استخدام AI
            $table->string('ai_provider')->nullable();       // مزود AI
            $table->json('ai_suggestions')->nullable();      // اقتراحات AI
            $table->text('ai_prompt')->nullable();           // AI Prompt المستخدم
            
            // Attributes & Properties
            $table->json('attributes')->nullable();          // الخصائص
            $table->json('fillable')->nullable();            // Fillable
            $table->json('hidden')->nullable();              // Hidden
            $table->json('casts')->nullable();               // Casts
            $table->json('dates')->nullable();               // Dates
            $table->json('appends')->nullable();             // Appends
            
            // Relations & Features
            $table->json('relations')->nullable();           // العلاقات
            $table->json('scopes')->nullable();              // Scopes
            $table->json('traits')->nullable();              // Traits
            $table->json('interfaces')->nullable();          // Interfaces
            $table->json('accessors')->nullable();           // Accessors
            $table->json('mutators')->nullable();            // Mutators
            
            // Additional Features
            $table->boolean('has_timestamps')->default(true); // Timestamps
            $table->boolean('has_soft_deletes')->default(false); // Soft Deletes
            $table->boolean('has_observer')->default(false); // Observer
            $table->boolean('has_factory')->default(false);  // Factory
            $table->boolean('has_seeder')->default(false);   // Seeder
            $table->boolean('has_policy')->default(false);   // Policy
            $table->boolean('has_resource')->default(false); // API Resource
            
            // Validation & Testing
            $table->boolean('is_validated')->default(false); // تم التحقق
            $table->json('validation_results')->nullable();  // نتائج التحقق
            $table->boolean('is_tested')->default(false);    // تم الاختبار
            $table->json('test_results')->nullable();        // نتائج الاختبار
            
            // Status
            $table->enum('status', [
                'draft',      // مسودة
                'generated',  // تم التوليد
                'validated',  // تم التحقق
                'deployed',   // تم النشر
                'failed'      // فشل
            ])->default('draft');
            $table->text('error_message')->nullable();       // رسالة الخطأ
            $table->json('warnings')->nullable();            // التحذيرات
            
            // Metadata
            $table->foreignId('template_id')->nullable()->constrained('model_templates')->nullOnDelete();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('name');
            $table->index('table_name');
            $table->index('namespace');
            $table->index('input_method');
            $table->index('status');
            $table->index('use_ai');
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_generations');
    }
};

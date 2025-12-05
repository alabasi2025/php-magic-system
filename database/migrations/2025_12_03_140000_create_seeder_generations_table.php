<?php

/**
 * 🧬 Gene: CreateSeederGenerationsTable Migration
 * 
 * جدول لتخزين الـ Seeders المولدة
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
        if (!Schema::hasTable('seeder_generations')) {
            Schema::create('seeder_generations', function (Blueprint $table) {
            // المعرف الفريد
            $table->id();
            
            // معلومات أساسية
            $table->string('name')->comment('اسم الـ Seeder');
            $table->text('description')->nullable()->comment('وصف الـ Seeder');
            $table->string('table_name')->comment('اسم الجدول');
            $table->string('model_name')->nullable()->comment('اسم الـ Model');
            $table->integer('count')->default(10)->comment('عدد السجلات المراد توليدها');
            
            // معلومات الإدخال
            $table->enum('input_method', [
                'web',
                'api',
                'cli',
                'json',
                'template',
                'reverse'
            ])->default('web')->comment('طريقة الإدخال');
            $table->json('input_data')->nullable()->comment('البيانات المدخلة');
            
            // المحتوى المولد
            $table->longText('generated_content')->nullable()->comment('محتوى الـ Seeder المولد');
            
            // معلومات الذكاء الاصطناعي
            $table->boolean('use_ai')->default(false)->comment('استخدام AI للتوليد');
            $table->string('ai_provider')->nullable()->comment('مزود AI (openai, claude, gemini)');
            $table->json('ai_suggestions')->nullable()->comment('اقتراحات AI');
            
            // الحالة والتنفيذ
            $table->enum('status', [
                'draft',
                'generated',
                'tested',
                'executed',
                'failed'
            ])->default('draft')->comment('الحالة');
            $table->float('execution_time')->nullable()->comment('وقت التنفيذ بالثواني');
            $table->integer('records_created')->nullable()->comment('عدد السجلات المنشأة');
            $table->text('error_message')->nullable()->comment('رسالة الخطأ إن وجدت');
            
            // معلومات المستخدم
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('المستخدم المنشئ');
            
            // التواريخ
            $table->timestamps();
            $table->softDeletes();
            
            // الفهارس
            $table->index('table_name');
            $table->index('status');
            $table->index('input_method');
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    /**
     * التراجع عن الـ migration
     */
    public function down(): void
    {
        Schema::dropIfExists('seeder_generations');
    }
};

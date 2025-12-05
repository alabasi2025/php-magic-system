<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create Controller Generations Table Migration
 * ترحيل إنشاء جدول توليدات المتحكمات
 * 
 * This migration creates the controller_generations table for storing
 * records of controller generation operations.
 * 
 * @author Manus AI - Controller Generator v3.27.0
 * @generated 2025-12-03
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * تشغيل الترحيلات
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable('controller_generations')) {
            Schema::create('controller_generations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('اسم المتحكم');
            $table->enum('type', ['resource', 'api', 'invokable', 'custom'])
                ->default('resource')
                ->comment('نوع المتحكم');
            $table->string('model_name')->nullable()->comment('اسم Model المرتبط');
            $table->enum('input_type', ['text', 'json', 'model', 'ai'])
                ->default('text')
                ->comment('نوع المدخل');
            $table->json('input_data')->comment('بيانات المدخل');
            $table->json('generated_files')->nullable()->comment('الملفات المولدة');
            $table->json('options')->nullable()->comment('خيارات إضافية');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('معرف المستخدم');
            $table->enum('status', ['pending', 'completed', 'failed'])
                ->default('pending')
                ->comment('حالة التوليد');
            $table->text('error_message')->nullable()->comment('رسالة الخطأ');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('type');
            $table->index('model_name');
            $table->index('status');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     * عكس الترحيلات
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('controller_generations');
    }
};

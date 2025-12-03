<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: FACTORY_GENERATIONS
 * Migration: إنشاء جدول factory_generations
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
        Schema::create('factory_generations', function (Blueprint $table) {
            $table->id();

            // الأعمدة الأساسية
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('model_name');
            $table->string('table_name')->nullable();

            // بيانات الإدخال والتوليد
            $table->string('input_method')->default('web');
            $table->json('input_data')->nullable();
            $table->longText('generated_content')->nullable();
            $table->string('file_path', 1024)->nullable();

            // خيارات الذكاء الاصطناعي
            $table->boolean('use_ai')->default(false);
            $table->string('ai_provider')->nullable();

            // الحالة والأخطاء
            $table->string('status')->default('draft'); // draft, generated, saved, error
            $table->text('error_message')->nullable();

            // من أنشأ وعدّل
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factory_generations');
    }
};

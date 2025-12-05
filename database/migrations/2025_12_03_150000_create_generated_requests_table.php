<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @class CreateGeneratedRequestsTable
 *
 * @brief Migration لإنشاء جدول generated_requests.
 *
 * يقوم هذا Migration بإنشاء جدول لتخزين معلومات Form Requests المولدة،
 * بما في ذلك الاسم، النوع، الإعدادات، والكود المولد.
 *
 * Migration to create the generated_requests table.
 *
 * This migration creates a table to store information about generated Form Requests,
 * including name, type, configuration, and generated code.
 *
 * @version 3.29.0
 * @author Manus AI
 */
return new class extends Migration
{
    /**
     * @brief تشغيل Migration.
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable('generated_requests')) {
            Schema::create('generated_requests', function (Blueprint $table) {
            $table->id();
            
            // معلومات أساسية
            $table->string('name')->unique()->comment('اسم Request');
            $table->string('type')->comment('نوع Request (store, update, search, etc.)');
            $table->text('description')->nullable()->comment('وصف Request');
            
            // الإعدادات والكود
            $table->json('config')->comment('إعدادات التوليد');
            $table->longText('code')->comment('الكود المولد');
            
            // معلومات الملف
            $table->string('file_path')->nullable()->comment('مسار الملف');
            $table->integer('file_size')->nullable()->comment('حجم الملف بالبايت');
            
            // حالة Request
            $table->boolean('is_saved')->default(false)->comment('هل تم حفظ الملف؟');
            $table->boolean('is_active')->default(true)->comment('هل Request نشط؟');
            
            // معلومات المستخدم
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')
                ->comment('المستخدم الذي أنشأ Request');
            
            // إحصائيات
            $table->integer('fields_count')->default(0)->comment('عدد الحقول');
            $table->boolean('has_authorization')->default(false)->comment('يحتوي على authorization؟');
            $table->boolean('has_custom_messages')->default(false)->comment('يحتوي على رسائل مخصصة؟');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('type');
            $table->index('is_saved');
            $table->index('is_active');
            $table->index('created_at');
        });
    }

    /**
     * @brief التراجع عن Migration.
     *
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_requests');
    }
};

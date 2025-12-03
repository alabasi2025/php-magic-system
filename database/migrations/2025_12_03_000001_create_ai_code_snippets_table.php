<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الترحيلات (Migrations).
     */
    public function up(): void
    {
        // إنشاء جدول مقتطفات الكود
        Schema::create('ai_code_snippets', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي (Primary Key)
            
            // معرف المستخدم الذي أنشأ المقتطف. مطلوب استخدام unsignedBigInteger.
            $table->unsignedBigInteger('user_id'); 
            
            // لغة البرمجة (مثل: php, javascript, python).
            $table->string('language', 50); 
            
            // نص الكود الفعلي. نستخدم longText لأنه قد يكون طويلاً جداً.
            $table->longText('code_text'); 
            
            // وصف موجز للمقتطف. يمكن أن يكون فارغاً.
            $table->text('description')->nullable(); 
            
            // أعمدة timestamps (created_at و updated_at).
            $table->timestamps();

            // إضافة فهارس للأعمدة المهمة للبحث والأداء
            $table->index('user_id');
            $table->index('language');
        });
    }

    /**
     * عكس الترحيلات (Rollback).
     */
    public function down(): void
    {
        // حذف جدول مقتطفات الكود إذا كان موجوداً
        Schema::dropIfExists('ai_code_snippets');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرات (Migrations).
     */
    public function up(): void
    {
        // إنشاء جدول مفاتيح API للذكاء الاصطناعي
        Schema::create('ai_api_keys', function (Blueprint $table) {
            $table->id(); // معرف المفتاح الأساسي (Primary Key)
            
            // معرف المستخدم المرتبط بالمفتاح. يجب أن يكون unsignedBigInteger حسب القواعد.
            $table->unsignedBigInteger('user_id'); 
            
            // مزود خدمة الذكاء الاصطناعي (مثل openai, anthropic)
            $table->string('provider', 50); 
            
            // مفتاح API الفعلي. نستخدم text لأنه قد يكون طويلاً.
            $table->text('api_key'); 
            
            // حالة تفعيل المفتاح (نشط/غير نشط)، القيمة الافتراضية هي true
            $table->boolean('is_active')->default(true); 
            
            // تاريخ ووقت انتهاء صلاحية المفتاح، يمكن أن يكون فارغاً (nullable)
            $table->timestamp('expires_at')->nullable(); 
            
            $table->timestamps(); // أعمدة created_at و updated_at

            // إضافة فهارس للأعمدة المهمة لتحسين أداء الاستعلامات
            $table->index('user_id');
            $table->index('provider');
            $table->index('is_active');
        });
    }

    /**
     * عكس الهجرات (Rollback).
     */
    public function down(): void
    {
        // حذف الجدول إذا كان موجوداً
        Schema::dropIfExists('ai_api_keys');
    }
};

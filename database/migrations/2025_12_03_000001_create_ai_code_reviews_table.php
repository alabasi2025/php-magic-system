<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرات (إنشاء الجدول).
     */
    public function up(): void
    {
        Schema::create('ai_code_reviews', function (Blueprint $table) {
            // المعرف الأساسي التلقائي
            $table->id();
            
            // معرف المستخدم الذي أرسل الكود للمراجعة
            $table->unsignedBigInteger('user_id');
            
            // نص الكود الذي تمت مراجعته. نستخدم longText لأنه قد يكون كوداً طويلاً.
            $table->longText('code_text');
            
            // نتيجة المراجعة بتنسيق JSON (مثل الأخطاء المقترحة، التحسينات)
            $table->json('review_result');
            
            // تقييم جودة المراجعة (مثلاً من 1 إلى 5)
            $table->unsignedSmallInteger('rating')->nullable();
            
            // طوابع الوقت لإنشاء وتحديث السجل
            $table->timestamps();
            
            // إضافة فهارس للأعمدة التي سيتم البحث أو التصفية بها بشكل متكرر
            $table->index('user_id');
            $table->index('rating');
        });
    }

    /**
     * عكس الهجرات (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_code_reviews');
    }
};

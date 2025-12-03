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
        Schema::create('ai_nlp_generations', function (Blueprint $table) {
            // المفتاح الأساسي التلقائي
            $table->id();

            // معرف المستخدم الذي أجرى عملية التوليد
            // (يجب أن يكون unsignedBigInteger وبدون مفتاح خارجي حسب القواعد)
            $table->unsignedBigInteger('user_id'); 

            // النص المدخل (الموجه) لعملية التوليد
            $table->text('prompt'); 

            // النص الناتج عن عملية التوليد
            $table->text('generated_text'); 

            // اسم النموذج المستخدم في التوليد (مثل gpt-4)
            $table->string('model', 50); 

            // طوابع الإنشاء والتحديث
            $table->timestamps();

            // إضافة فهارس للأعمدة المهمة لتحسين أداء الاستعلامات
            $table->index('user_id');
            $table->index('model');
        });
    }

    /**
     * عكس الهجرات (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_nlp_generations');
    }
};

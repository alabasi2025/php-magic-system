<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إنشاء جدول ai_api_logs لتسجيل طلبات واستجابات API الخاصة بالذكاء الاصطناعي
        Schema::create('ai_api_logs', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي التلقائي
            $table->unsignedBigInteger('user_id')->nullable(); // معرف المستخدم الذي أجرى الطلب (قد يكون null لطلبات النظام). لا يوجد Foreign Key حسب القواعد.
            $table->string('endpoint', 255); // نقطة نهاية API التي تم استدعاؤها
            $table->json('request_data')->nullable(); // بيانات الطلب بصيغة JSON
            $table->json('response_data')->nullable(); // بيانات الاستجابة بصيغة JSON
            $table->unsignedSmallInteger('status_code'); // رمز حالة HTTP للاستجابة
            $table->timestamps(); // حقلا created_at و updated_at

            // إضافة فهارس للأعمدة المهمة للبحث والتصفية
            $table->index('user_id');
            $table->index('endpoint');
            $table->index('status_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_api_logs');
    }
};

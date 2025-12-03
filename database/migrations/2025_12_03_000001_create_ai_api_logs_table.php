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
        Schema::create('ai_api_logs', function (Blueprint $table) {
            // معرف السجل الأساسي
            $table->id();

            // معرف المستخدم الذي أجرى الطلب (بدون مفتاح خارجي)
            $table->unsignedBigInteger('user_id')->index()->comment('معرف المستخدم الذي أجرى الطلب');

            // اسم نموذج الذكاء الاصطناعي المستخدم (مثل gpt-4, claude-3)
            $table->string('model_name', 100)->index()->comment('اسم نموذج الذكاء الاصطناعي المستخدم');

            // حالة الطلب (نجاح، فشل، إلخ)
            $table->string('status', 50)->index()->comment('حالة الطلب (نجاح، فشل، إلخ)');

            // حمولة الطلب المرسلة إلى واجهة برمجة التطبيقات
            $table->json('request_payload')->nullable()->comment('حمولة الطلب المرسلة');

            // بيانات الاستجابة المستلمة من واجهة برمجة التطبيقات
            $table->json('response_payload')->nullable()->comment('بيانات الاستجابة المستلمة');

            // عدد التوكنات المستخدمة في الإدخال
            $table->integer('prompt_tokens')->default(0)->comment('عدد التوكنات المستخدمة في الإدخال');

            // عدد التوكنات المستخدمة في الإخراج
            $table->integer('completion_tokens')->default(0)->comment('عدد التوكنات المستخدمة في الإخراج');

            // إجمالي عدد التوكنات المستخدمة
            $table->integer('total_tokens')->default(0)->index()->comment('إجمالي عدد التوكنات المستخدمة');

            // زمن استجابة واجهة برمجة التطبيقات بالمللي ثانية
            $table->integer('latency_ms')->nullable()->comment('زمن استجابة واجهة برمجة التطبيقات بالمللي ثانية');

            // ختمي الوقت: created_at و updated_at
            $table->timestamps();
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

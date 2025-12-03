<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة (إنشاء الجدول).
     */
    public function up(): void
    {
        // إنشاء جدول ai_api_keys لتخزين مفاتيح API الخاصة بالذكاء الاصطناعي
        Schema::create('ai_api_keys', function (Blueprint $table) {
            // المعرف الأساسي للجدول
            $table->id();

            // المعرف الخاص بالمستخدم الذي يمتلك المفتاح (يمكن أن يكون فارغًا إذا كان مفتاحًا عامًا للنظام)
            // نستخدم foreignId لإنشاء عمود user_id وربطه بجدول المستخدمين
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained() // يفترض أن جدول المستخدمين هو 'users'
                  ->onDelete('cascade') // حذف المفاتيح عند حذف المستخدم
                  ->comment('معرف المستخدم المالك للمفتاح');

            // اسم وصفي للمفتاح، لمساعدة المستخدم على تذكره
            $table->string('name')->comment('الاسم الوصفي للمفتاح');

            // المفتاح الفعلي لـ API. يجب أن يكون فريدًا.
            // نستخدم string بطول مناسب للمفاتيح المشفرة أو الرموز المميزة.
            $table->string('key', 255)->unique()->comment('المفتاح السري لـ API');

            // مزود الخدمة (مثل 'openai', 'gemini', 'anthropic')
            $table->string('service_provider')->comment('مزود خدمة الذكاء الاصطناعي');

            // حالة المفتاح (نشط/غير نشط)
            $table->boolean('is_active')->default(true)->comment('حالة تفعيل المفتاح');

            // تاريخ انتهاء صلاحية المفتاح (يمكن أن يكون فارغًا إذا لم يكن له تاريخ انتهاء)
            $table->timestamp('expires_at')->nullable()->comment('تاريخ انتهاء صلاحية المفتاح');

            // تاريخ آخر استخدام للمفتاح
            $table->timestamp('last_used_at')->nullable()->comment('تاريخ آخر استخدام للمفتاح');

            // حقل لتسجيل حدود الاستخدام أو الميزانية المتبقية (اختياري)
            $table->unsignedBigInteger('usage_limit')->nullable()->comment('حدود الاستخدام المسموح بها');

            // الطوابع الزمنية لإنشاء وتحديث السجل
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        // حذف جدول ai_api_keys
        Schema::dropIfExists('ai_api_keys');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ترحيل لإنشاء جدول `manus_webhooks` لتخزين بيانات الـ Webhooks الواردة من Manus API.
 *
 * يتضمن الجدول الحقول المطلوبة:
 * - id: مفتاح أساسي.
 * - webhook_id: معرف الـ Webhook الفريد.
 * - event_type: نوع الحدث (مثل payment.succeeded).
 * - payload: حمولة الـ Webhook بصيغة JSON.
 * - status: حالة المعالجة (pending, processed, failed).
 * - processed_at: وقت معالجة الـ Webhook بنجاح.
 * - error_message: رسالة الخطأ في حال فشل المعالجة.
 * - timestamps: حقول created_at و updated_at.
 */
return new class extends Migration
{
    /**
     * تشغيل عمليات الترحيل (إنشاء الجدول).
     *
     * @return void
     */
    public function up(): void
    {
        // التحقق أولاً من عدم وجود الجدول لتجنب الأخطاء
        if (!Schema::hasTable('manus_webhooks')) {
            Schema::create('manus_webhooks', function (Blueprint $table) {
                // المفتاح الأساسي
                $table->id();

                // معرف الـ Webhook الفريد من Manus
                $table->string('webhook_id')->unique()->comment('معرف الـ Webhook الفريد من Manus API');

                // نوع الحدث
                $table->string('event_type')->index()->comment('نوع الحدث الذي أطلقه الـ Webhook');

                // حمولة الـ Webhook بصيغة JSON
                $table->json('payload')->comment('حمولة الـ Webhook الكاملة بصيغة JSON');

                // حالة المعالجة: pending, processed, failed
                $table->enum('status', ['pending', 'processed', 'failed'])
                      ->default('pending')
                      ->index()
                      ->comment('حالة معالجة الـ Webhook');

                // وقت معالجة الـ Webhook بنجاح (يمكن أن يكون فارغاً)
                $table->timestamp('processed_at')->nullable()->comment('وقت معالجة الـ Webhook بنجاح');

                // رسالة الخطأ في حال فشل المعالجة (يمكن أن تكون فارغة)
                $table->text('error_message')->nullable()->comment('رسالة الخطأ في حال فشل المعالجة');

                // الطوابع الزمنية (created_at و updated_at)
                $table->timestamps();
            });
        }
    }

    /**
     * عكس عمليات الترحيل (حذف الجدول).
     *
     * @return void
     */
    public function down(): void
    {
        // حذف الجدول فقط إذا كان موجوداً
        Schema::dropIfExists('manus_webhooks');
    }
};

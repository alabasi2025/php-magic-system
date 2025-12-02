<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ترحيل لإنشاء جدول `manus_transactions` لتسجيل عمليات Manus API.
 *
 * يتضمن الجدول جميع الحقول المطلوبة لتتبع تفاصيل كل عملية، بما في ذلك
 * المعرفات، الحالة، المدخلات، المخرجات، التكلفة، والأخطاء.
 */
return new class extends Migration
{
    /**
     * تشغيل الترحيل (إنشاء الجدول).
     *
     * @return void
     */
    public function up(): void
    {
        // التحقق من عدم وجود الجدول قبل الإنشاء لتجنب الأخطاء
        if (!Schema::hasTable('manus_transactions')) {
            Schema::create('manus_transactions', function (Blueprint $table) {
                // المعرف الأساسي للجدول
                $table->id();

                // معرف العملية الفريد من Manus API
                $table->uuid('transaction_id')->unique()->comment('معرف العملية الفريد من Manus API');

                // نوع العملية (مثل: chat, image_generation, speech_to_text)
                $table->enum('type', ['chat', 'image_generation', 'speech_to_text', 'data_analysis', 'other'])
                      ->comment('نوع عملية Manus API');

                // حالة العملية (مثل: pending, completed, failed)
                $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])
                      ->default('pending')
                      ->comment('حالة تنفيذ عملية Manus API');

                // المعرفات الخارجية للربط بالنظام
                // يجب التأكد من وجود جداول project, station, users في النظام
                $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null')->comment('معرف المشروع المرتبط');
                $table->foreignId('station_id')->nullable()->constrained()->onDelete('set null')->comment('معرف المحطة المرتبطة');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('معرف المستخدم الذي أجرى العملية');

                // تفاصيل المدخلات للنموذج
                $table->string('model')->comment('اسم النموذج المستخدم (مثل: gpt-4.1-mini)');
                $table->text('prompt')->comment('نص الإدخال (الطلب) المرسل للنموذج');
                $table->unsignedSmallInteger('max_tokens')->nullable()->comment('الحد الأقصى للرموز المطلوبة في الرد');
                $table->float('temperature')->nullable()->comment('قيمة درجة الحرارة المستخدمة في النموذج');

                // تفاصيل المخرجات والتكلفة
                $table->longText('response')->nullable()->comment('الرد الكامل المستلم من Manus API');
                $table->unsignedInteger('tokens_used')->default(0)->comment('عدد الرموز المستخدمة في العملية (مدخلات ومخرجات)');
                $table->decimal('cost', 8, 4)->default(0.0000)->comment('التكلفة المقدرة للعملية بالدولار الأمريكي');
                $table->unsignedInteger('duration_ms')->nullable()->comment('مدة تنفيذ العملية بالمللي ثانية');

                // معالجة الأخطاء
                $table->string('error_code')->nullable()->comment('رمز الخطأ في حال فشل العملية');
                $table->text('error_message')->nullable()->comment('رسالة الخطأ التفصيلية');

                // بيانات إضافية بصيغة JSON
                $table->json('metadata')->nullable()->comment('بيانات وصفية إضافية بصيغة JSON');

                // الطوابع الزمنية القياسية
                $table->timestamps(); // creates created_at and updated_at

                // طابع زمني لتسجيل وقت اكتمال العملية
                $table->timestamp('completed_at')->nullable()->comment('وقت اكتمال العملية بنجاح أو فشل');

                // إضافة فهارس لتحسين أداء الاستعلامات
                $table->index(['type', 'status']);
                $table->index('user_id');
                $table->index('project_id');
            });
        }
    }

    /**
     * عكس الترحيل (حذف الجدول).
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('manus_transactions');
    }
};
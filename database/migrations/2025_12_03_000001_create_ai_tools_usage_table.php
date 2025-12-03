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
        Schema::create('ai_tools_usage', function (Blueprint $table) {
            // المعرف الأساسي للجدول
            $table->id()->comment('المعرف الأساسي لسجل استخدام أداة الذكاء الاصطناعي');

            // ربط السجل بالمستخدم الذي قام بالاستخدام
            $table->foreignId('user_id')
                  ->constrained('users') // نفترض وجود جدول 'users'
                  ->cascadeOnDelete()
                  ->comment('المعرف الخاص بالمستخدم الذي استخدم الأداة');

            // اسم الأداة المستخدمة
            $table->string('tool_name', 100)->comment('اسم أداة الذكاء الاصطناعي المستخدمة (مثل GPT-4)');
            
            // معرف فريد للأداة
            $table->string('tool_slug', 100)->index()->comment('المعرف الفريد للأداة المستخدمة (للتصنيف والبحث)');

            // نوع الاستخدام
            $table->enum('usage_type', ['text_generation', 'image_creation', 'code_completion', 'data_analysis'])
                  ->comment('نوع الاستخدام (مثل توليد نص، إنشاء صورة)');

            // عدد التوكنات المدخلة
            $table->unsignedInteger('input_tokens')->default(0)->comment('عدد التوكنات المدخلة في الطلب');

            // عدد التوكنات المخرجة
            $table->unsignedInteger('output_tokens')->default(0)->comment('عدد التوكنات المخرجة في الاستجابة');

            // التكلفة المقدرة للعملية
            $table->decimal('cost', 8, 4)->default(0.0000)->comment('التكلفة المقدرة للعملية بالدولار');

            // حالة الطلب
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending')->index()->comment('حالة الطلب (ناجح، فاشل، قيد الانتظار)');

            // بيانات إضافية (مثل نسخة النموذج، رسالة الخطأ)
            $table->json('metadata')->nullable()->comment('بيانات إضافية بصيغة JSON حول الطلب والاستجابة');

            // طوابع الوقت لإنشاء وتحديث السجل
            $table->timestamps();

            // طابع الوقت للحذف الناعم
            $table->softDeletes()->comment('طابع الوقت للحذف الناعم');
        });
    }

    /**
     * عكس الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_tools_usage');
    }
};

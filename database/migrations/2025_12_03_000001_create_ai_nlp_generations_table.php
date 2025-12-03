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
        Schema::create('ai_nlp_generations', function (Blueprint $table) {
            // المعرف الأساسي للسجل
            $table->id();

            // معرف المستخدم الذي قام بالجيل (مفتاح خارجي لجدول users)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->comment('معرف المستخدم');
            
            // اسم أداة الذكاء الاصطناعي المستخدمة (مثل GPT-4, Gemini)
            $table->string('tool_name', 100)->index()->comment('اسم الأداة');

            // النص المدخل (الطلب) من المستخدم
            $table->text('prompt')->comment('نص الطلب المدخل');

            // النص الناتج (الاستجابة) من الذكاء الاصطناعي
            $table->longText('response')->comment('نص الاستجابة الناتج');

            // اسم نموذج اللغة المستخدم (مثل text-davinci-003)
            $table->string('model_used', 50)->comment('النموذج المستخدم');

            // حالة الجيل (مثل success, failed, pending)
            $table->enum('status', ['success', 'failed', 'pending', 'processing'])->index()->comment('حالة الجيل');

            // عدد التوكنات المستخدمة في الجيل
            $table->unsignedInteger('token_count')->default(0)->comment('عدد التوكنات');

            // التكلفة التقديرية للجيل
            $table->decimal('cost', 8, 4)->default(0.0000)->comment('التكلفة التقديرية');

            // بيانات إضافية (مثل إعدادات API، زمن الاستجابة)
            $table->json('metadata')->nullable()->comment('بيانات وصفية إضافية');

            // الطوابع الزمنية لإنشاء وتحديث السجل
            $table->timestamps();

            // الحذف الناعم
            $table->softDeletes()->comment('تاريخ ووقت الحذف الناعم');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_nlp_generations');
    }
};

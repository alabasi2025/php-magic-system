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
            // المفتاح الأساسي للجدول
            $table->id()->comment('المفتاح الأساسي لسجل الاستدعاء');

            // ربط السجل بالمستخدم الذي قام بالاستدعاء (إذا كان متوفراً)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users') // يفترض وجود جدول users
                  ->cascadeOnDelete()
                  ->comment('معرف المستخدم الذي قام بالاستدعاء');
            
            // اسم نموذج الذكاء الاصطناعي المستخدم
            $table->string('model_name', 100)->index()->comment('اسم نموذج الذكاء الاصطناعي المستخدم (مثل gpt-4)');

            // بيانات الطلب المرسلة إلى واجهة برمجة التطبيقات (API)
            $table->json('request_data')->comment('حمولة (Payload) الطلب المرسل إلى API');

            // بيانات الاستجابة المستلمة من واجهة برمجة التطبيقات (API)
            $table->json('response_data')->comment('بيانات الاستجابة المستلمة من API');

            // رمز حالة HTTP للاستجابة (مثل 200, 400, 500)
            $table->unsignedSmallInteger('status_code')->index()->comment('رمز حالة HTTP للاستجابة');

            // مدة الاستدعاء بالمللي ثانية
            $table->unsignedInteger('duration_ms')->comment('مدة الاستدعاء بالمللي ثانية');

            // عدد التوكنات المستخدمة في الطلب (Prompt)
            $table->unsignedInteger('prompt_tokens')->default(0)->comment('عدد التوكنات في الطلب');

            // عدد التوكنات المستخدمة في الاستجابة (Completion)
            $table->unsignedInteger('completion_tokens')->default(0)->comment('عدد التوكنات في الاستجابة');

            // إجمالي عدد التوكنات المستخدمة
            $table->unsignedInteger('total_tokens')->default(0)->comment('إجمالي عدد التوكنات المستخدمة');

            // التكلفة التقديرية للاستدعاء
            $table->decimal('cost', 8, 5)->default(0.00000)->comment('التكلفة التقديرية للاستدعاء');

            // رسالة الخطأ في حال فشل الاستدعاء
            $table->text('error_message')->nullable()->comment('رسالة الخطأ في حال فشل الاستدعاء');

            // طوابع زمنية لإنشاء وتحديث السجل
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

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
        Schema::create('ai_refactoring_history', function (Blueprint $table) {
            // المعرف الأساسي لسجل عملية إعادة الهيكلة
            $table->id();

            // معرف المستخدم الذي قام بطلب عملية إعادة الهيكلة
            // يفترض وجود جدول 'users'
            $table->foreignId('user_id')
                  ->comment('معرف المستخدم')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // المسار الأصلي للملف قبل إعادة الهيكلة
            $table->string('original_file_path', 255)
                  ->comment('المسار الأصلي للملف')
                  ->index();

            // نوع عملية إعادة الهيكلة التي قام بها الذكاء الاصطناعي
            $table->enum('refactoring_type', ['optimization', 'bug_fix', 'style_change', 'feature_addition'])
                  ->comment('نوع إعادة الهيكلة')
                  ->index();

            // حالة عملية إعادة الهيكلة
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'reverted'])
                  ->comment('حالة العملية')
                  ->default('pending')
                  ->index();

            // الكود الأصلي الذي تم إدخاله للذكاء الاصطناعي
            $table->longText('input_code')
                  ->comment('الكود الأصلي');

            // الكود الناتج بعد عملية إعادة الهيكلة
            $table->longText('output_code')
                  ->comment('الكود الناتج');

            // اسم نموذج الذكاء الاصطناعي المستخدم (مثل GPT-4)
            $table->string('ai_model', 100)
                  ->comment('نموذج الذكاء الاصطناعي');

            // تفاصيل التكلفة والرموز (tokens) المستهلكة للعملية
            $table->json('cost_details')
                  ->comment('تفاصيل التكلفة');

            // رسالة الخطأ في حال فشل العملية
            $table->text('error_message')
                  ->nullable()
                  ->comment('رسالة الخطأ');

            // تاريخ ووقت التراجع عن عملية إعادة الهيكلة
            $table->timestamp('reverted_at')
                  ->nullable()
                  ->comment('تاريخ التراجع')
                  ->index();

            // تاريخ ووقت الإنشاء والتحديث
            $table->timestamps();

            // تاريخ ووقت حذف السجل (الحذف الناعم)
            $table->softDeletes()
                  ->comment('الحذف الناعم');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_refactoring_history');
    }
};

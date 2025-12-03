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
        Schema::create('ai_conversations', function (Blueprint $table) {
            // المعرف الأساسي للدردشة
            $table->id();

            // معرف المستخدم الذي أنشأ الدردشة. مفتاح خارجي يربط بجدول 'users' مع حذف متتالي.
            $table->foreignId('user_id')
                  ->comment('معرف المستخدم')
                  ->constrained()
                  ->cascadeOnDelete();

            // عنوان موجز للدردشة (يمكن أن يكون فارغاً).
            $table->string('title', 255)->nullable()->comment('عنوان الدردشة');

            // اسم نموذج الذكاء الاصطناعي المستخدم (مثل gpt-4).
            $table->string('model', 100)->index()->comment('نموذج الذكاء الاصطناعي');

            // السياق الأولي أو تعليمات النظام للنموذج.
            $table->text('context')->nullable()->comment('سياق النظام الأولي');

            // بيانات وصفية إضافية (مثل إعدادات الجلسة، استخدام التوكن).
            $table->json('metadata')->nullable()->comment('بيانات وصفية إضافية');

            // حالة الدردشة (نشطة، مؤرشفة، مكتملة).
            $table->enum('status', ['active', 'archived', 'completed'])
                  ->default('active')
                  ->index()
                  ->comment('حالة الدردشة');

            // تاريخ الإنشاء وتاريخ آخر تحديث.
            $table->timestamps();

            // تاريخ الحذف الناعم (soft delete).
            $table->softDeletes();
        });
    }

    /**
     * عكس الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};

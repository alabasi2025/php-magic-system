<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل عمليات الترحيل (إنشاء الجدول).
     */
    public function up(): void
    {
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            // معرف الرسالة الأساسي
            $table->id()->comment('المعرف الأساسي للرسالة');

            // معرف جلسة الدردشة المرتبطة
            // يفترض وجود جدول 'ai_chat_sessions'
            $table->foreignId('chat_session_id')
                  ->constrained('ai_chat_sessions')
                  ->cascadeOnDelete()
                  ->comment('معرف جلسة الدردشة المرتبطة');

            // معرف المستخدم الذي أرسل الرسالة
            // يفترض وجود جدول 'users'
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->comment('معرف المستخدم الذي أرسل الرسالة (يمكن أن يكون فارغًا إذا كانت الرسالة من الذكاء الاصطناعي)');

            // دور مرسل الرسالة (مستخدم، مساعد، نظام)
            $table->enum('role', ['user', 'assistant', 'system'])
                  ->index()
                  ->comment('دور مرسل الرسالة (مستخدم، مساعد، نظام)');

            // محتوى الرسالة النصي
            $table->text('content')->comment('محتوى الرسالة النصي');

            // بيانات وصفية إضافية للرسالة
            $table->json('metadata')
                  ->nullable()
                  ->comment('بيانات وصفية إضافية للرسالة (مثل عدد التوكنز، النموذج المستخدم)');

            // طوابع زمنية للإنشاء والتحديث
            $table->timestamps();

            // الحذف الناعم (Soft Deletes)
            $table->softDeletes()->comment('طابع زمني للحذف الناعم');
        });
    }

    /**
     * عكس عمليات الترحيل (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_messages');
    }
};

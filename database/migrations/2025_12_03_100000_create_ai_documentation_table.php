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
        Schema::create('ai_documentation', function (Blueprint $table) {
            // العمود الأساسي (Primary Key)
            $table->id();

            // معرف المستخدم الذي أنشأ التوثيق
            // ملاحظة: لا يوجد مفتاح خارجي (Foreign Key) حسب القواعد الصارمة
            $table->unsignedBigInteger('user_id');

            // محتوى التوثيق الفعلي، يستخدم TEXT لأنه قد يكون طويلاً
            $table->text('content');

            // صيغة التوثيق (مثل: markdown, html, plain)
            $table->string('format', 50);

            // رقم إصدار التوثيق
            $table->string('version', 20);

            // أعمدة الطابع الزمني (created_at, updated_at)
            $table->timestamps();

            // إضافة فهارس للأعمدة المستخدمة بشكل متكرر في البحث والتصفية
            $table->index('user_id', 'idx_ai_doc_user_id');
            $table->index('format', 'idx_ai_doc_format');
            $table->index('version', 'idx_ai_doc_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_documentation');
    }
};
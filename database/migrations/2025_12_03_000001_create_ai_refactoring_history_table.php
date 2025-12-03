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
        // إنشاء جدول سجل إعادة الهيكلة بالذكاء الاصطناعي
        Schema::create('ai_refactoring_history', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي التلقائي
            $table->unsignedBigInteger('user_id'); // معرف المستخدم الذي أجرى عملية إعادة الهيكلة
            $table->text('original_code'); // الكود الأصلي قبل إعادة الهيكلة
            $table->text('refactored_code'); // الكود بعد إعادة الهيكلة
            $table->timestamps(); // أعمدة created_at و updated_at

            // الفهارس لسرعة الاستعلام
            $table->index('user_id'); // فهرس لسرعة البحث حسب المستخدم
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف جدول سجل إعادة الهيكلة بالذكاء الاصطناعي إذا كان موجوداً
        Schema::dropIfExists('ai_refactoring_history');
    }
};

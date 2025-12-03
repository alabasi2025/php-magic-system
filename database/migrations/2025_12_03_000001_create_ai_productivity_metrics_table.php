<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الترحيلات (Migrations).
     */
    public function up(): void
    {
        Schema::create('ai_productivity_metrics', function (Blueprint $table) {
            // معرف فريد للسجل (المفتاح الأساسي)
            $table->id();

            // معرف المستخدم الذي يخصه هذا المقياس. يجب أن يكون unsignedBigInteger حسب القواعد.
            $table->unsignedBigInteger('user_id');

            // بيانات مقاييس الإنتاجية بصيغة JSON
            $table->json('metrics');

            // تاريخ ووقت بداية الفترة التي يغطيها المقياس
            $table->timestamp('period_start');

            // تاريخ ووقت نهاية الفترة التي يغطيها المقياس
            $table->timestamp('period_end');

            // طوابع زمنية لإنشاء وتحديث السجل
            $table->timestamps();

            // إضافة فهارس للأعمدة المهمة لتحسين أداء الاستعلامات
            $table->index('user_id');
            $table->index(['period_start', 'period_end']);
        });
    }

    /**
     * عكس الترحيلات (Rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_productivity_metrics');
    }
};
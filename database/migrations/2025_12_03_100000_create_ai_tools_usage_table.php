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
        Schema::create('ai_tools_usage', function (Blueprint $table) {
            // العمود الأساسي للجدول (معرف فريد)
            $table->id();

            // معرف المستخدم الذي استخدم الأداة. يستخدم unsignedBigInteger كما هو مطلوب.
            $table->unsignedBigInteger('user_id');

            // اسم الأداة التي تم استخدامها.
            $table->string('tool_name', 100);

            // عدد مرات استخدام الأداة من قبل هذا المستخدم.
            $table->integer('usage_count')->default(0);

            // آخر وقت تم فيه استخدام الأداة.
            $table->timestamp('last_used_at')->nullable();

            // أعمدة created_at و updated_at
            $table->timestamps();

            // إضافة فهارس للأعمدة المهمة لتحسين أداء الاستعلامات
            $table->index('user_id', 'idx_ai_usage_user_id');
            $table->index('tool_name', 'idx_ai_usage_tool_name');
            
            // فهرس مركب لتحسين البحث عن استخدام أداة معينة من قبل مستخدم معين
            $table->index(['user_id', 'tool_name'], 'idx_ai_usage_user_tool');
        });
    }

    /**
     * عكس الترحيلات (Rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_tools_usage');
    }
};

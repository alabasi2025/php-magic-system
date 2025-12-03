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
        Schema::create('ai_productivity_metrics', function (Blueprint $table) {
            // المعرف الأساسي للجدول
            $table->id()->comment('المعرف الفريد للمقياس');

            // ربط المقياس بالمستخدم الذي أنشأه
            // نفترض وجود جدول 'users' موجود مسبقاً
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->comment('معرف المستخدم المرتبط بالمقياس');

            // اسم أداة الذكاء الاصطناعي المستخدمة (مثل: ChatGPT, Midjourney)
            $table->string('tool_name', 100)->index()->comment('اسم أداة الذكاء الاصطناعي');

            // نوع المقياس (مثل: usage_count, time_spent, cost)
            $table->enum('metric_type', ['usage_count', 'time_spent', 'cost', 'tasks_completed', 'quality_score'])
                  ->index()
                  ->comment('نوع المقياس المسجل');

            // القيمة العددية للمقياس
            $table->double('metric_value')->comment('القيمة العددية للمقياس');

            // وحدة المقياس (مثل: minutes, USD, tasks)
            $table->string('unit', 50)->comment('وحدة قياس القيمة');

            // بيانات سياقية إضافية بصيغة JSON (مثل تفاصيل المطالبة أو الإعدادات)
            $table->json('context_data')->nullable()->comment('بيانات سياقية إضافية');

            // الوقت الذي تم فيه تسجيل المقياس
            $table->timestamp('recorded_at')->index()->comment('تاريخ ووقت تسجيل المقياس');

            // حقول الطوابع الزمنية (created_at, updated_at)
            $table->timestamps();

            // حقل الحذف الناعم (softDeletes)
            $table->softDeletes()->comment('طابع زمني للحذف الناعم');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_productivity_metrics');
    }
};

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
        Schema::create('ai_code_reviews', function (Blueprint $table) {
            // العمود الأساسي للمفتاح الرئيسي
            $table->id()->comment('المعرف الفريد للمراجعة');

            // معرف المستخدم الذي طلب المراجعة (بدون مفتاح خارجي كما هو مطلوب)
            $table->unsignedBigInteger('user_id')->comment('معرف المستخدم الذي طلب المراجعة');
            // إضافة فهرس (index) لـ user_id لتحسين البحث
            $table->index('user_id');

            // العلاقة المتعددة الأشكال (Polymorphic) لتحديد العنصر الذي تمت مراجعته
            $table->string('reviewable_type')->comment('نوع الكيان الذي تمت مراجعته (مثل File, PullRequest)');
            $table->unsignedBigInteger('reviewable_id')->comment('معرف الكيان الذي تمت مراجعته');
            // فهرس مركب (composite index) للعلاقة المتعددة الأشكال
            $table->index(['reviewable_type', 'reviewable_id']);

            // حالة المراجعة
            $table->enum('status', ['pending', 'completed', 'failed', 'in_progress'])->default('pending')->comment('حالة المراجعة: قيد الانتظار، مكتملة، فاشلة، قيد التنفيذ');
            // فهرس على حالة المراجعة
            $table->index('status');

            // تفاصيل المراجعة
            $table->string('model_used', 100)->comment('اسم نموذج الذكاء الاصطناعي المستخدم للمراجعة');
            $table->text('source_code_snippet')->nullable()->comment('مقتطف من الكود المصدري الذي تمت مراجعته (للتخزين السريع)');
            $table->longText('full_source_code_hash')->nullable()->comment('تجزئة (Hash) للكود المصدري الكامل لتجنب التكرار');
            $table->text('review_summary')->comment('ملخص نتائج مراجعة الذكاء الاصطناعي');
            $table->json('review_details')->nullable()->comment('تفاصيل المراجعة بتنسيق JSON (مثل قائمة بالمشكلات المكتشفة)');

            // معلومات التكلفة
            $table->unsignedInteger('prompt_tokens')->default(0)->comment('عدد التوكنات المستخدمة في الإدخال (Prompt)');
            $table->unsignedInteger('completion_tokens')->default(0)->comment('عدد التوكنات المستخدمة في الإخراج (Completion)');
            $table->decimal('estimated_cost', 8, 5)->default(0.00000)->comment('التكلفة المقدرة للمراجعة بالدولار الأمريكي');

            // الطوابع الزمنية والحذف الناعم
            $table->timestamps();
            $table->softDeletes()->comment('تاريخ ووقت الحذف الناعم');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_code_reviews');
    }
};

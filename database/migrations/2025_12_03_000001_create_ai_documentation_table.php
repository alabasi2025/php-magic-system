<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل عمليات الترحيل.
     */
    public function up(): void
    {
        Schema::create('ai_documentation', function (Blueprint $table) {
            // العمود الأساسي للتعريف
            $table->id();

            // معرف المستخدم الذي أنشأ الوثيقة (بدون مفتاح خارجي حالياً)
            $table->unsignedBigInteger('user_id')->comment('معرف المستخدم الذي أنشأ الوثيقة');

            // عنوان الوثيقة
            $table->string('title', 255)->comment('عنوان الوثيقة');

            // رابط فريد للوثيقة (للعناوين الصديقة لمحركات البحث)
            $table->string('slug', 255)->unique()->comment('الرابط الفريد (Slug)');
            $table->index('slug'); // إضافة فهرس للبحث السريع

            // محتوى الوثيقة الرئيسي، يستخدم longText للسماح بمحتوى كبير
            $table->longText('content')->comment('محتوى الوثيقة الرئيسي');

            // حالة الوثيقة (مسودة، منشورة، مؤرشفة)
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->comment('حالة الوثيقة');
            $table->index('status'); // فهرس للحالة

            // نوع الوثيقة (مثل: API, Tutorial, Guide)
            $table->string('type', 50)->nullable()->comment('نوع الوثيقة');
            $table->index('type'); // فهرس للنوع

            // بيانات وصفية إضافية (مثل: إصدار النموذج، إعدادات الذكاء الاصطناعي)
            $table->json('metadata')->nullable()->comment('بيانات وصفية إضافية');

            // تاريخ ووقت النشر الفعلي
            $table->timestamp('published_at')->nullable()->comment('تاريخ ووقت النشر');
            $table->index('published_at'); // فهرس لتاريخ النشر

            // أوقات الإنشاء والتحديث
            $table->timestamps();

            // الحذف الناعم (Soft Deletes)
            $table->softDeletes()->comment('تاريخ ووقت الحذف الناعم');
        });
    }

    /**
     * التراجع عن عمليات الترحيل.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_documentation');
    }
};

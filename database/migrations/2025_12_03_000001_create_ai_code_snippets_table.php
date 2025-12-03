<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرات (Migrations).
     */
    public function up(): void
    {
        Schema::create('ai_code_snippets', function (Blueprint $table) {
            // المعرف الأساسي للجدول
            $table->id();

            // معرف المستخدم الذي أنشأ المقتطف (بدون foreign key حالياً)
            $table->unsignedBigInteger('user_id')->index();

            // عنوان أو اسم المقتطف
            $table->string('title', 255)->comment('عنوان مقتطف الكود')->index();

            // وصف مختصر للمقتطف
            $table->text('description')->nullable()->comment('وصف مختصر لوظيفة الكود');

            // الكود البرمجي الفعلي
            $table->longText('code_content')->comment('محتوى الكود البرمجي');

            // لغة البرمجة (مثل: php, js, python)
            $table->string('language', 50)->comment('لغة البرمجة المستخدمة')->index();

            // نوع المقتطف (مثل: function, class, snippet)
            $table->enum('type', ['function', 'class', 'snippet', 'config', 'other'])->default('snippet')->comment('نوع المقتطف البرمجي');

            // إعدادات إضافية بصيغة JSON
            $table->json('settings')->nullable()->comment('إعدادات إضافية بصيغة JSON');

            // حالة المقتطف (مثل: draft, published, archived)
            $table->string('status', 50)->default('draft')->comment('حالة المقتطف (مسودة، منشور، مؤرشف)')->index();

            // عدد مرات استخدام المقتطف
            $table->unsignedInteger('usage_count')->default(0)->comment('عدد مرات استخدام المقتطف');

            // الطوابع الزمنية (created_at, updated_at)
            $table->timestamps();

            // الحذف الناعم (soft delete)
            $table->softDeletes()->comment('تاريخ ووقت حذف السجل بشكل ناعم');
        });
    }

    /**
     * التراجع عن الهجرات (Migrations).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_code_snippets');
    }
};

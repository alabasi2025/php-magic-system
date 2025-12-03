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
        Schema::create('ai_code_snippets', function (Blueprint $table) {
            // المعرف الرئيسي للقصاصة البرمجية
            $table->id();

            // معرف المستخدم الذي أنشأ القصاصة، مع مفتاح خارجي وحذف متتالي
            $table->foreignId('user_id')
                  ->comment('معرف المستخدم')
                  ->constrained()
                  ->onDelete('cascade');

            // عنوان موجز للقصاصة البرمجية
            $table->string('title', 255)->comment('عنوان القصاصة')->index();

            // وصف تفصيلي للقصاصة البرمجية
            $table->text('description')->nullable()->comment('وصف القصاصة');

            // لغة البرمجة المستخدمة (مثل PHP, JavaScript, Python)
            $table->string('language', 50)->comment('لغة البرمجة')->index();

            // الكود البرمجي الفعلي، يستخدم longText لاستيعاب الأكواد الطويلة
            $table->longText('code_snippet')->comment('القصاصة البرمجية');

            // اسم نموذج الذكاء الاصطناعي المستخدم في الإنشاء
            $table->string('model_used', 100)->comment('نموذج الذكاء الاصطناعي');

            // حالة القصاصة (مثل draft, published, archived)
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->comment('حالة القصاصة');

            // هل القصاصة متاحة للعامة؟
            $table->boolean('is_public')->default(false)->comment('متاحة للعامة')->index();

            // إضافة created_at و updated_at
            $table->timestamps();

            // إضافة softDeletes للحذف المنطقي
            $table->softDeletes()->comment('تاريخ الحذف المنطقي');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_code_snippets');
    }
};

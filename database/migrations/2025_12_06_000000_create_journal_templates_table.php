<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة - إنشاء جدول قوالب القيود اليومية
     */
    public function up(): void
    {
        Schema::create('journal_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم القالب
            $table->text('description')->nullable(); // وصف القالب
            $table->string('category')->nullable(); // فئة القالب (شراء، بيع، راتب، إلخ)
            $table->json('template_data'); // بيانات القالب (الحسابات، المبالغ، إلخ)
            $table->boolean('is_active')->default(true); // هل القالب نشط؟
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // من أنشأ القالب
            $table->timestamps();
            $table->softDeletes(); // حذف ناعم
        });
    }

    /**
     * التراجع عن الهجرة - حذف الجدول
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_templates');
    }
};

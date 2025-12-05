<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // إنشاء جدول قوالب القيود اليومية الذكية
        Schema::create('journal_entry_templates', function (Blueprint $table) {
            $table->id();
            // اسم القالب
            $table->string('name')->comment('اسم القالب');
            // وصف اختياري للقالب
            $table->text('description')->nullable()->comment('وصف القالب');
            // بيانات القيد المخزنة بصيغة JSON (مثل الحسابات والمبالغ)
            // هذه البيانات ستحتوي على هيكل القيد اليومي (الحسابات، المبالغ، الوصف)
            $table->json('template_data')->comment('بيانات القيد المخزنة بصيغة JSON');
            // مفتاح خارجي للمستخدم الذي أنشأ القالب
            // نفترض وجود جدول 'users'
            $table->foreignId('user_id')->constrained('users')->comment('المستخدم الذي أنشأ القالب');
            // حالة تفعيل القالب
            $table->boolean('is_active')->default(true)->comment('حالة تفعيل القالب');
            $table->timestamps();
            // دعم الحذف الناعم (Soft Deletes)
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_templates');
    }
};

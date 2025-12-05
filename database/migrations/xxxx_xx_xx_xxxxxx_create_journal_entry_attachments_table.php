<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة (إنشاء الجدول).
     */
    public function up(): void
    {
        Schema::create('journal_entry_attachments', function (Blueprint $table) {
            $table->id();
            // ربط المرفق بجدول قيود اليومية (JournalEntry)
            $table->foreignId('journal_entry_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('معرف قيد اليومية المرتبط');

            $table->string('file_name')->comment('الاسم الأصلي للملف');
            $table->string('file_path')->comment('المسار التخزيني للملف');
            $table->string('file_type', 50)->comment('نوع الملف (MIME Type)');
            $table->unsignedBigInteger('file_size')->comment('حجم الملف بالبايت');

            // ربط المرفق بالمستخدم الذي قام بالرفع
            $table->foreignId('uploaded_by')
                  ->nullable() // قد يكون الرفع آلياً
                  ->constrained('users') // نفترض وجود جدول users
                  ->onDelete('set null')
                  ->comment('معرف المستخدم الذي قام بالرفع');

            $table->timestamps();

            // إضافة فهرس لتحسين أداء الاستعلامات على القيد
            $table->index('journal_entry_id');
        });
    }

    /**
     * التراجع عن الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_attachments');
    }
};

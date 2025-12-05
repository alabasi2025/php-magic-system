<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة (إنشاء جدول الفئات).
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            // مفتاح خارجي يشير إلى الفئة الأب (للهيكلية الهرمية)
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->string('name')->unique()->comment('اسم الفئة');
            $table->text('description')->nullable()->comment('وصف الفئة');
            $table->boolean('is_active')->default(true)->comment('حالة تفعيل الفئة');
            $table->timestamps();

            // إضافة فهرس لتحسين أداء الاستعلامات على parent_id
            $table->index('parent_id');
        });
    }

    /**
     * عكس الهجرة (حذف جدول الفئات).
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

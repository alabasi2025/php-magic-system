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
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            // ربط البحث المحفوظ بالمستخدم الذي أنشأه (نفترض وجود جدول users)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // اسم البحث المحفوظ
            $table->string('name');
            // معايير البحث المخزنة كـ JSON
            $table->json('criteria');
            $table->timestamps();

            // إضافة فهرس لسرعة البحث
            $table->index('user_id');
        });
    }

    /**
     * عكس الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};

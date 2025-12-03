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
        Schema::create('ai_design_conversions', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي التلقائي
            
            // معرف المستخدم الذي قام بالتحويل
            // ملاحظة: تم استخدام unsignedBigInteger بدون foreign key بناءً على القواعد الصارمة
            $table->unsignedBigInteger('user_id'); 
            
            // رابط التصميم الأصلي (قد يكون رابط Figma أو صورة)
            $table->string('design_url', 2048); 
            
            // الكود الناتج عن عملية التحويل (HTML, CSS, JS, إلخ)
            $table->longText('generated_code'); 
            
            // إطار العمل المستخدم في التحويل (مثل React, Vue, HTML/CSS)
            $table->string('framework', 50); 
            
            $table->timestamps(); // أعمدة وقت الإنشاء والتحديث

            // إضافة فهارس لتحسين أداء الاستعلامات على الأعمدة المهمة
            $table->index('user_id');
            $table->index('framework');
        });
    }

    /**
     * عكس الهجرات (Rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_design_conversions');
    }
};

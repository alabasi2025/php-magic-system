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
        Schema::create('ai_performance_reports', function (Blueprint $table) {
            // المعرف الأساسي التلقائي
            $table->id();
            
            // معرف المستخدم الذي يخصه التقرير (بدون مفتاح خارجي حسب الطلب)
            $table->unsignedBigInteger('user_id');
            
            // بيانات التقرير التفصيلية بصيغة JSON
            $table->json('report_data');
            
            // درجة الأداء (يمكن أن تكون قيمة عشرية)
            $table->float('score')->nullable();
            
            // طوابع الوقت (created_at و updated_at)
            $table->timestamps();
            
            // إضافة فهارس للأعمدة المهمة لتحسين الأداء
            $table->index('user_id');
            $table->index('score');
        });
    }

    /**
     * عكس الهجرات (Rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_performance_reports');
    }
};
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
        Schema::create('ai_user_preferences', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي التلقائي
            
            // معرف المستخدم المرتبط، بدون قيد مفتاح خارجي حسب التعليمات. يجب أن يكون فريدًا لكل مستخدم.
            $table->unsignedBigInteger('user_id')->unique(); 
            
            // تفضيلات المستخدم المخزنة بصيغة JSON (مثل إعدادات الواجهة، الإشعارات، إلخ)
            $table->json('preferences')->nullable(); 
            
            // تفضيل المظهر (مثل: dark, light)
            $table->string('theme', 50)->default('light'); 
            
            // تفضيل اللغة (مثل: ar, en)
            $table->string('language', 10)->default('en'); 
            
            // طوابع الوقت لإنشاء وتحديث السجل
            $table->timestamps(); 

            // إضافة فهارس لتحسين أداء الاستعلامات
            $table->index('user_id', 'idx_ai_user_preferences_user_id');
            $table->index('theme', 'idx_ai_user_preferences_theme');
            $table->index('language', 'idx_ai_user_preferences_language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_user_preferences');
    }
};

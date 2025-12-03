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
        // إنشاء جدول خطط المشاريع ai_project_plans
        Schema::create('ai_project_plans', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي التلقائي (Primary Key)
            
            // معرف المستخدم الذي أنشأ الخطة، يستخدم unsignedBigInteger حسب القاعدة
            $table->unsignedBigInteger('user_id'); 
            
            // بيانات الخطة بصيغة JSON، لتخزين تفاصيل الخطة المرنة
            $table->json('plan_data'); 
            
            // حالة الخطة، مع قيمة افتراضية 'pending'
            $table->string('status', 50)->default('pending'); 
            
            // أولوية الخطة، رقم صغير غير سالب، مع قيمة افتراضية 0
            $table->unsignedSmallInteger('priority')->default(0); 
            
            // حقلا created_at و updated_at لتتبع وقت الإنشاء والتحديث
            $table->timestamps(); 

            // إضافة فهارس للأعمدة المهمة لتحسين أداء الاستعلامات
            $table->index('user_id');
            $table->index('status');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف الجدول في حال التراجع عن الهجرة
        Schema::dropIfExists('ai_project_plans');
    }
};

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
        Schema::create('ai_test_cases', function (Blueprint $table) {
            $table->id(); // المعرف الأساسي للجدول
            
            // معرف المستخدم الذي أنشأ حالة الاختبار. يجب أن يكون unsignedBigInteger حسب القواعد.
            $table->unsignedBigInteger('user_id'); 
            
            // كود الاختبار (نص طويل)
            $table->text('test_code'); 
            
            // نتائج الاختبار المخزنة بصيغة JSON، يمكن أن تكون فارغة
            $table->json('test_results')->nullable(); 
            
            $table->timestamps(); // أعمدة الإنشاء والتحديث (created_at, updated_at)
            
            // إضافة فهرس (index) لعمود user_id لتحسين أداء الاستعلامات
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_test_cases');
    }
};

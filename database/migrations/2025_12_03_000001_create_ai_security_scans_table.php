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
        Schema::create('ai_security_scans', function (Blueprint $table) {
            $table->id(); // معرف الفحص الأساسي (Primary Key)
            
            // معرف المستخدم الذي أجرى الفحص
            $table->unsignedBigInteger('user_id'); 
            
            // نتائج الفحص بالتفصيل (بصيغة JSON)
            $table->json('scan_results'); 
            
            // مستوى خطورة الفحص (مثل: low, medium, high, critical)
            $table->string('severity', 20); 
            
            $table->timestamps(); // أعمدة تاريخ الإنشاء والتحديث

            // إضافة فهارس للأعمدة المهمة لسرعة الاستعلام
            $table->index('user_id');
            $table->index('severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_security_scans');
    }
};

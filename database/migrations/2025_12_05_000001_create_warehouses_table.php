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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            // اسم المخزن، يجب أن يكون فريداً
            $table->string('name')->unique()->comment('اسم المخزن');
            // موقع المخزن (اختياري)
            $table->string('location')->nullable()->comment('موقع المخزن الجغرافي');
            // حالة المخزن (نشط/غير نشط)
            $table->boolean('is_active')->default(true)->comment('حالة نشاط المخزن');
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};

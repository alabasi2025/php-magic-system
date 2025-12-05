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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            // نوع التنبيه: 'low_stock', 'expired', 'system'
            $table->string('type')->comment('نوع التنبيه');
            // رسالة التنبيه
            $table->text('message')->comment('نص رسالة التنبيه');
            // مفتاح خارجي للمنتج (اختياري، إذا كان التنبيه خاصاً بمنتج)
            $table->foreignId('product_id')->nullable()->constrained()->comment('المنتج المرتبط بالتنبيه');
            // حالة التنبيه (تم الحل/لم يتم الحل)
            $table->boolean('is_resolved')->default(false)->comment('هل تم حل التنبيه');
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};

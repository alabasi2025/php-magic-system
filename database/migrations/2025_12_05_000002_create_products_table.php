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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // رمز المنتج التعريفي (Stock Keeping Unit)
            $table->string('sku')->unique()->comment('رمز المنتج التعريفي');
            // اسم المنتج
            $table->string('name')->comment('اسم المنتج');
            // وصف المنتج
            $table->text('description')->nullable()->comment('وصف تفصيلي للمنتج');
            // الكمية الحالية في المخزون (افتراضياً 0)
            $table->unsignedInteger('current_stock')->default(0)->comment('الكمية الإجمالية الحالية في المخزون');
            // الحد الأدنى لمستوى المخزون لإطلاق تنبيه
            $table->unsignedInteger('min_stock_level')->default(10)->comment('الحد الأدنى للمخزون');
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة (حذف الجدول).
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

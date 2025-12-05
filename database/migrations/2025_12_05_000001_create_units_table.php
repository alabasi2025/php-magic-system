<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة (إنشاء جدول الوحدات).
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('اسم الوحدة (مثل: كيلوغرام)');
            $table->string('symbol', 10)->unique()->comment('رمز الوحدة (مثل: كجم)');
            $table->boolean('is_base_unit')->default(false)->comment('هل هي وحدة أساسية؟');
            // مفتاح خارجي يشير إلى الوحدة الأساسية التي تشتق منها هذه الوحدة
            $table->foreignId('base_unit_id')->nullable()->constrained('units')->onDelete('restrict');
            // معامل التحويل إلى الوحدة الأساسية (مثال: 1000 لتحويل 1 كجم إلى 1000 جم)
            $table->decimal('conversion_factor', 10, 4)->default(1.0)->comment('معامل التحويل إلى الوحدة الأساسية');
            $table->timestamps();

            // إضافة فهرس لتحسين أداء الاستعلامات على base_unit_id
            $table->index('base_unit_id');
        });
    }

    /**
     * عكس الهجرة (حذف جدول الوحدات).
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};

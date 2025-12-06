<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة - إنشاء جدول إعدادات الترقيم التلقائي
     */
    public function up(): void
    {
        Schema::create('auto_numbering_settings', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // نوع الكيان (journal_entry, invoice, etc.)
            $table->string('prefix')->nullable(); // البادئة (JE-, INV-, etc.)
            $table->string('pattern'); // النمط ({PREFIX}{YEAR}{MONTH}{NUMBER})
            $table->integer('padding')->default(4); // عدد الأصفار (0001, 00001)
            $table->integer('current_number')->default(0); // الرقم الحالي
            $table->boolean('reset_yearly')->default(false); // إعادة تعيين سنوياً
            $table->boolean('reset_monthly')->default(false); // إعادة تعيين شهرياً
            $table->boolean('is_active')->default(true); // هل النظام نشط
            $table->timestamps();
        });
    }

    /**
     * التراجع عن الهجرة
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_numbering_settings');
    }
};

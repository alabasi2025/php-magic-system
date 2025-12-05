<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة (إنشاء جدول الموردين).
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('اسم المورد');
            $table->string('contact_person')->nullable()->comment('اسم شخص الاتصال');
            $table->string('phone')->unique()->comment('رقم الهاتف');
            $table->string('email')->unique()->nullable()->comment('البريد الإلكتروني');
            $table->text('address')->nullable()->comment('العنوان التفصيلي');
            $table->decimal('initial_balance', 10, 2)->default(0)->comment('الرصيد الافتتاحي');
            $table->decimal('balance', 10, 2)->default(0)->comment('الرصيد الحالي المحسوب');
            $table->boolean('is_active')->default(true)->comment('حالة التفعيل');
            // مفتاح خارجي للمستخدم الذي أنشأ السجل
            $table->foreignId('user_id')->constrained('users')->comment('المستخدم المنشئ');
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة (حذف جدول الموردين).
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};

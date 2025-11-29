<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'cashier_shifts' table.
 *
 * This table is part of the Cashiers Gene and is responsible for tracking
 * the start and end times of a cashier's work shift, along with the
 * initial and final cash amounts for accountability.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // إنشاء جدول 'cashier_shifts' لتسجيل فترات عمل الصرافين
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            
            // معرف الصراف (المستخدم) الذي قام بفتح الوردية
            // نفترض وجود جدول 'users' أو 'employees'
            $table->foreignId('user_id')
                  ->constrained('users') // ربط مع جدول المستخدمين
                  ->comment('معرف الصراف (المستخدم) الذي قام بفتح الوردية');

            // وقت بدء الوردية
            $table->timestamp('start_time')
                  ->useCurrent()
                  ->comment('وقت بدء الوردية');

            // وقت انتهاء الوردية (يمكن أن يكون فارغًا إذا كانت الوردية مفتوحة)
            $table->timestamp('end_time')
                  ->nullable()
                  ->comment('وقت انتهاء الوردية');

            // المبلغ النقدي الأولي عند بدء الوردية
            $table->decimal('initial_cash', 10, 2)
                  ->default(0.00)
                  ->comment('المبلغ النقدي الأولي عند بدء الوردية');

            // المبلغ النقدي النهائي عند إغلاق الوردية
            $table->decimal('final_cash', 10, 2)
                  ->nullable()
                  ->comment('المبلغ النقدي النهائي عند إغلاق الوردية');

            // حالة الوردية (مفتوحة/مغلقة)
            $table->enum('status', ['open', 'closed'])
                  ->default('open')
                  ->comment('حالة الوردية: مفتوحة (open) أو مغلقة (closed)');

            // إضافة حقول التوقيتات القياسية (created_at, updated_at)
            $table->timestamps();

            // إضافة فهرس لـ user_id لتحسين أداء الاستعلامات
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // حذف الجدول عند التراجع عن الهجرة
        Schema::dropIfExists('cashier_shifts');
    }
};
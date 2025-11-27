<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Migration: إنشاء جدول الحسابات الوسيطة (بدون Foreign Keys)
 * 
 * 💡 الفكرة:
 * جدول يربط الحسابات الرئيسية بحساباتها الوسيطة.
 * كل حساب يمكن أن يكون له حتى 3 حسابات وسيطة.
 * 
 * ⚠️ ملاحظة: تم إزالة Foreign Keys مؤقتاً لأن جدول accounts غير موجود بعد.
 * سيتم إضافتها لاحقاً عند إنشاء نظام المحاسبة الكامل.
 * 
 * @version 1.0.0
 * @since 2025-11-27
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('intermediate_accounts', function (Blueprint $table) {
            $table->id();
            
            // الحساب الرئيسي
            $table->unsignedBigInteger('main_account_id')
                ->comment('الحساب الرئيسي');
            
            // الحساب الوسيط الأول (إلزامي)
            $table->unsignedBigInteger('intermediate_account_1_id')
                ->comment('الحساب الوسيط الأول (إلزامي)');
            
            // الحساب الوسيط الثاني (اختياري)
            $table->unsignedBigInteger('intermediate_account_2_id')
                ->nullable()
                ->comment('الحساب الوسيط الثاني (اختياري)');
            
            // الحساب الوسيط الثالث (اختياري)
            $table->unsignedBigInteger('intermediate_account_3_id')
                ->nullable()
                ->comment('الحساب الوسيط الثالث (اختياري)');
            
            // الحالة
            $table->enum('status', ['active', 'inactive'])
                ->default('active')
                ->comment('الحالة: نشط أو غير نشط');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات إضافية');
            
            // التواريخ
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('main_account_id');
            $table->index('intermediate_account_1_id');
            $table->index('intermediate_account_2_id');
            $table->index('intermediate_account_3_id');
            $table->index('status');
            
            // Unique constraint
            $table->unique('main_account_id', 'unique_main_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intermediate_accounts');
    }
};

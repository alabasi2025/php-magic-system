<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Migration: إنشاء جدول الحسابات الوسيطة
 * 
 * 💡 الفكرة:
 * جدول يربط الحسابات الرئيسية بحساباتها الوسيطة.
 * كل حساب يمكن أن يكون له حتى 3 حسابات وسيطة.
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
            $table->foreignId('main_account_id')
                ->constrained('accounts')
                ->onDelete('cascade')
                ->comment('الحساب الرئيسي');
            
            // الحساب الوسيط الأول (إلزامي)
            $table->foreignId('intermediate_account_1_id')
                ->constrained('accounts')
                ->onDelete('cascade')
                ->comment('الحساب الوسيط الأول (إلزامي)');
            
            // الحساب الوسيط الثاني (اختياري)
            $table->foreignId('intermediate_account_2_id')
                ->nullable()
                ->constrained('accounts')
                ->onDelete('cascade')
                ->comment('الحساب الوسيط الثاني (اختياري)');
            
            // الحساب الوسيط الثالث (اختياري)
            $table->foreignId('intermediate_account_3_id')
                ->nullable()
                ->constrained('accounts')
                ->onDelete('cascade')
                ->comment('الحساب الوسيط الثالث (اختياري)');
            
            // الحالة
            $table->enum('status', ['active', 'inactive'])
                ->default('active')
                ->comment('حالة الحساب الوسيط');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            // من أنشأ وعدّل
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('main_account_id');
            $table->index('intermediate_account_1_id');
            $table->index('intermediate_account_2_id');
            $table->index('intermediate_account_3_id');
            $table->index('status');
            
            // Unique constraint: كل حساب رئيسي له إعداد واحد فقط
            $table->unique('main_account_id');
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

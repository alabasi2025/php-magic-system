<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Migration: إنشاء جدول معاملات الحسابات الوسيطة
 * 
 * 💡 الفكرة:
 * جدول يحتوي على جميع المعاملات (قبوضات ومدفوعات) للحسابات الوسيطة
 * 
 * @version 1.0.0
 * @since 2025-12-02
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('alabasi_intermediate_transactions');
        Schema::create('alabasi_intermediate_transactions', function (Blueprint $table) {
            $table->id();
            
            // الحساب الوسيط
            $table->unsignedBigInteger('intermediate_account_id')->comment('الحساب الوسيط');
            
            // نوع المعاملة
            $table->enum('type', ['receipt', 'payment'])->comment('نوع المعاملة: قبض أو دفع');
            
            // المبالغ
            $table->decimal('amount', 15, 2)->comment('المبلغ الإجمالي');
            $table->decimal('available_amount', 15, 2)->comment('المبلغ المتاح للربط');
            
            // معلومات المعاملة
            $table->string('reference_number', 100)->comment('رقم المرجع');
            $table->date('transaction_date')->comment('تاريخ المعاملة');
            $table->text('description')->nullable()->comment('وصف المعاملة');
            
            // الحالة
            $table->enum('status', ['pending', 'completed', 'cancelled'])
                ->default('pending')
                ->comment('حالة المعاملة');
            
            $table->boolean('is_transferred')->default(false)->comment('هل تم الترحيل');
            
            // من أنشأ وعدّل
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('intermediate_account_id');
            $table->index('type');
            $table->index('status');
            $table->index('transaction_date');
            $table->index('is_transferred');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alabasi_intermediate_transactions');
    }
};

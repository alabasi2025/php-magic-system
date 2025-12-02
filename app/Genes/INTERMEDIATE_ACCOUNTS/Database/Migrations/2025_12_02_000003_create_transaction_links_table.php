<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Migration: إنشاء جدول ربط المعاملات
 * 
 * 💡 الفكرة:
 * جدول يربط القبوضات بالمدفوعات في الحسابات الوسيطة
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
        Schema::create('transaction_links', function (Blueprint $table) {
            $table->id();
            
            // معاملة القبض
            $table->foreignId('receipt_transaction_id')
                ->constrained('intermediate_transactions')
                ->onDelete('cascade')
                ->comment('معاملة القبض');
            
            // معاملة الدفع
            $table->foreignId('payment_transaction_id')
                ->constrained('intermediate_transactions')
                ->onDelete('cascade')
                ->comment('معاملة الدفع');
            
            // المبلغ المربوط
            $table->decimal('linked_amount', 15, 2)->comment('المبلغ المربوط');
            
            // تاريخ الربط
            $table->date('link_date')->comment('تاريخ الربط');
            
            // من أنشأ وعدّل
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('receipt_transaction_id');
            $table->index('payment_transaction_id');
            $table->index('link_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_links');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Migration: إنشاء جدول ربط العمليات المعاكسة
 * 
 * 💡 الفكرة:
 * جدول يربط العمليات في الحساب الوسيط ببعضها البعض.
 * يمكن ربط عملية واحدة بعدة عمليات معاكسة، والعكس.
 * المهم أن يكون المجموع متساوياً.
 * 
 * 🎯 القاعدة الذهبية:
 * مجموع المبالغ المرتبطة يجب أن يكون متساوياً تماماً
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
        Schema::create('transaction_links', function (Blueprint $table) {
            $table->id();
            
            // العملية المصدر (قبض أو صرف)
            $table->foreignId('source_transaction_id')
                ->constrained('intermediate_transactions')
                ->onDelete('cascade')
                ->comment('العملية المصدر');
            
            // العملية الهدف (العملية المعاكسة)
            $table->foreignId('target_transaction_id')
                ->constrained('intermediate_transactions')
                ->onDelete('cascade')
                ->comment('العملية الهدف (المعاكسة)');
            
            // المبلغ المرتبط
            $table->decimal('linked_amount', 15, 2)
                ->comment('المبلغ المرتبط من هذه العملية');
            
            // تاريخ الربط
            $table->timestamp('linked_at')
                ->useCurrent()
                ->comment('تاريخ الربط');
            
            // من قام بالربط
            $table->foreignId('linked_by')
                ->nullable()
                ->constrained('users')
                ->comment('من قام بالربط');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            $table->timestamps();
            
            // Indexes
            $table->index('source_transaction_id');
            $table->index('target_transaction_id');
            $table->index(['source_transaction_id', 'target_transaction_id']);
            
            // Unique constraint: لا يمكن ربط نفس العمليتين مرتين
            $table->unique(['source_transaction_id', 'target_transaction_id']);
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

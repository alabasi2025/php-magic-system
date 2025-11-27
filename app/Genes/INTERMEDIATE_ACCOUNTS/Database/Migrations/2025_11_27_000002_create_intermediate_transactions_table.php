<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Migration: إنشاء جدول العمليات في الحسابات الوسيطة
 * 
 * 💡 الفكرة:
 * جدول يحفظ جميع العمليات (قبض/صرف) التي تدخل الحساب الوسيط.
 * كل عملية يمكن أن تكون مرتبطة بعمليات معاكسة.
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
        Schema::create('intermediate_transactions', function (Blueprint $table) {
            $table->id();
            
            // الحساب الوسيط
            $table->foreignId('intermediate_account_id')
                ->constrained('intermediate_accounts')
                ->onDelete('cascade')
                ->comment('الحساب الوسيط');
            
            // نوع العملية
            $table->enum('type', ['receipt', 'payment'])
                ->comment('نوع العملية: قبض أو صرف');
            
            // رقم السند
            $table->string('voucher_number', 50)
                ->comment('رقم السند');
            
            // من/إلى
            $table->string('from_to', 255)
                ->comment('من (للقبض) أو إلى (للصرف)');
            
            // الحساب المصدر/الهدف
            $table->foreignId('source_target_account_id')
                ->nullable()
                ->constrained('accounts')
                ->onDelete('set null')
                ->comment('الحساب المصدر (للقبض) أو الهدف (للصرف)');
            
            // المبلغ
            $table->decimal('amount', 15, 2)
                ->comment('المبلغ');
            
            // التاريخ
            $table->date('transaction_date')
                ->comment('تاريخ العملية');
            
            // البيان
            $table->text('description')
                ->comment('بيان العملية');
            
            // الحالة
            $table->enum('status', ['pending', 'linked', 'transferred'])
                ->default('pending')
                ->comment('حالة العملية: معلقة، مرتبطة، مرحّلة');
            
            // هل تم الترحيل إلى الحساب الرئيسي؟
            $table->boolean('is_transferred')
                ->default(false)
                ->comment('هل تم الترحيل إلى الحساب الرئيسي؟');
            
            // تاريخ الترحيل
            $table->timestamp('transferred_at')
                ->nullable()
                ->comment('تاريخ الترحيل');
            
            // من قام بالترحيل
            $table->foreignId('transferred_by')
                ->nullable()
                ->constrained('users')
                ->comment('من قام بالترحيل');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            // من أنشأ وعدّل
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('intermediate_account_id');
            $table->index('type');
            $table->index('voucher_number');
            $table->index('transaction_date');
            $table->index('status');
            $table->index('is_transferred');
            $table->index(['intermediate_account_id', 'status']);
            $table->index(['intermediate_account_id', 'is_transferred']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intermediate_transactions');
    }
};

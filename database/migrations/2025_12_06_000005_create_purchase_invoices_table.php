<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for Purchase Invoices and Items Tables
 * جدول فواتير الموردين وأصنافها
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
        // جدول فواتير الموردين
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->comment('رقم الفاتورة من المورد');
            $table->string('internal_number')->unique()->comment('رقم داخلي');
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete()->comment('أمر الشراء');
            $table->foreignId('purchase_receipt_id')->nullable()->constrained('purchase_receipts')->nullOnDelete()->comment('استلام البضاعة');
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete()->comment('المورد');
            $table->date('invoice_date')->comment('تاريخ الفاتورة');
            $table->date('due_date')->nullable()->comment('تاريخ الاستحقاق');
            $table->decimal('subtotal', 15, 2)->default(0)->comment('المجموع الفرعي');
            $table->decimal('tax_amount', 15, 2)->default(0)->comment('قيمة الضريبة');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('قيمة الخصم');
            $table->decimal('total_amount', 15, 2)->default(0)->comment('المجموع الكلي');
            $table->decimal('paid_amount', 15, 2)->default(0)->comment('المبلغ المدفوع');
            $table->decimal('remaining_amount', 15, 2)->default(0)->comment('المبلغ المتبقي');
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid')->comment('حالة الدفع');
            $table->enum('status', ['draft', 'approved', 'cancelled'])->default('draft')->comment('الحالة');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->comment('المستخدم المنشئ');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->comment('المستخدم المعتمد');
            $table->timestamp('approved_at')->nullable()->comment('تاريخ الاعتماد');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete()->comment('القيد المحاسبي');
            
            // للتوافق مع النظام القديم
            $table->string('name')->nullable()->comment('الاسم');
            $table->text('description')->nullable()->comment('الوصف');
            $table->boolean('is_active')->default(true)->nullable()->comment('حالة النشاط');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('invoice_number');
            $table->index('internal_number');
            $table->index('supplier_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('invoice_date');
            $table->index('due_date');
        });

        // جدول أصناف فواتير الموردين
        Schema::create('purchase_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained('purchase_invoices')->cascadeOnDelete()->comment('فاتورة المشتريات');
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete()->comment('الصنف');
            $table->decimal('quantity', 15, 2)->comment('الكمية');
            $table->decimal('unit_price', 15, 2)->comment('سعر الوحدة');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('نسبة الضريبة');
            $table->decimal('discount_rate', 5, 2)->default(0)->comment('نسبة الخصم');
            $table->decimal('total_amount', 15, 2)->comment('المجموع');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('purchase_invoice_id');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_items');
        Schema::dropIfExists('purchase_invoices');
    }
};

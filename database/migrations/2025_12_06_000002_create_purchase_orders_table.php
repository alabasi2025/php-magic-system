<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for Purchase Orders Table
 * جدول أوامر الشراء
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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->comment('رقم الأمر');
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete()->comment('المورد');
            $table->date('order_date')->comment('تاريخ الأمر');
            $table->date('expected_date')->nullable()->comment('تاريخ التسليم المتوقع');
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete()->comment('المخزن المستلم');
            $table->decimal('subtotal', 15, 2)->default(0)->comment('المجموع الفرعي');
            $table->decimal('tax_amount', 15, 2)->default(0)->comment('قيمة الضريبة');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('قيمة الخصم');
            $table->decimal('total_amount', 15, 2)->default(0)->comment('المجموع الكلي');
            $table->enum('status', ['draft', 'sent', 'confirmed', 'partially_received', 'received', 'cancelled'])->default('draft')->comment('الحالة');
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid')->comment('حالة الدفع');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->comment('المستخدم المنشئ');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->comment('المستخدم المعتمد');
            $table->timestamp('approved_at')->nullable()->comment('تاريخ الاعتماد');
            
            // للتوافق مع النظام القديم
            $table->string('name')->nullable()->comment('الاسم');
            $table->text('description')->nullable()->comment('الوصف');
            $table->boolean('is_active')->default(true)->nullable()->comment('حالة النشاط');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('order_number');
            $table->index('supplier_id');
            $table->index('warehouse_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('order_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};

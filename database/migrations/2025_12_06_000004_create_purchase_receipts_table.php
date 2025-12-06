<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for Purchase Receipts and Items Tables
 * جدول استلام البضاعة وأصنافها
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
        // جدول استلام البضاعة
        Schema::create('purchase_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique()->comment('رقم الاستلام');
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete()->comment('أمر الشراء');
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete()->comment('المورد');
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete()->comment('المخزن');
            $table->date('receipt_date')->comment('تاريخ الاستلام');
            $table->string('reference_number')->nullable()->comment('رقم المرجع من المورد');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('الحالة');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->comment('المستخدم المنشئ');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->comment('المستخدم المعتمد');
            $table->timestamp('approved_at')->nullable()->comment('تاريخ الاعتماد');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('receipt_number');
            $table->index('supplier_id');
            $table->index('warehouse_id');
            $table->index('status');
            $table->index('receipt_date');
        });

        // جدول أصناف استلام البضاعة
        Schema::create('purchase_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_receipt_id')->constrained('purchase_receipts')->cascadeOnDelete()->comment('استلام البضاعة');
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete()->comment('الصنف');
            $table->decimal('quantity', 15, 2)->comment('الكمية المستلمة');
            $table->decimal('unit_price', 15, 2)->comment('سعر الوحدة');
            $table->decimal('total_amount', 15, 2)->comment('المجموع');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('purchase_receipt_id');
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
        Schema::dropIfExists('purchase_receipt_items');
        Schema::dropIfExists('purchase_receipts');
    }
};

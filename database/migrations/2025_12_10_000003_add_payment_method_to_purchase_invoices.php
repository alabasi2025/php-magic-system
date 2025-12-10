<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add payment_method to purchase_invoices table
 * إضافة حقل طريقة الدفع لجدول فواتير المشتريات
 * 
 * @version 5.7.1
 * @date 2025-12-10
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
        Schema::table('purchase_invoices', function (Blueprint $table) {
            // إضافة عمود طريقة الدفع بعد warehouse_id
            $table->enum('payment_method', ['cash', 'credit', 'bank_transfer', 'check'])
                ->default('cash')
                ->after('warehouse_id')
                ->comment('طريقة الدفع');
            
            // إضافة index للأداء
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropIndex(['payment_method']);
            $table->dropColumn('payment_method');
        });
    }
};

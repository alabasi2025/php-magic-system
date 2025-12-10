<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add warehouse_id to purchase_invoices table
 * إضافة حقل المخزن لجدول فواتير المشتريات
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
            // إضافة عمود المخزن بعد supplier_id
            $table->foreignId('warehouse_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained('warehouses')
                ->restrictOnDelete()
                ->comment('المخزن المستهدف');
            
            // إضافة index للأداء
            $table->index('warehouse_id');
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
            $table->dropForeign(['warehouse_id']);
            $table->dropIndex(['warehouse_id']);
            $table->dropColumn('warehouse_id');
        });
    }
};

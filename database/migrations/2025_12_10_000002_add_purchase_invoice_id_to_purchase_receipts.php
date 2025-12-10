<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_receipts', function (Blueprint $table) {
            // إضافة عمود فاتورة الشراء
            $table->foreignId('purchase_invoice_id')->nullable()->after('purchase_order_id')->constrained('purchase_invoices')->onDelete('cascade');
            
            // جعل purchase_order_id اختياري (nullable)
            $table->foreignId('purchase_order_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_receipts', function (Blueprint $table) {
            $table->dropForeign(['purchase_invoice_id']);
            $table->dropColumn('purchase_invoice_id');
        });
    }
};

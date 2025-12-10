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
        Schema::create('purchase_invoice_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('اسم نوع الفاتورة');
            $table->string('code')->unique()->comment('رمز النوع للترقيم');
            $table->string('prefix')->comment('بادئة الترقيم');
            $table->text('description')->nullable()->comment('وصف النوع');
            $table->boolean('is_active')->default(true)->comment('حالة التفعيل');
            $table->integer('last_number')->default(0)->comment('آخر رقم مستخدم');
            $table->timestamps();
        });

        // إضافة عمود invoice_type_id إلى جدول purchase_invoices
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->foreignId('invoice_type_id')->nullable()->after('id')->constrained('purchase_invoice_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropForeign(['invoice_type_id']);
            $table->dropColumn('invoice_type_id');
        });
        
        Schema::dropIfExists('purchase_invoice_types');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة.
     */
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->comment('معرف الصنف');
            $table->enum('type', ['in', 'out'])->comment('نوع الحركة: دخول أو خروج');
            $table->decimal('quantity', 10, 2)->comment('الكمية');
            $table->timestamp('transaction_date')->comment('تاريخ الحركة');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};

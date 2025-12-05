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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->comment('معرف الصنف');
            $table->decimal('quantity', 10, 2)->comment('الكمية المباعة');
            $table->decimal('unit_price', 10, 2)->comment('سعر الوحدة');
            $table->decimal('total_price', 10, 2)->comment('إجمالي سعر البيع');
            $table->timestamp('sale_date')->comment('تاريخ البيع');
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

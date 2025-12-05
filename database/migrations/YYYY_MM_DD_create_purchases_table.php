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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->comment('معرف الصنف');
            $table->decimal('quantity', 10, 2)->comment('الكمية المشتراة');
            $table->decimal('unit_price', 10, 2)->comment('سعر الوحدة');
            $table->decimal('total_price', 10, 2)->comment('إجمالي سعر الشراء');
            $table->timestamp('purchase_date')->comment('تاريخ الشراء');
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

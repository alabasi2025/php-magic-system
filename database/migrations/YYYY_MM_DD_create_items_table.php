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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('اسم الصنف');
            $table->decimal('current_stock', 10, 2)->default(0)->comment('الرصيد الحالي للمخزون');
            $table->decimal('min_stock_level', 10, 2)->default(0)->comment('الحد الأدنى للمخزون');
            $table->decimal('cost_price', 10, 2)->default(0)->comment('سعر التكلفة لتقييم المخزون');
            $table->timestamps();
        });
    }

    /**
     * عكس الهجرة.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

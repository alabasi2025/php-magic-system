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
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            // مفتاح خارجي للمخزن، نفترض وجود جدول warehouses
            $table->foreignId('warehouse_id')->constrained('warehouses')->comment('معرف المخزن');
            // مفتاح خارجي للصنف، نفترض وجود جدول items
            $table->foreignId('item_id')->constrained('items')->comment('معرف الصنف');
            $table->decimal('quantity', 15, 4)->default(0)->comment('الكمية الحالية في المخزن');
            $table->decimal('last_cost', 15, 4)->default(0)->comment('آخر تكلفة شراء مسجلة');
            $table->decimal('average_cost', 15, 4)->default(0)->comment('متوسط التكلفة المحسوب');
            $table->decimal('total_value', 15, 4)->default(0)->comment('القيمة الإجمالية للرصيد (الكمية * متوسط التكلفة)');
            $table->timestamp('last_updated')->useCurrent()->comment('آخر تحديث للرصيد');
            $table->timestamps();

            // ضمان عدم تكرار الصنف في نفس المخزن
            $table->unique(['warehouse_id', 'item_id']);
        });
    }

    /**
     * التراجع عن الهجرة.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
    }
};

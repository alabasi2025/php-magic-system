<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرات (الإنشاء).
     */
    public function up(): void
    {
        Schema::create('stock_count_details', function (Blueprint $table) {
            $table->id();
            // ربط التفصيل بعملية الجرد
            $table->foreignId('stock_count_id')->constrained('stock_counts')->onDelete('cascade')->comment('مرجع لعملية الجرد');
            // ربط التفصيل بالصنف (نفترض وجود جدول items)
            $table->foreignId('item_id')->constrained('items')->comment('الصنف الذي تم جرده');
            // الكمية المسجلة في النظام وقت إنشاء الجرد
            $table->decimal('system_quantity', 10, 2)->comment('الكمية المسجلة في النظام');
            // الكمية الفعلية التي تم عدها
            $table->decimal('actual_quantity', 10, 2)->nullable()->comment('الكمية الفعلية التي تم عدها');
            // الفرق بين الكمية الفعلية وكمية النظام
            $table->decimal('difference', 10, 2)->default(0)->comment('الفرق (الكمية الفعلية - كمية النظام)');
            // ملاحظات خاصة بهذا الصنف في عملية الجرد
            $table->text('notes')->nullable()->comment('ملاحظات على الصنف');
            $table->timestamps();

            // ضمان عدم تكرار الصنف في نفس عملية الجرد
            $table->unique(['stock_count_id', 'item_id']);
        });
    }

    /**
     * عكس الهجرات (الحذف).
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_count_details');
    }
};

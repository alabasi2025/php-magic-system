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
        Schema::create('item_unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete()->comment('الصنف');
            $table->foreignId('unit_id')->constrained('item_units')->restrictOnDelete()->comment('الوحدة');
            $table->decimal('capacity', 15, 4)->default(1)->comment('السعة بالنسبة للوحدة الرئيسية');
            $table->boolean('is_primary')->default(false)->comment('هل هذه الوحدة الرئيسية للصرف؟');
            $table->decimal('price', 15, 2)->nullable()->comment('سعر هذه الوحدة (اختياري)');
            $table->integer('sort_order')->default(0)->comment('ترتيب العرض');
            $table->timestamps();

            // Indexes
            $table->index(['item_id', 'unit_id']);
            $table->index(['item_id', 'is_primary']);
            
            // Unique constraint: لا يمكن تكرار نفس الوحدة للصنف الواحد
            $table->unique(['item_id', 'unit_id'], 'item_unit_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_unit_conversions');
    }
};

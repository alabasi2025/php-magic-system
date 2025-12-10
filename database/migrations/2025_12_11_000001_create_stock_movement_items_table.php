<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: إنشاء جدول تفاصيل الحركات المخزنية
 * 
 * 💡 الفكرة:
 * جدول لتخزين تفاصيل الأصناف في كل حركة مخزنية
 * يسمح بإضافة عدة أصناف في أمر واحد
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movement_items', function (Blueprint $table) {
            $table->id();
            
            // الحركة المخزنية
            $table->unsignedBigInteger('stock_movement_id')->comment('رقم الحركة المخزنية');
            
            // الصنف
            $table->unsignedBigInteger('item_id')->comment('الصنف');
            
            // الكميات
            $table->decimal('quantity', 15, 3)->comment('الكمية');
            $table->string('unit', 50)->nullable()->comment('الوحدة');
            
            // التكاليف
            $table->decimal('unit_cost', 15, 2)->default(0)->comment('تكلفة الوحدة');
            $table->decimal('total_cost', 15, 2)->default(0)->comment('التكلفة الإجمالية');
            
            // معلومات إضافية
            $table->string('batch_number', 100)->nullable()->comment('رقم الدفعة');
            $table->date('expiry_date')->nullable()->comment('تاريخ الانتهاء');
            $table->text('notes')->nullable()->comment('ملاحظات');
            
            $table->timestamps();
            
            // Indexes
            $table->index('stock_movement_id');
            $table->index('item_id');
            $table->index('batch_number');
            
            // Foreign Keys
            $table->foreign('stock_movement_id')
                  ->references('id')
                  ->on('stock_movements')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movement_items');
    }
};

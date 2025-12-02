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
        Schema::create('alabasi_cash_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('اسم الصندوق/الخزنة');
            $table->string('code', 50)->unique()->comment('رمز الصندوق/الخزنة');
            $table->decimal('balance', 15, 2)->default(0)->comment('الرصيد الحالي للصندوق');
            $table->boolean('is_active')->default(true)->comment('حالة الصندوق (نشط/غير نشط)');
            $table->text('description')->nullable()->comment('وصف إضافي للصندوق');

            // Tracking Columns
            $table->unsignedBigInteger('created_by')->nullable()->comment('المستخدم الذي أنشأ السجل');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('المستخدم الذي حدث السجل');

            // Timestamps and Soft Deletes
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('is_active');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alabasi_cash_boxes');
    }
};

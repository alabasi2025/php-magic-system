<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الترحيل - إنشاء جدول الميزانيات
     */
    public function up(): void
    {
        Schema::create('alabasi_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('fiscal_year', 4);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->timestamps();

            // الفهارس
            $table->index('fiscal_year');
            $table->index('status');
            $table->index('created_by');
        });
    }

    /**
     * التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('alabasi_budgets');
    }
};

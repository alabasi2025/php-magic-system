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
        Schema::dropIfExists('alabasi_profit_calculations');
        Schema::create('alabasi_profit_calculations', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Khumaasiyya (البنية الخماسية) - Core Business Keys
            $table->unsignedBigInteger('holding_id')->nullable()->comment('معرف الهولدنج');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('معرف الوحدة');
            $table->unsignedBigInteger('project_id')->nullable()->comment('معرف المشروع');
            $table->unsignedBigInteger('partner_id')->comment('معرف الشريك');

            // Calculation Data
            $table->date('calculation_date')->comment('تاريخ احتساب الأرباح');
            $table->decimal('profit_amount', 15, 4)->comment('مبلغ الأرباح المحتسبة');
            $table->text('notes')->nullable()->comment('ملاحظات إضافية');

            // Auditing (التدقيق)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Timestamps and Soft Deletes
            $table->timestamps();
            $table->softDeletes();

            // Indices (الفهارس) - For faster lookups
            $table->index(['holding_id', 'unit_id', 'project_id', 'partner_id'], 'profit_calc_khumasiyya_index');
            $table->index('calculation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alabasi_profit_calculations');
    }
};

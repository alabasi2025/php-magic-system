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
        Schema::create('profit_calculations', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Khumaasiyya (البنية الخماسية) - Core Business Keys
            // holding_id
            $table->foreignId('holding_id')->constrained('holdings')->onDelete('cascade');
            // unit_id
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            // project_id
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            // partner_id - Added as a logical key for PARTNERSHIP_ACCOUNTING
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');

            // Calculation Data
            $table->date('calculation_date')->comment('تاريخ احتساب الأرباح');
            $table->decimal('profit_amount', 15, 4)->comment('مبلغ الأرباح المحتسبة');
            $table->text('notes')->nullable()->comment('ملاحظات إضافية');

            // Auditing (التدقيق)
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

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
        Schema::dropIfExists('profit_calculations');
    }
};

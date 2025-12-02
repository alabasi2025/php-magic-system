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
        Schema::create('alabasi_simple_revenues', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Quinary Structure (Foreign Keys: holding_id, unit_id, project_id)
            $table->unsignedBigInteger('holding_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();

            // Specific Columns for simple_revenues
            $table->decimal('amount', 15, 2)->comment('مبلغ الإيراد');
            $table->date('revenue_date')->comment('تاريخ الإيراد');
            $table->string('description', 500)->nullable()->comment('وصف الإيراد');
            $table->string('status', 50)->default('draft')->comment('حالة الإيراد: draft, posted, cancelled');

            // Auditing Columns (created_by, updated_by)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Timestamps and Soft Deletes
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['holding_id', 'unit_id', 'project_id'], 'simple_revenues_quinary_index');
            $table->index('revenue_date');
            $table->index('status');

            // Foreign Key Constraints (Assuming standard table names: holdings, units, projects, users)
            $table->foreign('holding_id')->references('id')->on('holdings')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alabasi_simple_revenues');
    }
};

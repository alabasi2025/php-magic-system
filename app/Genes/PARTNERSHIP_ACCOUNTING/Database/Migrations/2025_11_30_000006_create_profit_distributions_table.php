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
        Schema::create('profit_distributions', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Quinary Structure Foreign Keys (البنية الخماسية)
            $table->unsignedBigInteger('holding_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('project_id');

            // Table Specific Columns
            $table->unsignedBigInteger('partner_id')->comment('The partner receiving the distribution');
            $table->decimal('amount', 15, 4)->comment('The distributed profit amount');
            $table->date('distribution_date')->comment('The date the profit was distributed');
            $table->string('status', 50)->default('pending')->comment('Status of the distribution (e.g., pending, paid, cancelled)');
            $table->text('notes')->nullable();

            // Auditing (للتدقيق)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Timestamps and Soft Deletes
            $table->timestamps();
            $table->softDeletes(); // Soft Deletes

            // Foreign Keys
            $table->foreign('holding_id')->references('id')->on('holdings')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes (الفهارس المناسبة)
            $table->index(['holding_id', 'unit_id', 'project_id'], 'profit_distributions_quinary_index');
            $table->index('partner_id');
            $table->index('distribution_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_distributions');
    }
};

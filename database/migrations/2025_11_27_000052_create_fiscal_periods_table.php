<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'fiscal_periods' table.
 * This table stores information about specific fiscal periods within a fiscal year.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('fiscal_periods', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'fiscal_years' table
            // Assuming a 'fiscal_years' table exists with an 'id' column.
            $table->foreignId('fiscal_year_id')
                  ->constrained('fiscal_years')
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // Prevent deletion of a fiscal year if periods exist
                  ->comment('The fiscal year this period belongs to.');

            // Period details
            $table->string('name', 100)->comment('Name of the fiscal period (e.g., Q1, Month 1).');
            $table->date('start_date')->comment('The start date of the fiscal period.');
            $table->date('end_date')->comment('The end date of the fiscal period.');

            // Status flag
            $table->boolean('is_closed')->default(false)->comment('Indicates if the period is closed for transactions.');

            // Timestamps
            $table->timestamps();

            // Indexes for faster lookups
            $table->index('start_date');
            $table->index('end_date');
            $table->unique(['fiscal_year_id', 'name'], 'fiscal_period_unique_name');
            $table->unique(['fiscal_year_id', 'start_date', 'end_date'], 'fiscal_period_unique_dates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('fiscal_periods');
    }
};

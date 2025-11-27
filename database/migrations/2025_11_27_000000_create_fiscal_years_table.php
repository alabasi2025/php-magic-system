<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating and dropping the 'fiscal_years' table.
 * This table stores information about different fiscal periods.
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
        // Create the 'fiscal_years' table
        Schema::create('fiscal_years', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Fiscal Year Name (e.g., "FY 2025-2026")
            $table->string('name', 100)->unique()->comment('Unique name for the fiscal year.');

            // Start and End Dates of the Fiscal Year
            $table->date('start_date')->comment('The start date of the fiscal year.');
            $table->date('end_date')->comment('The end date of the fiscal year.');

            // Constraint to ensure start_date is before end_date
            // Note: This is a database-level constraint, but application-level validation is also crucial.
            // $table->check('start_date < end_date'); // Not directly supported by all DB drivers in Laravel schema builder

            // Flag to indicate if the fiscal year is closed for transactions
            $table->boolean('is_closed')->default(false)->comment('Indicates if the fiscal year is closed for any new transactions.');

            // Status of the Fiscal Year: active, pending, closed
            // 'active': The current fiscal year.
            // 'pending': A future fiscal year.
            // 'closed': A past and finalized fiscal year.
            $table->enum('status', ['active', 'pending', 'closed'])
                  ->default('pending')
                  ->comment('The operational status of the fiscal year.');

            // Indexes for performance
            $table->index(['start_date', 'end_date']);

            // Timestamps (created_at and updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'fiscal_years' table if it exists
        Schema::dropIfExists('fiscal_years');
    }
};
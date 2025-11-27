<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'cost_center_allocations' table.
 * This table is used to allocate a percentage of an account's value
 * to a specific cost center.
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
        Schema::create('cost_center_allocations', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'cost_centers' table
            // Assuming 'cost_centers' table exists and has an 'id' column
            $table->foreignId('cost_center_id')
                  ->constrained('cost_centers')
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('The cost center to which the allocation is made.');

            // Foreign Key to the 'accounts' table
            // Assuming 'accounts' table exists and has an 'id' column
            $table->foreignId('account_id')
                  ->constrained('accounts')
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('The account being allocated.');

            // Allocation percentage (e.g., 10.50 for 10.50%)
            // Using decimal for precise financial calculations. Total digits 5, decimal places 2.
            $table->decimal('percentage', 5, 2)
                  ->comment('The percentage of the account allocated to the cost center (e.g., 10.50).');

            // Ensure a cost center can only be allocated to an account once
            $table->unique(['cost_center_id', 'account_id'], 'cost_center_account_unique');

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
        Schema::dropIfExists('cost_center_allocations');
    }
};

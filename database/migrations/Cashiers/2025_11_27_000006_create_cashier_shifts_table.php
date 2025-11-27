<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'cashier_shifts' table.
 * This table is part of the Cashiers Gene and manages the shifts of cashiers.
 *
 * Task ID: 2006
 * Category: Database
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
        // Check if the table already exists to prevent errors during re-runs
        if (!Schema::hasTable('cashier_shifts')) {
            Schema::create('cashier_shifts', function (Blueprint $table) {
                // Primary Key
                $table->id();

                // Foreign Key to the user (cashier) who opened the shift
                // Assuming a 'users' table exists and the cashier is a user.
                $table->foreignId('user_id')
                      ->comment('The ID of the cashier (user) who opened the shift.')
                      ->constrained('users')
                      ->onUpdate('cascade')
                      ->onDelete('restrict');

                // Shift Status (e.g., 'open', 'closed', 'suspended')
                $table->string('status', 20)
                      ->default('open')
                      ->comment('Current status of the shift (open, closed, suspended).');

                // Shift Timestamps
                $table->timestamp('opened_at')
                      ->comment('The time the shift was opened.');
                $table->timestamp('closed_at')
                      ->nullable()
                      ->comment('The time the shift was closed.');

                // Starting and Ending Cash Balances
                $table->decimal('opening_balance', 10, 2)
                      ->default(0.00)
                      ->comment('The cash amount at the start of the shift.');
                $table->decimal('closing_balance', 10, 2)
                      ->nullable()
                      ->comment('The calculated cash amount at the end of the shift.');

                // System-calculated total sales for the shift
                $table->decimal('total_sales', 10, 2)
                      ->default(0.00)
                      ->comment('Total sales amount recorded during this shift.');

                // Notes or comments about the shift
                $table->text('notes')
                      ->nullable()
                      ->comment('Any notes or comments related to the shift.');

                // Gene Architecture Timestamps
                $table->timestamps();
                $table->softDeletes(); // For soft deletion capability

                // Indexes for performance
                $table->index(['user_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'cashier_shifts' table.
 * This table stores information about the shifts worked by cashiers.
 *
 * @category Database
 * @package  CashiersGene
 * @author   Manus
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
        Schema::create('cashier_shifts', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the user (cashier) who opened the shift
            // Assuming 'users' table exists and 'id' is the primary key
            $table->foreignId('user_id')
                  ->comment('The ID of the user (cashier) who opened the shift.')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Shift Status (e.g., 'open', 'closed', 'suspended')
            $table->string('status', 20)
                  ->default('open')
                  ->comment('The current status of the shift (e.g., open, closed).');

            // Shift Start and End Times
            $table->timestamp('start_time')
                  ->comment('The time the shift was opened.');
            $table->timestamp('end_time')
                  ->nullable()
                  ->comment('The time the shift was closed.');

            // Initial Cash Balance (Starting float)
            $table->decimal('opening_balance', 10, 2)
                  ->default(0.00)
                  ->comment('The amount of cash in the till at the start of the shift.');

            // Final Cash Balance (Closing amount)
            $table->decimal('closing_balance', 10, 2)
                  ->nullable()
                  ->comment('The amount of cash in the till at the end of the shift.');

            // Total Sales during the shift
            $table->decimal('total_sales', 10, 2)
                  ->default(0.00)
                  ->comment('The total value of sales recorded during the shift.');

            // Total Cash Payments received
            $table->decimal('cash_payments', 10, 2)
                  ->default(0.00)
                  ->comment('The total cash payments received during the shift.');

            // Total Card Payments received
            $table->decimal('card_payments', 10, 2)
                  ->default(0.00)
                  ->comment('The total card payments received during the shift.');

            // Total Expenses/Payouts made from the till
            $table->decimal('payouts', 10, 2)
                  ->default(0.00)
                  ->comment('The total amount of payouts/expenses made from the till.');

            // Notes or comments related to the shift
            $table->text('notes')
                  ->nullable()
                  ->comment('Any notes or comments regarding the shift.');

            // Timestamps for creation and update
            $table->timestamps();

            // Indexes for faster lookups
            $table->index('user_id');
            $table->index('status');
        });
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
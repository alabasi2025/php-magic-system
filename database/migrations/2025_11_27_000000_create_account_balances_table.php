<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'account_balances' table.
 * This table stores the periodic balances for each account, linking to
 * the specific account and the fiscal period.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_balances', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'accounts' table
            // This links the balance record to a specific account in the Chart of Accounts.
            // Assuming 'accounts' table exists and has an 'id' primary key.
            $table->foreignId('account_id')
                  ->constrained('accounts')
                  ->onUpdate('cascade')
                  ->onDelete('cascade'); // If an account is deleted, its balances should also be removed.

            // Foreign Key to the 'fiscal_periods' table
            // This links the balance record to a specific fiscal period (e.g., month, quarter, year).
            // Assuming 'fiscal_periods' table exists and has an 'id' primary key.
            $table->foreignId('fiscal_period_id')
                  ->constrained('fiscal_periods')
                  ->onUpdate('cascade')
                  ->onDelete('cascade'); // If a fiscal period is deleted, its balances should also be removed.

            // Financial columns for balance tracking. Precision set to 15 total digits, 4 after the decimal point.
            // Using decimal for precise financial calculations as required for financial data.
            $table->decimal('opening_balance', 15, 4)->default(0.0000)->comment('Balance at the start of the fiscal period.');
            $table->decimal('debit', 15, 4)->default(0.0000)->comment('Total debit transactions during the period.');
            $table->decimal('credit', 15, 4)->default(0.0000)->comment('Total credit transactions during the period.');
            $table->decimal('closing_balance', 15, 4)->default(0.0000)->comment('Balance at the end of the fiscal period.');

            // Ensure a unique balance record per account and fiscal period.
            $table->unique(['account_id', 'fiscal_period_id']);

            // Timestamps for creation and last update.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_balances');
    }
};
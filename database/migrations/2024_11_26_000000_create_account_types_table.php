<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the 'account_types' table.
 * This table stores the different types of accounts in the system,
 * such as Asset, Liability, Equity, Revenue, and Expense.
 * Each type is defined by a name, a unique code, and its nature (Debit or Credit).
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
        
        // Create the 'account_types' table
        Schema::create('account_types', function (Blueprint $table) {
            // Primary key ID
            $table->id();

            // Account type name (e.g., 'Asset', 'Liability')
            $table->string('name', 100)->unique()->comment('The human-readable name of the account type.');

            // Unique code for the account type (e.g., 'AST', 'LBT')
            // This can be used for internal logic or quick lookups.
            $table->string('code', 10)->unique()->comment('A short, unique code for the account type.');

            // The nature of the account type, which determines its default balance behavior.
            // 'debit' for accounts that increase with a debit (e.g., Assets, Expenses).
            // 'credit' for accounts that increase with a credit (e.g., Liabilities, Equity, Revenue).
            $table->enum('nature', ['debit', 'credit'])->comment('The natural balance of the account type (debit or credit).');

            // Timestamps for creation and last update
            $table->timestamps();

            // Add an index on the 'nature' column for faster lookups
            $table->index('nature');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'account_types' table if the migration is rolled back
    }
};
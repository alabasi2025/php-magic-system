<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the journal_entry_lines table.
 * This table stores the individual debit and credit entries for a journal entry.
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
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the main journal entry
            // Assuming 'journal_entries' table exists
            $table->foreignId('journal_entry_id')
                  ->constrained('journal_entries')
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('Reference to the parent journal entry.');

            // Foreign Key to the account being debited or credited
            // Assuming 'accounts' table exists
            $table->foreignId('account_id')
                  ->constrained('accounts')
                  ->onUpdate('cascade')
                  ->onDelete('restrict')
                  ->comment('Reference to the general ledger account.');

            // Debit amount (using precision for financial data)
            // Common practice is to use a high precision like (15, 4) for high precision
            $table->decimal('debit', 15, 4)->default(0.0000)->comment('The debit amount for the line item.');

            // Credit amount (using precision for financial data)
            $table->decimal('credit', 15, 4)->default(0.0000)->comment('The credit amount for the line item.');

            // Description for the specific line item
            $table->string('description', 255)->comment('Detailed description for the line item.');

            // Foreign Key to the cost center (nullable)
            // Assuming 'cost_centers' table exists
            $table->foreignId('cost_center_id')
                  ->nullable()
                  ->constrained('cost_centers')
                  ->onUpdate('cascade')
                  ->onDelete('set null')
                  ->comment('Reference to the cost center, if applicable.');

            // Timestamps
            $table->timestamps();

            // Add indexes for faster lookups
            $table->index(['journal_entry_id', 'account_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
    }
};
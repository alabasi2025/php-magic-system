<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'cashier_transactions' table.
 *
 * This table stores all financial transactions related to cashiers,
 * such as deposits, withdrawals, and transfers, within the Cashiers Gene.
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
        // Check if the table already exists to prevent errors in case of partial migration
        if (!Schema::hasTable('cashier_transactions')) {
            Schema::create('cashier_transactions', function (Blueprint $table) {
                // Primary Key and Identification
                $table->id();
                
                // Foreign Key to the cashier (user) who performed the transaction
                // Assuming 'cashiers' are a subset of 'users' or a dedicated 'cashiers' table exists.
                // For Gene Architecture, we link to the main 'users' table for simplicity and consistency.
                $table->foreignId('cashier_id')
                      ->comment('The ID of the cashier (user) who performed the transaction.')
                      ->constrained('users') // Assuming 'users' table exists
                      ->onUpdate('cascade')
                      ->onDelete('restrict');

                // Transaction Details
                $table->string('transaction_type', 50)
                      ->comment('Type of transaction (e.g., deposit, withdrawal, transfer_in, transfer_out).');
                
                $table->decimal('amount', 15, 4)
                      ->comment('The amount of the transaction.');
                
                $table->string('currency', 10)
                      ->default('SAR')
                      ->comment('The currency of the transaction.');

                // Status and Reference
                $table->string('status', 20)
                      ->default('pending')
                      ->comment('Status of the transaction (e.g., pending, completed, failed, reversed).');
                
                $table->string('reference_number', 100)
                      ->unique()
                      ->nullable()
                      ->comment('Unique reference number for the transaction.');

                // Related Entities (Polymorphic relationship for flexibility)
                // This allows linking the transaction to various other entities (e.g., orders, invoices)
                $table->morphs('transactable');

                // Timestamps and Soft Deletes
                $table->timestamps();
                $table->softDeletes();

                // Indexes for performance
                $table->index(['cashier_id', 'transaction_type']);
                $table->index('status');
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
        Schema::dropIfExists('cashier_transactions');
    }
};
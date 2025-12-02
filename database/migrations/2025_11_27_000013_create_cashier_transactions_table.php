<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the cashier_transactions table.
 *
 * This table stores all financial transactions related to cashiers,
 * including deposits, withdrawals, and other movements of funds.
 *
 * @category Database
 * @package  CashiersGene
 * @author   Manus AI
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
        // Check if the table already exists to prevent errors in case of manual execution
        if (!Schema::hasTable('cashier_transactions')) {
            Schema::create('cashier_transactions', function (Blueprint $table) {
                // Primary Key and Identification
                $table->id();
                $table->uuid('uuid')->unique(); // Unique identifier for the transaction

                // Relationships
                // Link to the cashier (user) who performed the transaction
                $table->foreignId('cashier_id')
                      ->constrained('users') // Assuming 'users' table holds cashier data
                      ->onDelete('cascade');

                // Link to the related account (e.g., bank account, safe)
                $table->foreignId('account_id')
                      ->nullable()
                      ->constrained('accounts') // Assuming an 'accounts' table exists
                      ->onDelete('set null');

                // Transaction Details
                $table->string('transaction_type', 50); // e.g., 'deposit', 'withdrawal', 'transfer', 'adjustment'
                $table->decimal('amount', 15, 2); // Transaction amount
                $table->string('currency', 3)->default('SAR'); // Currency code (e.g., SAR, USD)
                $table->decimal('balance_before', 15, 2)->nullable(); // Balance before the transaction
                $table->decimal('balance_after', 15, 2)->nullable(); // Balance after the transaction

                // Status and Metadata
                $table->string('status', 20)->default('completed'); // e.g., 'completed', 'pending', 'failed', 'reversed'
                $table->text('notes')->nullable(); // Any additional notes or description
                $table->json('metadata')->nullable(); // Store extra data in JSON format

                // Timestamps
                $table->timestamps(); // created_at and updated_at
                $table->softDeletes(); // For soft deletion

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
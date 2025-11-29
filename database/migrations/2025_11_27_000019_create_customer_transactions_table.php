<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'customer_transactions' table.
 * This table stores all financial transactions related to customers.
 *
 * @package Database\Migrations
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
        Schema::create('customer_transactions', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'customers' table
            // Assuming a 'customers' table already exists or will be created.
            $table->foreignId('customer_id')
                  ->constrained('customers') // Assumes 'customers' table exists
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('The ID of the customer associated with the transaction.');

            // Transaction Type: e.g., 'deposit', 'withdrawal', 'purchase', 'refund'
            $table->enum('type', ['deposit', 'withdrawal', 'purchase', 'refund', 'adjustment'])
                  ->comment('The type of the transaction (e.g., deposit, withdrawal).');

            // Transaction Amount: using decimal for precise financial data
            // Precision of 10 digits in total, 2 after the decimal point.
            $table->decimal('amount', 10, 2)
                  ->comment('The monetary amount of the transaction.');

            // Transaction Date: using date type for just the date
            $table->date('date')
                  ->comment('The date the transaction occurred.');

            // Reference/Description: a unique or descriptive string for the transaction
            $table->string('reference', 255)->nullable()
                  ->comment('A unique reference or description for the transaction.');

            // Indexes for performance
            $table->index('customer_id');
            $table->index('date');

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
        Schema::dropIfExists('customer_transactions');
    }
};

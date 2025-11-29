<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the 'supplier_transactions' table.
 * This table stores all financial transactions related to suppliers,
 * such as payments made to them (debit) or refunds received from them (credit).
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
        // Create the 'supplier_transactions' table
        Schema::create('supplier_transactions', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'suppliers' table
            // Assuming a 'suppliers' table exists. This ensures data integrity.
            $table->foreignId('supplier_id')
                  ->constrained() // Assumes the foreign key is to the 'id' column of the 'suppliers' table
                  ->onUpdate('cascade') // Update the transaction if the supplier ID changes
                  ->onDelete('restrict'); // Prevent deletion of a supplier if transactions exist

            // Transaction Type: 'debit' (payment to supplier) or 'credit' (refund from supplier)
            $table->enum('type', ['debit', 'credit'])->comment('Type of transaction: debit (payment to supplier) or credit (refund from supplier).');

            // Transaction Amount: Stored as a decimal for precise financial calculations.
            // Using precision (10) and scale (2) for up to 99,999,999.99
            $table->decimal('amount', 10, 2)->comment('The amount of the transaction.');

            // Transaction Date: The actual date the transaction occurred.
            $table->date('date')->comment('The date the transaction occurred.');

            // Reference/Description: A unique or descriptive reference for the transaction (e.g., invoice number, payment ID).
            $table->string('reference', 100)->nullable()->unique()->comment('Unique reference for the transaction (e.g., invoice number).');

            // Timestamps: created_at and updated_at columns
            $table->timestamps();

            // Indexes for performance
            $table->index('supplier_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the table if the migration is rolled back
        Schema::dropIfExists('supplier_transactions');
    }
};
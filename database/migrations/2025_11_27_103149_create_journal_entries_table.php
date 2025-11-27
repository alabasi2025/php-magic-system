<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'journal_entries' table.
 * This table stores all financial journal entries for the SEMOP Magic System.
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
        // Create the 'journal_entries' table
        Schema::create('journal_entries', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Unique Entry Number
            // This ensures that each journal entry has a unique identifier, which is crucial for auditing and reference.
            $table->string('entry_number', 50)->unique()->comment('Unique identifier for the journal entry.');

            // Date of the Entry
            $table->date('date')->comment('The date the journal entry was recorded.');

            // Description
            $table->text('description')->nullable()->comment('Detailed description of the journal entry.');

            // Reference
            // Can be used to link to external documents, invoices, or other related records.
            $table->string('reference', 100)->nullable()->comment('External reference number or link.');

            // Status Enum
            // Defines the current state of the journal entry (e.g., Draft, Posted, Cancelled).
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft')->comment('Current status of the journal entry.');

            // Foreign Key to Users Table (created_by)
            // Links the entry to the user who created it. Assumes a 'users' table exists.
            $table->foreignId('created_by')
                  ->constrained('users') // Assumes the foreign key references the 'id' column on the 'users' table
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // Prevent deletion of a user if they have created journal entries
                  ->comment('Foreign key to the user who created the entry.');

            // Timestamps
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns

            // Index for faster lookups on date and status
            $table->index(['date', 'status']);
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
        Schema::dropIfExists('journal_entries');
    }
};

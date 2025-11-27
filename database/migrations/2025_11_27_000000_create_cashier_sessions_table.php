<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'cashier_sessions' table.
 * This table is part of the Cashiers Gene and is used to track
 * the start and end times of a cashier's work session.
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
        // Check if the table already exists to prevent errors on re-run
        if (!Schema::hasTable('cashier_sessions')) {
            Schema::create('cashier_sessions', function (Blueprint $table) {
                // Primary key for the session
                $table->id();

                // Foreign key to link the session to a specific cashier (user)
                // Assuming 'users' table holds the cashier accounts
                $table->foreignId('cashier_id')
                      ->constrained('users') // Link to the 'users' table
                      ->onDelete('cascade') // Delete sessions if the user is deleted
                      ->comment('Foreign key to the cashier (user) who started the session.');

                // The time the cashier started their work session
                $table->timestamp('start_time')
                      ->comment('The exact time the cashier started the session.');

                // The time the cashier ended their work session (nullable until session ends)
                $table->timestamp('end_time')
                      ->nullable()
                      ->comment('The exact time the cashier ended the session. Null if the session is still active.');

                // Status of the session (e.g., 'open', 'closed', 'suspended')
                $table->string('status', 20)
                      ->default('open')
                      ->comment('The current status of the session (e.g., open, closed).');

                // Optional: Store the initial cash amount when the session started (float for currency)
                $table->decimal('initial_cash', 10, 2)
                      ->default(0.00)
                      ->comment('The amount of cash in the drawer at the start of the session.');

                // Optional: Store the final cash amount when the session ended
                $table->decimal('final_cash', 10, 2)
                      ->nullable()
                      ->comment('The amount of cash in the drawer at the end of the session.');

                // Standard timestamps for creation and update
                $table->timestamps();

                // Index for faster lookups by cashier and status
                $table->index(['cashier_id', 'status']);
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
        // Drop the table if it exists when rolling back the migration
        Schema::dropIfExists('cashier_sessions');
    }
};
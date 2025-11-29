<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'cashier_shifts' table.
 * This table is part of the Cashiers Gene and stores information about
 * the start and end of a cashier's work shift.
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
        // Ensure the table does not exist before creation
        if (!Schema::hasTable('cashier_shifts')) {
            Schema::create('cashier_shifts', function (Blueprint $table) {
                // Primary Key
                $table->id();

                // Foreign Key to the user (cashier) who opened the shift
                // Assuming 'users' table exists and has 'id'
                $table->foreignId('user_id')
                      ->comment('ID of the cashier (user) who opened the shift')
                      ->constrained('users')
                      ->onUpdate('cascade')
                      ->onDelete('restrict');

                // Foreign Key to the branch/unit where the shift was opened
                // Assuming 'branches' table exists and has 'id'
                $table->foreignId('branch_id')
                      ->comment('ID of the branch/unit where the shift was opened')
                      ->constrained('branches')
                      ->onUpdate('cascade')
                      ->onDelete('restrict');

                // Shift Timestamps
                $table->timestamp('start_time')
                      ->comment('The time the shift was opened');
                $table->timestamp('end_time')
                      ->nullable()
                      ->comment('The time the shift was closed (null if still open)');

                // Initial and Final Cash Amounts
                $table->decimal('initial_cash', 10, 2)
                      ->default(0.00)
                      ->comment('The amount of cash at the start of the shift (e.g., float)');
                $table->decimal('final_cash', 10, 2)
                      ->nullable()
                      ->comment('The amount of cash counted at the end of the shift');

                // Status of the shift (e.g., open, closed, reconciled)
                $table->string('status', 50)
                      ->default('open')
                      ->comment('Current status of the shift (e.g., open, closed, reconciled)');

                // Audit and Gene Architecture fields
                $table->timestamps(); // created_at and updated_at
                $table->softDeletes(); // deleted_at for soft deletion

                // Indexing for faster lookups
                $table->index(['user_id', 'branch_id']);
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
        Schema::dropIfExists('cashier_shifts');
    }
};
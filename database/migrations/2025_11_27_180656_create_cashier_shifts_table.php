<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the 'cashier_shifts' table for managing cashier work shifts.
 *
 * Task 2015: Database - نظام الصرافين (Cashiers) - Database - Task 15
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
        Schema::create('cashier_shifts', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to users table (The Cashier)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict')
                  ->comment('The ID of the cashier (user) who started the shift.');

            // Shift Timestamps
            $table->timestamp('start_time')->useCurrent()->comment('The exact time the shift started.');
            $table->timestamp('end_time')->nullable()->comment('The exact time the shift ended (null if still active).');

            // Financial Data
            $table->decimal('starting_cash', 10, 2)->default(0.00)->comment('The amount of cash the cashier started the shift with.');
            $table->decimal('ending_cash', 10, 2)->nullable()->comment('The amount of cash counted at the end of the shift.');
            $table->decimal('system_sales_total', 10, 2)->default(0.00)->comment('Total sales recorded by the system during this shift.');
            $table->decimal('difference', 10, 2)->nullable()->comment('The difference between ending_cash and expected cash (starting_cash + system_sales_total).');

            // Status and Notes
            $table->enum('status', ['open', 'closed', 'reconciled'])->default('open')->comment('Current status of the shift.');
            $table->text('notes')->nullable()->comment('Any notes or comments regarding the shift.');

            // Laravel Timestamps
            $table->timestamps();

            // Indexes for faster lookups
            $table->index(['user_id', 'status']);
        });
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
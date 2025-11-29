<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cashiers Gene - Database - Task 2: Create cashier_sessions table
        Schema::create('cashier_sessions', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to the cashiers table (Task 1)
            // Assuming the cashiers table is named 'cashiers' and uses 'id' as primary key
            $table->foreignId('cashier_id')
                  ->constrained('cashiers')
                  ->onDelete('cascade')
                  ->comment('Foreign key to the cashiers table');

            // Session details
            $table->timestamp('start_time')->comment('The time the cashier session started');
            $table->timestamp('end_time')->nullable()->comment('The time the cashier session ended (null if active)');
            $table->decimal('opening_balance', 10, 2)->default(0.00)->comment('The cash balance at the start of the session');
            $table->decimal('closing_balance', 10, 2)->nullable()->comment('The cash balance at the end of the session');
            $table->enum('status', ['open', 'closed', 'suspended'])->default('open')->comment('Status of the cashier session');

            // System tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->comment('User who opened the session');
            $table->foreignId('updated_by')->nullable()->constrained('users')->comment('User who last updated the session');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_sessions');
    }
};
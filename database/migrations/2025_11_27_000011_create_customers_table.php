<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the 'customers' table.
 * This table stores customer information for the SEMOP Magic System.
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
        Schema::create('customers', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Customer Details
            $table->string('name', 150)->comment('Full name of the customer.');
            $table->string('code', 50)->unique()->comment('Unique identifier code for the customer.');
            $table->string('email', 150)->nullable()->comment('Customer email address.');
            $table->string('phone', 50)->nullable()->comment('Customer phone number.');
            $table->text('address')->nullable()->comment('Customer physical address.');
            $table->string('tax_number', 100)->nullable()->comment('Customer tax identification number.');

            // Financial Details
            // Using decimal for credit limit with 10 total digits and 2 decimal places.
            $table->decimal('credit_limit', 10, 2)->default(0.00)->comment('Maximum credit limit allowed for the customer.');

            // Status
            // Using tinyInteger for status (e.g., 1 for Active, 0 for Inactive).
            $table->tinyInteger('status')->default(1)->comment('Customer status (1=Active, 0=Inactive).');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
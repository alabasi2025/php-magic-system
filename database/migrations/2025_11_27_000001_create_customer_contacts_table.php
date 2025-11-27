<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the 'customer_contacts' table.
 * This table stores contact information for specific customers.
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
        Schema::create('customer_contacts', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'customers' table
            // Assuming a 'customers' table exists. This links the contact to a customer.
            $table->foreignId('customer_id')
                  ->constrained('customers') // Assumes 'customers' table
                  ->onDelete('cascade')      // Delete contacts if the customer is deleted
                  ->comment('Foreign key to the customers table.');

            // Contact Details
            $table->string('name', 100)->comment('Full name of the contact person.');
            $table->string('position', 100)->nullable()->comment('Job position or title of the contact.');
            $table->string('email', 100)->unique()->nullable()->comment('Email address of the contact. Must be unique if present.');
            $table->string('phone', 50)->nullable()->comment('Phone number of the contact.');

            // Status Flag
            // Indicates if this is the primary contact for the customer.
            $table->boolean('is_primary')->default(false)->comment('Flag to indicate if this is the primary contact for the customer.');

            // Indexes
            $table->index(['customer_id', 'is_primary'], 'customer_contact_primary_idx');

            // Timestamps
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
        Schema::dropIfExists('customer_contacts');
    }
};
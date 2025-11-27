<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Create the 'suppliers' table to store information about the company's suppliers.
        Schema::create('suppliers', function (Blueprint $table) {
            // Primary Key: Auto-incrementing ID for the supplier.
            $table->id();

            // Supplier's name (required).
            $table->string('name', 255)->comment('Supplier\'s official name.');

            // Unique code for the supplier (required).
            // This is often used for quick identification or integration with external systems.
            $table->string('code', 50)->unique()->comment('Unique identifier code for the supplier.');

            // Contact information (optional).
            $table->string('email', 255)->nullable()->comment('Supplier\'s email address.');
            $table->string('phone', 50)->nullable()->comment('Supplier\'s phone number.');
            $table->text('address')->nullable()->comment('Supplier\'s physical address.');

            // Financial and legal information (optional).
            $table->string('tax_number', 100)->nullable()->comment('Supplier\'s tax identification number.');

            // Payment terms (optional).
            // This can store a description of the agreed-upon payment terms (e.g., "Net 30", "2/10 Net 30").
            $table->text('payment_terms')->nullable()->comment('Agreed-upon payment terms with the supplier.');

            // Timestamps for creation and last update.
            $table->timestamps();

            // Add an index on the name for faster searching.
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'suppliers' table if the migration is rolled back.
        Schema::dropIfExists('suppliers');
    }
};
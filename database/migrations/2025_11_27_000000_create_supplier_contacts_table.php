<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'supplier_contacts' table.
 * This table stores contact information for various suppliers.
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
        Schema::create('supplier_contacts', function (Blueprint $table) {
            // Primary key: Auto-incrementing ID
            $table->id();

            // Foreign key to the 'suppliers' table
            // Using constrained() for a foreign key to the 'suppliers' table,
            // assuming the primary key is 'id' and the table name is 'suppliers'.
            // 'cascade' on delete ensures that if a supplier is deleted, all
            // associated contacts are also deleted.
            $table->foreignId('supplier_id')
                  ->constrained('suppliers')
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('Foreign key to the suppliers table');

            // Contact's full name
            $table->string('name', 100)->comment('Full name of the supplier contact');

            // Contact's position or title
            $table->string('position', 100)->nullable()->comment('Position or title of the contact');

            // Contact's email address (must be unique for a contact)
            $table->string('email', 100)->unique()->nullable()->comment('Email address of the contact');

            // Contact's phone number
            $table->string('phone', 50)->nullable()->comment('Phone number of the contact');

            // Timestamps: created_at and updated_at
            $table->timestamps();

            // Adding an index for faster lookups by supplier_id
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_contacts');
    }
};
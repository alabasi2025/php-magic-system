<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the employee_documents table.
 * This table stores various documents related to employees, such as contracts,
 * certifications, or identification papers.
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
        Schema::create('employee_documents', function (Blueprint $table) {
            // Primary key ID
            $table->id();

            // Foreign key to the 'employees' table. Assuming 'employees' table exists.
            // This links the document to a specific employee.
            $table->foreignId('employee_id')
                  ->constrained('employees')
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('Foreign key to the employees table');

            // Type of the document (e.g., 'contract', 'passport', 'visa', 'certification').
            $table->string('type', 100)->comment('Type of the document (e.g., contract, visa)');

            // Path to the stored file. Should be unique to prevent duplicate file entries.
            $table->string('file_path')->unique()->comment('Absolute or relative path to the stored document file');

            // Expiry date of the document. Nullable for documents that do not expire.
            $table->date('expiry_date')->nullable()->comment('The date the document expires (nullable)');

            // Timestamps for creation and last update.
            $table->timestamps();

            // Add an index on employee_id for faster lookups
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
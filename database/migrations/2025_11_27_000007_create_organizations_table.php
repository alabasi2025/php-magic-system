<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'organizations' table.
 * This table stores information about different organizational units
 * within the SEMOP Magic System, supporting a hierarchical structure.
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
        Schema::create('organizations', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to support hierarchical structure (e.g., Holding Company)
            // It's nullable to allow for top-level organizations (the ultimate parent).
            $table->foreignId('holding_id')
                  ->nullable()
                  ->constrained('organizations') // Self-referencing foreign key
                  ->onUpdate('cascade')
                  ->onDelete('set null')
                  ->comment('Self-referencing foreign key to the parent organization (Holding)');

            // Organization details
            $table->string('name', 150)->comment('The official name of the organization');
            $table->string('code', 50)->unique()->comment('A unique, short code for the organization (e.g., SEMOP-HQ)');

            // Type of organization, using an enum for predefined types
            // Common types could be 'Unit', 'Institution', 'Branch', 'Department', etc.
            $table->enum('type', ['Unit', 'Institution', 'Branch', 'Department', 'Other'])
                  ->default('Institution')
                  ->comment('The type of the organization (e.g., Unit, Institution, Branch)');

            // Status of the organization (e.g., active, inactive)
            // Using tinyInteger for better performance and to allow for more status codes if needed,
            // but defaulting to 1 (active) for a simple boolean-like status.
            $table->tinyInteger('status')->default(1)->comment('The status of the organization (1=Active, 0=Inactive)');

            // Timestamps for creation and last update
            $table->timestamps();

            // Optional: Add an index on the 'type' column for faster lookups
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
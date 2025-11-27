<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'accounts' table.
 * This table is designed to store a hierarchical chart of accounts (COA)
 * for the SEMOP Magic System, including unique codes, parent-child relationships,
 * and status flags.
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
        // Create the 'accounts' table
        Schema::create('accounts', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Account Code: Must be unique for identification in the COA.
            $table->string('code', 50)->unique()->comment('Unique account code (e.g., 1000, 2101)');

            // Account Name
            $table->string('name', 255)->comment('Human-readable name of the account');

            // Parent Account ID: Defines the hierarchical structure (parent-child relationship).
            // It is nullable to allow for top-level (root) accounts.
            // Note: The foreign key constraint assumes the 'accounts' table is created first,
            // which is true within this 'up' method.
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('accounts') // Self-referencing foreign key
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // Prevent deletion if children exist
                  ->comment('ID of the parent account in the hierarchy');

            // Account Type ID: Links to the type of account (e.g., Asset, Liability, Equity).
            // Assumes an 'account_types' table exists or will be created in a preceding migration.
            $table->foreignId('account_type_id')
                  ->constrained('account_types') // Foreign key to the account_types table
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // Restrict deletion of account type if accounts use it
                  ->comment('ID of the associated account type');

            // Status Flags
            // is_active: Indicates if the account is currently in use.
            $table->boolean('is_active')->default(true)->comment('Account status: true if active, false if inactive');

            // is_final: Indicates if the account is a final (posting) account or a group (parent) account.
            $table->boolean('is_final')->default(false)->comment('Account level: true for final/posting account, false for group/parent account');

            // Indexes for performance
            $table->index(['parent_id', 'account_type_id']);

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
        // Drop the 'accounts' table if it exists
        Schema::dropIfExists('accounts');
    }
};

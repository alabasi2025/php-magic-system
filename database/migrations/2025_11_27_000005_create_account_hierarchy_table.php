<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'account_hierarchy' table.
 * This table implements the Materialized Path pattern for efficient
 * querying of account relationships (ancestors and descendants).
 *
 * The table structure includes:
 * - id: Primary key.
 * - account_id: The ID of the descendant account.
 * - ancestor_id: The ID of the ancestor account.
 * - depth: The distance (number of levels) between the ancestor and descendant.
 * - timestamps: created_at and updated_at.
 *
 * A unique composite index is added on (account_id, ancestor_id) to prevent
 * duplicate relationships.
 * A foreign key is assumed to exist on the 'accounts' table for both IDs.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_hierarchy', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Account ID (Descendant) - Foreign Key to the 'accounts' table
            // Assuming 'accounts' table exists and uses 'id' as primary key.
            $table->foreignId('account_id')
                  ->comment('The ID of the descendant account.')
                  ->constrained('accounts')
                  ->cascadeOnDelete();

            // Ancestor ID - Foreign Key to the 'accounts' table
            $table->foreignId('ancestor_id')
                  ->comment('The ID of the ancestor account.')
                  ->constrained('accounts')
                  ->cascadeOnDelete();

            // Depth of the relationship (distance between ancestor and descendant)
            $table->unsignedSmallInteger('depth')
                  ->comment('The distance (number of levels) between the ancestor and descendant. 0 for self-relationship.');

            // Timestamps
            $table->timestamps();

            // Indexes for performance and integrity
            // 1. Unique index to ensure a relationship is defined only once.
            $table->unique(['account_id', 'ancestor_id'], 'account_hierarchy_unique');

            // 2. Index on ancestor_id for efficient lookups of all descendants of an ancestor.
            $table->index('ancestor_id');

            // 3. Index on account_id for efficient lookups of all ancestors of an account.
            // This is covered by the unique index, but an explicit index can sometimes be beneficial.
            // We will rely on the unique index for now, as it covers the account_id as the first column.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_hierarchy');
    }
};
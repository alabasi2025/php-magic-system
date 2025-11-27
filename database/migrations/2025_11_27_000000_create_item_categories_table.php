<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'item_categories' table.
 * This table stores categories for items, supporting a hierarchical structure
 * through a self-referencing 'parent_id'.
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
        Schema::create('item_categories', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Category Name (e.g., "Electronics", "Clothing")
            $table->string('name', 100)->comment('The name of the item category.');

            // Unique Code (e.g., "ELEC", "CLOTH")
            $table->string('code', 50)->unique()->comment('A unique, short code for the category.');

            // Self-referencing Foreign Key for hierarchical structure
            // Nullable to allow for root categories.
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('item_categories') // Constrains to the same table
                  ->onUpdate('cascade')
                  ->onDelete('set null') // If a parent category is deleted, children become root categories
                  ->comment('The ID of the parent category, null for root categories.');

            // Detailed description of the category
            $table->text('description')->nullable()->comment('A detailed description of the category.');

            // Timestamps (created_at and updated_at)
            $table->timestamps();

            // Adding an index on the name for faster lookups
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
        Schema::dropIfExists('item_categories');
    }
};
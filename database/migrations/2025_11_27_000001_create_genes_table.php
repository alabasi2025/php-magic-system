<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the 'genes' table.
 * This table stores information about genetic elements or features within the SEMOP Magic System.
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
        // Create the 'genes' table
        Schema::create('genes', function (Blueprint $table) {
            // Primary key ID
            $table->id();

            // Unique code for the gene (e.g., a short identifier).
            // Indexed for fast lookups and enforced uniqueness.
            $table->string('code', 50)->unique()->comment('Unique identifier code for the gene.');

            // Human-readable name of the gene.
            $table->string('name', 255)->comment('The human-readable name of the gene.');

            // Category or type of the gene (e.g., "Regulatory", "Structural").
            $table->string('category', 100)->index()->comment('The category or type of the gene.');

            // Status flag to indicate if the gene is currently active or in use.
            $table->boolean('is_active')->default(true)->comment('Boolean flag indicating if the gene is active.');

            // JSON column to store flexible, unstructured settings or metadata for the gene.
            $table->json('settings')->nullable()->comment('JSON column for unstructured settings and metadata.');

            // Timestamps for creation and last update.
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
        // Drop the 'genes' table if the migration is rolled back
        Schema::dropIfExists('genes');
    }
};
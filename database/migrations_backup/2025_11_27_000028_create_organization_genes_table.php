<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'organization_genes' table.
 *
 * This table serves as a pivot table to link organizations with specific 'genes' (features or configurations).
 * It includes configuration settings specific to the organization's use of the gene.
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
        Schema::create('organization_genes', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'organizations' table
            // Using constrained() for a foreign key with cascade on delete for data integrity.
            // Assumes 'organizations' table exists.
            $table->foreignId('organization_id')
                  ->constrained('organizations')
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('The ID of the organization.');

            // Foreign Key to the 'genes' table
            // Assumes a 'genes' table exists for the features/configurations.
            $table->foreignId('gene_id')
                  ->constrained('genes')
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('The ID of the gene/feature.');

            // Feature status
            $table->boolean('is_enabled')
                  ->default(true)
                  ->comment('Indicates if the gene/feature is enabled for the organization.');

            // Configuration settings for the gene, stored as JSON
            $table->json('config')
                  ->nullable()
                  ->comment('JSON configuration for the gene specific to the organization.');

            // Timestamps for creation and last update
            $table->timestamps();

            // Unique constraint to prevent duplicate organization-gene pairings
            $table->unique(['organization_id', 'gene_id'], 'organization_gene_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_genes');
    }
};
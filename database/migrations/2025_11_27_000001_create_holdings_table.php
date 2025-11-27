<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'holdings' table.
 * This table is designed to store information about various holdings or entities
 * within the SEMOP Magic System, such as companies, assets, or accounts.
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
        Schema::create('holdings', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Holding Name
            // Stores the human-readable name of the holding.
            $table->string('name', 255)->comment('The name of the holding or entity.');

            // Unique Code
            // A short, unique identifier for the holding, often used for quick lookups.
            $table->string('code', 50)->unique()->comment('A unique short code for the holding.');

            // Description
            // Detailed description of the holding. Can be nullable.
            $table->text('description')->nullable()->comment('Detailed description of the holding.');

            // Logo/Icon Path
            // Stores the file path or URL to the holding\'s logo or icon. Can be nullable.
            $table->string('logo', 255)->nullable()->comment('File path or URL to the holding logo.');

            // Status
            // Indicates the current operational status (e.g., active, inactive, pending).
            // Using a string for flexibility, but could be an enum in a real-world scenario.
            $table->string('status', 50)->default('active')->index()->comment('The current status of the holding (e.g., active, inactive).');

            // Settings (JSON)
            // Stores flexible, unstructured configuration settings for the holding.
            $table->json('settings')->nullable()->comment('JSON column for flexible configuration settings.');

            // Timestamps
            // Standard Laravel timestamps for creation and last update.
            $table->timestamps();

            // Soft Deletes (Optional but good practice for core entities)
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('holdings');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the 'projects' table to store project information.
        Schema::create('projects', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Project name
            $table->string('name', 255)->comment('The name of the project.');

            // Unique project code/identifier
            $table->string('code', 50)->unique()->comment('A unique short code for the project.');

            // Project description (can be long)
            $table->text('description')->nullable()->comment('Detailed description of the project.');

            // Project start and end dates
            $table->date('start_date')->comment('The official start date of the project.');
            $table->date('end_date')->nullable()->comment('The expected or actual end date of the project.');

            // Project budget (decimal)
            // Using 10 total digits and 2 decimal places for currency/budget.
            $table->decimal('budget', 10, 2)->default(0.00)->comment('The allocated budget for the project.');

            // Project status (enum)
            // Common statuses for a project: pending, in_progress, completed, cancelled, on_hold.
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled', 'on_hold'])
                  ->default('pending')
                  ->comment('The current status of the project.');

            // Timestamps for creation and last update
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the 'projects' table if the migration is rolled back.
        Schema::dropIfExists('projects');
    }
};
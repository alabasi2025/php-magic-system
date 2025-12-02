<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'project_milestones' table.
 * This table stores milestones associated with a project.
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
        Schema::create('project_milestones', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'projects' table
            // Assuming a 'projects' table exists.
            $table->foreignId('project_id')
                  ->constrained('projects') // Assumes 'projects' table and 'id' column
                  ->onDelete('cascade'); // If a project is deleted, its milestones are also deleted

            // Milestone Details
            $table->string('name', 255)->comment('Name of the milestone');
            $table->date('due_date')->comment('The date the milestone is due');

            // Status and Completion
            // Using a string for status to allow for various states (e.g., 'pending', 'in_progress', 'completed', 'on_hold')
            $table->string('status', 50)->default('pending')->comment('Current status of the milestone');
            // Completion percentage, constrained between 0 and 100
            $table->unsignedTinyInteger('completion_percentage')->default(0)->comment('Completion percentage (0-100)');

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index('project_id');
            $table->index('due_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};
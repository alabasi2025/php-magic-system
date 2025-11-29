<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Create the 'tasks' table to manage project tasks.
        Schema::create('tasks', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to the 'projects' table. Assumes 'projects' table exists.
            // Using foreignId() for constrained foreign key.
            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->onDelete('cascade') // If a project is deleted, its tasks are also deleted.
                  ->comment('The project this task belongs to.');

            // Task details
            $table->string('title', 255)->comment('The title of the task.');
            $table->text('description')->nullable()->comment('Detailed description of the task.');

            // Foreign Key to the 'users' table for the assigned user. Assumes 'users' table exists.
            $table->foreignId('assigned_to')
                  ->nullable() // Task might not be assigned initially.
                  ->constrained('users')
                  ->onDelete('set null') // If a user is deleted, set assigned_to to null.
                  ->comment('The user ID the task is assigned to.');

            // Enum for task priority
            $table->enum('priority', ['low', 'medium', 'high'])
                  ->default('medium')
                  ->comment('The priority level of the task.');

            // Enum for task status
            $table->enum('status', ['pending', 'in_progress', 'completed', 'blocked'])
                  ->default('pending')
                  ->comment('The current status of the task.');

            // Due date for the task
            $table->timestamp('due_date')->nullable()->comment('The deadline for the task.');

            // Timestamps (created_at and updated_at)
            $table->timestamps();

            // Index for faster lookups on status and priority
            $table->index(['status', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

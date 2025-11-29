<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the 'task_comments' table.
 * This table stores comments related to tasks, including the comment text,
 * the user who posted it, and a JSON field for any associated attachments.
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
        // Create the 'task_comments' table
        Schema::create('task_comments', function (Blueprint $table) {
            // Primary Key: Auto-incrementing ID
            $table->id();

            // Foreign Key: Link to the 'tasks' table
            // This ensures every comment is associated with an existing task.
            // Assumes a 'tasks' table exists and has an 'id' column.
            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('The ID of the task this comment belongs to.');

            // Foreign Key: Link to the 'users' table
            // This identifies the user who posted the comment.
            // Assumes a 'users' table exists and has an 'id' column.
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // Prevent deleting a user if they have comments
                  ->comment('The ID of the user who posted the comment.');

            // Comment Content: Text field for the main comment body
            $table->text('comment')
                  ->comment('The actual content of the comment.');

            // Attachments: JSON field to store metadata about attachments (e.g., file paths, names)
            $table->json('attachments')
                  ->nullable()
                  ->comment('JSON array of attachment metadata for the comment.');

            // Timestamps: created_at and updated_at columns
            $table->timestamps();

            // Index for quick retrieval of comments by task
            $table->index('task_id', 'idx_task_comments_task_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'task_comments' table if it exists
        Schema::dropIfExists('task_comments');
    }
};
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
        // Create the 'task_attachments' table to store files attached to tasks.
        Schema::create('task_attachments', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key to the 'tasks' table. Assuming 'tasks' table exists.
            // This links the attachment to a specific task.
            $table->foreignId('task_id')
                  ->constrained('tasks') // Assumes the foreign table is 'tasks'
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Path where the file is stored (e.g., S3 path or local storage path)
            $table->string('file_path', 2048); // Increased length for long paths/URLs

            // Original name of the file
            $table->string('file_name', 512);

            // Size of the file in bytes
            $table->unsignedBigInteger('file_size');

            // Timestamps for creation and last update
            $table->timestamps();

            // Add an index for faster lookups by task_id
            $table->index(['task_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'task_attachments' table if the migration is rolled back.
        Schema::dropIfExists('task_attachments');
    }
};
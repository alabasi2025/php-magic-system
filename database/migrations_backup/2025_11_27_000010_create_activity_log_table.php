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
    public function up()
    {
        // Create the 'activity_log' table to store all application activity.
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            
            // The ID of the user who performed the action. Nullable if the action is by a guest or system.
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users') // Assumes a 'users' table exists
                  ->onDelete('set null');

            // The action performed (e.g., 'GET', 'POST', 'login', 'update_profile').
            $table->string('action', 100);

            // The model or resource being acted upon (e.g., 'App\Models\Post', 'User').
            $table->string('model')->nullable();

            // The IP address of the user.
            $table->ipAddress('ip');

            // The full URL of the request.
            $table->text('url');

            // The user agent string.
            $table->string('user_agent')->nullable();

            // Additional data, such as request payload or response status, stored as JSON.
            $table->json('data')->nullable();

            $table->timestamps();

            // Index for faster lookups by user and action.
            $table->index(['user_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_log');
    }
};
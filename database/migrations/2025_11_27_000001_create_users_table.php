<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to create the 'users' table with all required columns.
 *
 * This table will store user authentication and profile information.
 * It includes columns for name, email (unique), password, phone, avatar,
 * status, email verification, remember token, timestamps, and soft deletes.
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
        Schema::create('users', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // User Profile Information
            $table->string('name', 100)->comment('Full name of the user.');
            $table->string('email', 100)->unique()->comment('Unique email address for authentication.');
            $table->string('phone', 20)->nullable()->comment('User phone number, optional.');
            $table->string('avatar')->nullable()->comment('Path or URL to the user profile picture.');
            $table->unsignedTinyInteger('status')->default(1)->comment('User status: 1=Active, 0=Inactive, 2=Suspended.');

            // Authentication and Security
            $table->string('password')->comment('Hashed password for user authentication.');
            $table->timestamp('email_verified_at')->nullable()->comment('Timestamp when the user verified their email address.');
            $table->rememberToken()->comment('Token for "remember me" functionality.');

            // Timestamps and Soft Deletes
            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at for soft deletion

            // Indexes for performance
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
        Schema::dropIfExists('users');
    }
};
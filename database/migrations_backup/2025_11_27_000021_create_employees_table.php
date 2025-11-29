<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'employees' table.
 * This table stores detailed information about employees, linking them to users and departments.
 * It includes essential HR data like position, hire date, salary, and employment status.
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
        // Create the 'employees' table
        Schema::create('employees', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key to 'users' table (assuming a user account is required for an employee)
            // The user_id is unique to ensure a one-to-one relationship between a user and an employee record.
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users') // Assumes 'users' table exists
                  ->onUpdate('cascade')
                  ->onDelete('cascade')
                  ->comment('Foreign key to the users table, unique for one-to-one relationship.');

            // Foreign Key to 'departments' table (assuming a departments table exists)
            $table->foreignId('department_id')
                  ->constrained('departments') // Assumes 'departments' table exists
                  ->onUpdate('cascade')
                  ->onDelete('restrict') // Prevent deleting a department if employees are assigned
                  ->comment('Foreign key to the departments table.');

            // Employee Position (e.g., "Software Engineer", "HR Manager")
            $table->string('position', 100)->comment('The job title or position of the employee.');

            // Employee Hire Date
            $table->date('hire_date')->comment('The official date the employee was hired.');

            // Employee Salary (using decimal for precise currency storage)
            // Precision of 10 total digits, 2 of which are after the decimal point.
            $table->decimal('salary', 10, 2)->comment('The employee\'s current salary.');

            // Employee Status (e.g., 'active', 'on_leave', 'terminated')
            // Using an enum for predefined, controlled status values.
            $table->enum('status', ['active', 'on_leave', 'terminated', 'probation'])
                  ->default('active')
                  ->comment('The current employment status of the employee.');

            // Timestamps for creation and last update
            $table->timestamps();

            // Add an index for faster lookups on common fields
            $table->index(['department_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'employees' table if it exists
        Schema::dropIfExists('employees');
    }
};
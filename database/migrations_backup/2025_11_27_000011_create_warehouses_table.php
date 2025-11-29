<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'warehouses' table.
 * This table stores information about different warehouses in the system.
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
        Schema::create('warehouses', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Warehouse Code - Must be unique for identification
            $table->string('code', 50)->unique()->comment('Unique code for the warehouse (e.g., WH-001)');

            // Warehouse Name
            $table->string('name', 100)->comment('Name of the warehouse');

            // Warehouse Location
            $table->string('location')->nullable()->comment('Physical location or address of the warehouse');

            // Manager ID - Foreign key to the 'users' table (assuming 'users' table exists)
            // This links the warehouse to its responsible manager.
            $table->foreignId('manager_id')
                  ->nullable() // Assuming a manager might not be assigned initially
                  ->constrained('users') // Assumes 'users' table is the manager source
                  ->onUpdate('cascade')
                  ->onDelete('set null')
                  ->comment('Foreign key to the user who manages the warehouse');

            // Warehouse Type - Restricted to a predefined set of values
            $table->enum('type', ['main', 'satellite', 'transit', 'returns'])
                  ->default('main')
                  ->comment('Type of warehouse: main, satellite, transit, or returns');

            // Status Flag
            $table->boolean('is_active')->default(true)->comment('Indicates if the warehouse is currently active');

            // Timestamps
            $table->timestamps();

            // Add index for faster lookups on name and type
            $table->index(['name', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};

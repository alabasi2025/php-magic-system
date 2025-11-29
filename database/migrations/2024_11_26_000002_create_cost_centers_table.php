<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'cost_centers' table.
 * This table stores information about cost centers, which can be hierarchical.
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
        
        Schema::create('cost_centers', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Unique code for the cost center (e.g., 'CC-001', 'DEPT-HR')
            $table->string('code')->unique()->comment('Unique identifier code for the cost center.');

            // Name of the cost center
            $table->string('name')->comment('Human-readable name of the cost center.');

            // Hierarchical relationship: parent_id can be null for top-level cost centers
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('cost_centers') // Self-referencing foreign key
                  ->onUpdate('cascade')
                  ->onDelete('set null')
                  ->comment('ID of the parent cost center, null for root cost centers.');

            // Status flag: determines if the cost center is currently active
            $table->boolean('is_active')->default(true)->comment('Status of the cost center (active/inactive).');

            // Budget allocated to this cost center. Using decimal for precise currency/budget values.
            // Precision (total digits) of 15 and scale (digits after decimal) of 2 is a common standard.
            $table->decimal('budget', 15, 2)->default(0.00)->comment('Allocated budget for the cost center.');

            // Timestamps for creation and last update
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
    }
};
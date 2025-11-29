<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the 'cashier_system_settings' table.
 * This table will store system-wide configuration settings specific to the Cashiers Gene.
 *
 * Task ID: 2010
 * Category: Database
 * Gene: Cashiers
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
        // Check if the table already exists to prevent errors during re-runs
        if (!Schema::hasTable('cashier_system_settings')) {
            Schema::create('cashier_system_settings', function (Blueprint $table) {
                // Primary key and standard columns
                $table->id();
                $table->string('key')->unique()->comment('Unique setting key, e.g., default_currency, max_transaction_limit');
                $table->text('value')->nullable()->comment('The value of the setting, stored as JSON or string');
                $table->string('type')->default('string')->comment('Data type of the value (e.g., string, integer, boolean, json)');
                $table->text('description')->nullable()->comment('A brief description of the setting');

                // Standard Gene Architecture columns
                $table->unsignedBigInteger('created_by')->nullable()->comment('User ID of the creator');
                $table->unsignedBigInteger('updated_by')->nullable()->comment('User ID of the last updater');
                $table->unsignedBigInteger('deleted_by')->nullable()->comment('User ID of the deleter');
                $table->timestamps();
                $table->softDeletes();

                // Indexes for performance
                $table->index('key');
                $table->index('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_system_settings');
    }
};
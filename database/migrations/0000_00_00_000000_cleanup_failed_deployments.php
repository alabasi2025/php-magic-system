<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cleanup migration for failed deployments.
 * This migration drops all tables that may exist from failed deployments
 * to ensure a clean slate for subsequent migrations.
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
        // Drop all tables that may exist from failed deployments
        Schema::dropIfExists('cost_center_allocations');
        Schema::dropIfExists('cost_centers');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('audit_logs');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // No need to recreate tables in down() as they will be created by other migrations
    }
};

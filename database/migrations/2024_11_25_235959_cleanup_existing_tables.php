<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop tables in reverse order of dependencies
        Schema::dropIfExists('cost_center_allocations');
        Schema::dropIfExists('cost_centers');
        Schema::dropIfExists('account_balances');
        Schema::dropIfExists('account_hierarchy');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('account_types');
        
        // Clear migration records for these tables to allow re-running
        DB::table('migrations')->whereIn('migration', [
            '2024_11_26_000000_create_account_types_table',
            '2024_11_26_000001_create_accounts_table',
            '2024_11_26_000002_create_cost_centers_table',
            '2024_11_26_000003_create_cost_center_allocations_table',
            '2025_11_27_000000_create_account_balances_table',
            '2025_11_27_000043_create_account_hierarchy_table',
        ])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to recreate tables on rollback
    }
};

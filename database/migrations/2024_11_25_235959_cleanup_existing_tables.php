<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to recreate tables on rollback
    }
};

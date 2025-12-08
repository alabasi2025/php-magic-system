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
        Schema::table('warehouses', function (Blueprint $table) {
            $table->foreignId('warehouse_group_id')->nullable()->after('id')->constrained('warehouse_groups')->nullOnDelete()->comment('Warehouse group');
            $table->index('warehouse_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropForeign(['warehouse_group_id']);
            $table->dropIndex(['warehouse_group_id']);
            $table->dropColumn('warehouse_group_id');
        });
    }
};

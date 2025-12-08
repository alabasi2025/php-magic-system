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
        // Add deleted_at to items table if not exists
        if (Schema::hasTable('items') && !Schema::hasColumn('items', 'deleted_at')) {
            Schema::table('items', function (Blueprint $table) {
                $table->softDeletes()->after('status');
            });
        }

        // Add deleted_at to warehouses table if not exists
        if (Schema::hasTable('warehouses') && !Schema::hasColumn('warehouses', 'deleted_at')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->softDeletes()->after('description');
            });
        }

        // Add deleted_at to item_units table if not exists
        if (Schema::hasTable('item_units') && !Schema::hasColumn('item_units', 'deleted_at')) {
            Schema::table('item_units', function (Blueprint $table) {
                $table->softDeletes()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('items', 'deleted_at')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('warehouses', 'deleted_at')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('item_units', 'deleted_at')) {
            Schema::table('item_units', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};

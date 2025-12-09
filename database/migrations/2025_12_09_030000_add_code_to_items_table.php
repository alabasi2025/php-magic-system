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
        // Add code column if it doesn't exist
        if (!Schema::hasColumn('items', 'code')) {
            Schema::table('items', function (Blueprint $table) {
                $table->string('code', 100)->nullable()->unique()->after('id')->comment('Item code (same as SKU by default)');
            });
            
            // Update existing records: set code = sku
            DB::table('items')->whereNull('code')->update([
                'code' => DB::raw('sku')
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('items', 'code')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }
    }
};

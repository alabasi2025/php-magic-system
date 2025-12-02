<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to drop all existing tables in the database.
 * 
 * This migration will:
 * 1. Disable foreign key checks
 * 2. Drop all tables in the current database
 * 3. Re-enable foreign key checks
 * 
 * This is necessary to clean up the database from failed deployments.
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
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Get all tables in the current database
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $tableKey = "Tables_in_{$dbName}";

        // Drop each table
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            
            // Skip migrations table - we'll handle it separately
            if ($tableName === 'migrations') {
                continue;
            }
            
            Schema::dropIfExists($tableName);
        }

        // Clear migrations table to allow re-running all migrations
        DB::table('migrations')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Cannot reverse this migration
    }
};

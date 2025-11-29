<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Reset Database Migration
 * 
 * This migration drops ALL existing tables and recreates a clean database.
 * This is necessary because the old migration system used different naming conventions
 * and we need to start fresh with the new schema.
 * 
 * WARNING: This will delete ALL data in the database!
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks to allow dropping tables with foreign keys
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Get all tables in the current database
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $tableKey = 'Tables_in_' . $dbName;
        
        // Drop all existing tables
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            DB::statement("DROP TABLE IF EXISTS `{$tableName}`");
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Log the reset
        \Log::info('Database reset completed successfully. All tables dropped.');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse a database reset
        throw new \Exception('Cannot reverse a database reset migration!');
    }
};

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
        
        // Recreate migrations table
        DB::statement("CREATE TABLE `migrations` (
            `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `migration` varchar(255) NOT NULL,
            `batch` int NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Log the reset
        \Log::info('Database reset completed successfully. All tables dropped and migrations table recreated.');
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

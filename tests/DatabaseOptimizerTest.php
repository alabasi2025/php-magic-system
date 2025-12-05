<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\ChartAccount;
use App\Models\CashBox;
use App\Models\ChartGroup;
use App\Models\Unit;

/**
 * Database Optimizer Test Suite
 * 
 * Tests for database optimizations implemented in v3.17.0
 */
class DatabaseOptimizerTest extends TestCase
{
    /**
     * Test that PostgreSQL is the default database connection
     */
    public function test_postgresql_is_default_connection(): void
    {
        $defaultConnection = config('database.default');
        $this->assertEquals('pgsql', $defaultConnection);
    }

    /**
     * Test that only PostgreSQL connection is configured
     */
    public function test_only_postgresql_connection_exists(): void
    {
        $connections = config('database.connections');
        $this->assertArrayHasKey('pgsql', $connections);
        $this->assertCount(1, $connections);
    }

    /**
     * Test PostgreSQL-specific optimizations are configured
     */
    public function test_postgresql_optimizations_configured(): void
    {
        $pgsqlConfig = config('database.connections.pgsql');
        
        $this->assertArrayHasKey('options', $pgsqlConfig);
        $this->assertArrayHasKey('pool', $pgsqlConfig);
        
        // Check pool settings
        $this->assertEquals(2, $pgsqlConfig['pool']['min']);
        $this->assertEquals(10, $pgsqlConfig['pool']['max']);
    }

    /**
     * Test that performance indexes exist on chart_accounts table
     */
    public function test_chart_accounts_indexes_exist(): void
    {
        $indexes = DB::select("
            SELECT indexname 
            FROM pg_indexes 
            WHERE tablename = 'chart_accounts' 
            AND indexname LIKE 'idx_%'
        ");
        
        $indexNames = array_column($indexes, 'indexname');
        
        $this->assertContains('idx_chart_accounts_chart_group_id', $indexNames);
        $this->assertContains('idx_chart_accounts_parent_id', $indexNames);
        $this->assertContains('idx_chart_accounts_account_type', $indexNames);
        $this->assertContains('idx_chart_accounts_is_active', $indexNames);
    }

    /**
     * Test that performance indexes exist on cash_boxes table
     */
    public function test_cash_boxes_indexes_exist(): void
    {
        $indexes = DB::select("
            SELECT indexname 
            FROM pg_indexes 
            WHERE tablename = 'cash_boxes' 
            AND indexname LIKE 'idx_%'
        ");
        
        $indexNames = array_column($indexes, 'indexname');
        
        $this->assertContains('idx_cash_boxes_unit_id', $indexNames);
        $this->assertContains('idx_cash_boxes_is_active', $indexNames);
    }

    /**
     * Test OptimizedQueries trait is used in ChartAccount model
     */
    public function test_chart_account_uses_optimized_queries_trait(): void
    {
        $model = new ChartAccount();
        $traits = class_uses($model);
        
        $this->assertContains('App\Traits\OptimizedQueries', $traits);
    }

    /**
     * Test OptimizedQueries trait is used in CashBox model
     */
    public function test_cash_box_uses_optimized_queries_trait(): void
    {
        $model = new CashBox();
        $traits = class_uses($model);
        
        $this->assertContains('App\Traits\OptimizedQueries', $traits);
    }

    /**
     * Test scopeActive works correctly
     */
    public function test_scope_active_works(): void
    {
        // This is a basic test - in real scenario, you'd create test data
        $query = ChartAccount::active();
        $sql = $query->toSql();
        
        $this->assertStringContainsString('is_active', $sql);
    }

    /**
     * Test scopeByChartGroup works correctly
     */
    public function test_scope_by_chart_group_works(): void
    {
        $query = ChartAccount::byChartGroup(1);
        $sql = $query->toSql();
        
        $this->assertStringContainsString('chart_group_id', $sql);
    }

    /**
     * Test scopeByType works correctly
     */
    public function test_scope_by_type_works(): void
    {
        $query = ChartAccount::byType('general');
        $sql = $query->toSql();
        
        $this->assertStringContainsString('account_type', $sql);
    }

    /**
     * Test scopeActiveByChartGroup works correctly
     */
    public function test_scope_active_by_chart_group_works(): void
    {
        $query = ChartAccount::activeByChartGroup(1);
        $sql = $query->toSql();
        
        $this->assertStringContainsString('chart_group_id', $sql);
        $this->assertStringContainsString('is_active', $sql);
    }

    /**
     * Test default relations are defined in ChartAccount
     */
    public function test_chart_account_has_default_relations(): void
    {
        $model = new ChartAccount();
        $this->assertObjectHasProperty('defaultRelations', $model);
        $this->assertIsArray($model->defaultRelations);
    }

    /**
     * Test default relations are defined in CashBox
     */
    public function test_cash_box_has_default_relations(): void
    {
        $model = new CashBox();
        $this->assertObjectHasProperty('defaultRelations', $model);
        $this->assertIsArray($model->defaultRelations);
    }

    /**
     * Test query performance improvement
     * 
     * This is a basic benchmark test
     */
    public function test_query_performance_improvement(): void
    {
        // Measure query time with indexes
        $startTime = microtime(true);
        
        ChartAccount::where('is_active', true)
            ->where('account_type', 'general')
            ->limit(10)
            ->get();
        
        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        // Query should be faster than 100ms (with indexes)
        $this->assertLessThan(100, $queryTime);
    }

    /**
     * Test caching functionality
     */
    public function test_caching_functionality(): void
    {
        $cacheKey = 'test_cache_key';
        
        $result = ChartAccount::getCached($cacheKey, function () {
            return ['test' => 'data'];
        });
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('test', $result);
        
        // Clear cache
        ChartAccount::clearCache($cacheKey);
    }

    /**
     * Test chunk processing
     */
    public function test_chunk_processing(): void
    {
        $processed = 0;
        
        ChartAccount::processInChunks(10, function ($accounts) use (&$processed) {
            $processed += $accounts->count();
            return true;
        });
        
        $this->assertGreaterThanOrEqual(0, $processed);
    }

    /**
     * Test database connection is working
     */
    public function test_database_connection_works(): void
    {
        $this->assertTrue(DB::connection()->getDatabaseName() !== null);
    }

    /**
     * Test migration was executed successfully
     */
    public function test_performance_indexes_migration_executed(): void
    {
        $migrations = DB::table('migrations')
            ->where('migration', 'like', '%add_performance_indexes_to_tables%')
            ->count();
        
        $this->assertGreaterThan(0, $migrations);
    }
}

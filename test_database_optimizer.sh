#!/bin/bash

# Database Optimizer Test Script v3.17.0
# This script tests all database optimizations

echo "========================================="
echo "Database Optimizer v3.17.0 Test Suite"
echo "========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counter
TESTS_PASSED=0
TESTS_FAILED=0

# Function to print test result
print_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓ PASSED${NC}: $2"
        ((TESTS_PASSED++))
    else
        echo -e "${RED}✗ FAILED${NC}: $2"
        ((TESTS_FAILED++))
    fi
}

echo "1. Testing Configuration Files..."
echo "-----------------------------------"

# Test 1: Check if config/database.php exists
if [ -f "config/database.php" ]; then
    print_result 0 "config/database.php exists"
else
    print_result 1 "config/database.php not found"
fi

# Test 2: Check if PostgreSQL is default connection
if grep -q "'default' => env('DB_CONNECTION', 'pgsql')" config/database.php; then
    print_result 0 "PostgreSQL is default connection"
else
    print_result 1 "PostgreSQL is not default connection"
fi

# Test 3: Check if MySQL connection is removed
if ! grep -q "'mysql' =>" config/database.php; then
    print_result 0 "MySQL connection removed"
else
    print_result 1 "MySQL connection still exists"
fi

echo ""
echo "2. Testing Migration Files..."
echo "-----------------------------------"

# Test 4: Check if performance indexes migration exists
if [ -f "database/migrations/2025_12_03_120000_add_performance_indexes_to_tables.php" ]; then
    print_result 0 "Performance indexes migration exists"
else
    print_result 1 "Performance indexes migration not found"
fi

# Test 5: Check migration content
if grep -q "idx_chart_accounts_chart_group_id" database/migrations/2025_12_03_120000_add_performance_indexes_to_tables.php; then
    print_result 0 "Migration contains chart_accounts indexes"
else
    print_result 1 "Migration missing chart_accounts indexes"
fi

echo ""
echo "3. Testing Model Files..."
echo "-----------------------------------"

# Test 6: Check if OptimizedQueries trait exists
if [ -f "app/Traits/OptimizedQueries.php" ]; then
    print_result 0 "OptimizedQueries trait exists"
else
    print_result 1 "OptimizedQueries trait not found"
fi

# Test 7: Check if ChartAccount uses OptimizedQueries
if grep -q "OptimizedQueries" app/Models/ChartAccount.php; then
    print_result 0 "ChartAccount uses OptimizedQueries trait"
else
    print_result 1 "ChartAccount doesn't use OptimizedQueries trait"
fi

# Test 8: Check if CashBox uses OptimizedQueries
if grep -q "OptimizedQueries" app/Models/CashBox.php; then
    print_result 0 "CashBox uses OptimizedQueries trait"
else
    print_result 1 "CashBox doesn't use OptimizedQueries trait"
fi

# Test 9: Check if ChartAccount has new scopes
if grep -q "scopeByChartGroup" app/Models/ChartAccount.php; then
    print_result 0 "ChartAccount has scopeByChartGroup"
else
    print_result 1 "ChartAccount missing scopeByChartGroup"
fi

# Test 10: Check if ChartAccount has defaultRelations
if grep -q "defaultRelations" app/Models/ChartAccount.php; then
    print_result 0 "ChartAccount has defaultRelations"
else
    print_result 1 "ChartAccount missing defaultRelations"
fi

echo ""
echo "4. Testing Documentation..."
echo "-----------------------------------"

# Test 11: Check if analysis document exists
if [ -f "database_analysis.md" ]; then
    print_result 0 "database_analysis.md exists"
else
    print_result 1 "database_analysis.md not found"
fi

# Test 12: Check if report document exists
if [ -f "DATABASE_OPTIMIZER_v3.17.0_REPORT.md" ]; then
    print_result 0 "DATABASE_OPTIMIZER_v3.17.0_REPORT.md exists"
else
    print_result 1 "DATABASE_OPTIMIZER_v3.17.0_REPORT.md not found"
fi

echo ""
echo "5. Testing Test Files..."
echo "-----------------------------------"

# Test 13: Check if test file exists
if [ -f "tests/DatabaseOptimizerTest.php" ]; then
    print_result 0 "DatabaseOptimizerTest.php exists"
else
    print_result 1 "DatabaseOptimizerTest.php not found"
fi

# Test 14: Check if test file has test methods
if grep -q "test_postgresql_is_default_connection" tests/DatabaseOptimizerTest.php; then
    print_result 0 "Test file contains test methods"
else
    print_result 1 "Test file missing test methods"
fi

echo ""
echo "6. Testing Trait Methods..."
echo "-----------------------------------"

# Test 15: Check if trait has getCached method
if grep -q "function getCached" app/Traits/OptimizedQueries.php; then
    print_result 0 "OptimizedQueries has getCached method"
else
    print_result 1 "OptimizedQueries missing getCached method"
fi

# Test 16: Check if trait has processInChunks method
if grep -q "function processInChunks" app/Traits/OptimizedQueries.php; then
    print_result 0 "OptimizedQueries has processInChunks method"
else
    print_result 1 "OptimizedQueries missing processInChunks method"
fi

# Test 17: Check if trait has scopeActive method
if grep -q "function scopeActive" app/Traits/OptimizedQueries.php; then
    print_result 0 "OptimizedQueries has scopeActive method"
else
    print_result 1 "OptimizedQueries missing scopeActive method"
fi

echo ""
echo "========================================="
echo "Test Results Summary"
echo "========================================="
echo -e "Tests Passed: ${GREEN}${TESTS_PASSED}${NC}"
echo -e "Tests Failed: ${RED}${TESTS_FAILED}${NC}"
echo "Total Tests: $((TESTS_PASSED + TESTS_FAILED))"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}All tests passed! ✓${NC}"
    exit 0
else
    echo -e "${RED}Some tests failed! ✗${NC}"
    exit 1
fi

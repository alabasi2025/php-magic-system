<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\CodeMetric;
use App\Services\CodeMetricsService;
use App\Analyzers\ComplexityAnalyzer;
use App\Analyzers\SecurityAnalyzer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CodeMetricsTest extends TestCase
{
    use RefreshDatabase;

    private CodeMetricsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CodeMetricsService();
    }

    /**
     * Test: ComplexityAnalyzer can analyze a simple file
     */
    public function test_complexity_analyzer_can_analyze_file(): void
    {
        $analyzer = new ComplexityAnalyzer();
        
        // Create a test file
        $testFile = base_path('tests/fixtures/test_code.php');
        $this->createTestFile($testFile);
        
        $result = $analyzer->analyzeFile($testFile);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('file', $result);
        $this->assertArrayHasKey('lines', $result);
        $this->assertArrayHasKey('functions', $result);
        
        // Cleanup
        @unlink($testFile);
    }

    /**
     * Test: SecurityAnalyzer can detect SQL injection
     */
    public function test_security_analyzer_detects_sql_injection(): void
    {
        $analyzer = new SecurityAnalyzer();
        
        // Create a test file with SQL injection vulnerability
        $testFile = base_path('tests/fixtures/vulnerable_code.php');
        file_put_contents($testFile, '<?php
            $query = DB::raw("SELECT * FROM users WHERE id = $id");
        ');
        
        $issues = $analyzer->analyzeFile($testFile);
        
        $this->assertNotEmpty($issues);
        $this->assertEquals('sql_injection', $issues[0]['type']);
        $this->assertEquals('critical', $issues[0]['severity']);
        
        // Cleanup
        @unlink($testFile);
    }

    /**
     * Test: CodeMetricsService can calculate overall score
     */
    public function test_service_calculates_overall_score_correctly(): void
    {
        $scores = [
            'security' => 90,
            'reliability' => 85,
            'performance' => 88,
            'maintainability' => 87,
        ];
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('calculateOverallScore');
        $method->setAccessible(true);
        
        $overallScore = $method->invoke($this->service, $scores);
        
        // Expected: (90*0.3) + (85*0.25) + (88*0.2) + (87*0.25) = 87.65
        $this->assertEquals(87.65, $overallScore);
    }

    /**
     * Test: Grade calculation is correct
     */
    public function test_grade_calculation_is_correct(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('calculateGrade');
        $method->setAccessible(true);
        
        $this->assertEquals('A+', $method->invoke($this->service, 95));
        $this->assertEquals('A', $method->invoke($this->service, 90));
        $this->assertEquals('B+', $method->invoke($this->service, 85));
        $this->assertEquals('B', $method->invoke($this->service, 80));
        $this->assertEquals('C+', $method->invoke($this->service, 75));
        $this->assertEquals('C', $method->invoke($this->service, 70));
        $this->assertEquals('D', $method->invoke($this->service, 60));
        $this->assertEquals('F', $method->invoke($this->service, 50));
    }

    /**
     * Test: CodeMetric model stores data correctly
     */
    public function test_code_metric_model_stores_data(): void
    {
        $metric = CodeMetric::create([
            'version' => 'v3.21.0-test',
            'analyzed_at' => now(),
            'total_files' => 100,
            'total_lines' => 10000,
            'overall_score' => 87.5,
            'grade' => 'B+',
            'security_score' => 90,
            'reliability_score' => 85,
            'performance_score' => 88,
            'maintainability_score' => 87,
        ]);
        
        $this->assertDatabaseHas('code_metrics', [
            'version' => 'v3.21.0-test',
            'grade' => 'B+',
        ]);
        
        $this->assertEquals(87.5, $metric->overall_score);
        $this->assertEquals('جيد جداً', $metric->quality_status);
    }

    /**
     * Test: Trend comparison works correctly
     */
    public function test_trend_comparison_works(): void
    {
        // Create two analyses
        $analysis1 = CodeMetric::create([
            'version' => 'v3.20.0',
            'analyzed_at' => now()->subDay(),
            'overall_score' => 80,
            'security_score' => 75,
            'reliability_score' => 80,
            'performance_score' => 85,
            'maintainability_score' => 80,
        ]);
        
        $analysis2 = CodeMetric::create([
            'version' => 'v3.21.0',
            'analyzed_at' => now(),
            'overall_score' => 87.5,
            'security_score' => 90,
            'reliability_score' => 85,
            'performance_score' => 88,
            'maintainability_score' => 87,
        ]);
        
        $trend = $analysis2->getTrendComparison();
        
        $this->assertNotNull($trend);
        $this->assertEquals(7.5, $trend['overall_score']['change']);
        $this->assertEquals(15, $trend['security_score']['change']);
    }

    /**
     * Test: Dashboard route is accessible
     */
    public function test_dashboard_route_is_accessible(): void
    {
        $user = \App\Models\User::factory()->create();
        
        $response = $this->actingAs($user)->get('/code-metrics');
        
        $response->assertStatus(200);
        $response->assertViewIs('code-metrics.index');
    }

    /**
     * Test: Analysis can be triggered
     */
    public function test_analysis_can_be_triggered(): void
    {
        $user = \App\Models\User::factory()->create();
        
        $response = $this->actingAs($user)->post('/code-metrics/analyze');
        
        // Should redirect to show page or back with success
        $response->assertRedirect();
    }

    /**
     * Test: API endpoint returns latest analysis
     */
    public function test_api_returns_latest_analysis(): void
    {
        $metric = CodeMetric::create([
            'version' => 'v3.21.0',
            'analyzed_at' => now(),
            'overall_score' => 87.5,
            'grade' => 'B+',
            'security_score' => 90,
            'reliability_score' => 85,
            'performance_score' => 88,
            'maintainability_score' => 87,
        ]);
        
        $user = \App\Models\User::factory()->create();
        
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/code-metrics/latest');
        
        $response->assertStatus(200);
        $response->assertJson([
            'version' => 'v3.21.0',
            'overall_score' => 87.5,
            'grade' => 'B+',
        ]);
    }

    /**
     * Test: Export functionality works
     */
    public function test_export_functionality_works(): void
    {
        $metric = CodeMetric::create([
            'version' => 'v3.21.0',
            'analyzed_at' => now(),
            'overall_score' => 87.5,
            'grade' => 'B+',
            'security_score' => 90,
            'reliability_score' => 85,
            'performance_score' => 88,
            'maintainability_score' => 87,
        ]);
        
        $user = \App\Models\User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get("/code-metrics/{$metric->id}/export");
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
        
        $data = $response->json();
        $this->assertEquals('v3.21.0', $data['version']);
        $this->assertEquals(87.5, $data['overall_score']);
    }

    /**
     * Helper: Create a test PHP file
     */
    private function createTestFile(string $path): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $content = '<?php

namespace App\Test;

class TestClass
{
    public function simpleFunction()
    {
        return "Hello World";
    }
    
    public function complexFunction($param)
    {
        if ($param > 0) {
            for ($i = 0; $i < 10; $i++) {
                if ($i % 2 == 0) {
                    echo $i;
                }
            }
        } else {
            return false;
        }
        return true;
    }
}
';
        file_put_contents($path, $content);
    }
}

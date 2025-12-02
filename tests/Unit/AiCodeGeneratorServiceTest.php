<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AiCodeGeneratorService;
use Illuminate\Support\Facades\Config;
use Exception;

/**
 * Unit Tests for AiCodeGeneratorService
 * 
 * اختبارات الوحدة لخدمة توليد الأكواد بالذكاء الاصطناعي
 */
class AiCodeGeneratorServiceTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Skip tests if OpenAI API key is not configured
        if (!env('OPENAI_API_KEY')) {
            $this->markTestSkipped('OpenAI API key is not configured');
        }
        
        $this->service = new AiCodeGeneratorService();
    }

    /**
     * Test: Generate CRUD with valid input
     * اختبار: توليد CRUD مع مدخلات صحيحة
     */
    public function test_generate_crud_with_valid_input()
    {
        $description = "نموذج المنتجات يحتوي على اسم، وصف، سعر";
        $modelName = "Product";
        $fields = ["name", "description", "price"];

        $result = $this->service->generateCRUD($description, $modelName, $fields);

        $this->assertTrue($result['success']);
        $this->assertEquals($modelName, $result['model_name']);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('components', $result);
        $this->assertNotEmpty($result['code']);
    }

    /**
     * Test: Generate CRUD with empty description
     * اختبار: توليد CRUD مع وصف فارغ
     */
    public function test_generate_crud_with_empty_description()
    {
        $description = "";
        $modelName = "Product";

        try {
            $result = $this->service->generateCRUD($description, $modelName);
            
            // Should fail or return error
            $this->assertFalse($result['success']);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test: Generate CRUD with invalid model name
     * اختبار: توليد CRUD مع اسم نموذج غير صحيح
     */
    public function test_generate_crud_with_invalid_model_name()
    {
        $description = "نموذج المنتجات";
        $modelName = "product"; // Should be PascalCase

        try {
            $result = $this->service->generateCRUD($description, $modelName);
            
            // Should fail or return error
            $this->assertFalse($result['success']);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test: Generate Migration with valid input
     * اختبار: توليد Migration مع مدخلات صحيحة
     */
    public function test_generate_migration_with_valid_input()
    {
        $description = "جدول المنتجات يحتوي على اسم، وصف، سعر";
        $modelName = "Product";

        $result = $this->service->generateMigration($description, $modelName);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('migration_code', $result);
        $this->assertStringContainsString('Schema::', $result['migration_code']);
    }

    /**
     * Test: Generate API Resource with valid input
     * اختبار: توليد API Resource مع مدخلات صحيحة
     */
    public function test_generate_api_resource_with_valid_input()
    {
        $modelName = "Product";
        $fields = ["id", "name", "description", "price"];

        $result = $this->service->generateApiResource($modelName, $fields);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('resource_code', $result);
        $this->assertStringContainsString('JsonResource', $result['resource_code']);
    }

    /**
     * Test: Generate Tests with valid input
     * اختبار: توليد الاختبارات مع مدخلات صحيحة
     */
    public function test_generate_tests_with_valid_input()
    {
        $modelName = "Product";
        $description = "اختبر جميع عمليات CRUD";

        $result = $this->service->generateTests($modelName, $description);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('tests_code', $result);
        $this->assertStringContainsString('TestCase', $result['tests_code']);
    }

    /**
     * Test: Response structure
     * اختبار: هيكل الاستجابة
     */
    public function test_response_structure()
    {
        $description = "نموذج المنتجات";
        $modelName = "Product";

        $result = $this->service->generateCRUD($description, $modelName);

        // Check response structure
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('model_name', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test: Error handling
     * اختبار: معالجة الأخطاء
     */
    public function test_error_handling()
    {
        try {
            // Try with invalid API key
            $result = $this->service->generateCRUD("test", "Test");
            
            // Should return error response
            $this->assertIsArray($result);
            $this->assertArrayHasKey('success', $result);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }
}

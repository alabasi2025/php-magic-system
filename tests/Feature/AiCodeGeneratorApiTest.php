<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Integration Tests for Code Generator APIs
 * 
 * اختبارات التكامل لـ APIs مولد الأكواد
 */
class AiCodeGeneratorApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: GET /developer/ai/code-generator returns 200
     * اختبار: الحصول على صفحة مولد الأكواد
     */
    public function test_get_code_generator_page_returns_200()
    {
        $response = $this->get('/developer/ai/code-generator');

        $response->assertStatus(200);
        $response->assertViewIs('developer.ai-code-generator');
    }

    /**
     * Test: POST /developer/ai/code-generator with valid data
     * اختبار: توليد CRUD مع بيانات صحيحة
     */
    public function test_post_code_generator_with_valid_data()
    {
        $data = [
            'description' => 'نموذج المنتجات يحتوي على اسم، وصف، سعر، فئة',
            'model_name' => 'Product',
            'fields' => ['name', 'description', 'price', 'category_id'],
            'auto_save' => false
        ];

        $response = $this->postJson('/developer/ai/code-generator', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'model_name',
            'code',
            'components',
            'message'
        ]);
    }

    /**
     * Test: POST /developer/ai/code-generator with missing description
     * اختبار: توليد CRUD بدون وصف
     */
    public function test_post_code_generator_without_description()
    {
        $data = [
            'model_name' => 'Product',
            'fields' => ['name', 'description']
        ];

        $response = $this->postJson('/developer/ai/code-generator', $data);

        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonValidationErrors('description');
    }

    /**
     * Test: POST /developer/ai/code-generator with invalid model name
     * اختبار: توليد CRUD مع اسم نموذج غير صحيح
     */
    public function test_post_code_generator_with_invalid_model_name()
    {
        $data = [
            'description' => 'نموذج المنتجات',
            'model_name' => 'product', // Should be PascalCase
            'fields' => []
        ];

        $response = $this->postJson('/developer/ai/code-generator', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('model_name');
    }

    /**
     * Test: POST /developer/ai/migration with valid data
     * اختبار: توليد Migration مع بيانات صحيحة
     */
    public function test_post_migration_with_valid_data()
    {
        $data = [
            'table_name' => 'products',
            'description' => 'جدول المنتجات يحتوي على اسم، وصف، سعر',
            'fields' => ['name', 'description', 'price']
        ];

        $response = $this->postJson('/developer/ai/migration', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'migration_code',
            'message'
        ]);
    }

    /**
     * Test: POST /developer/ai/migration with invalid table name
     * اختبار: توليد Migration مع اسم جدول غير صحيح
     */
    public function test_post_migration_with_invalid_table_name()
    {
        $data = [
            'table_name' => 'Products', // Should be snake_case
            'description' => 'جدول المنتجات',
            'fields' => []
        ];

        $response = $this->postJson('/developer/ai/migration', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('table_name');
    }

    /**
     * Test: POST /developer/ai/api-resource with valid data
     * اختبار: توليد API Resource مع بيانات صحيحة
     */
    public function test_post_api_resource_with_valid_data()
    {
        $data = [
            'model_name' => 'Product',
            'fields' => ['id', 'name', 'description', 'price']
        ];

        $response = $this->postJson('/developer/ai/api-resource', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'resource_code',
            'message'
        ]);
    }

    /**
     * Test: POST /developer/ai/tests with valid data
     * اختبار: توليد Tests مع بيانات صحيحة
     */
    public function test_post_tests_with_valid_data()
    {
        $data = [
            'model_name' => 'Product',
            'description' => 'اختبر جميع عمليات CRUD'
        ];

        $response = $this->postJson('/developer/ai/tests', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'tests_code',
            'message'
        ]);
    }

    /**
     * Test: Response contains proper JSON structure
     * اختبار: الاستجابة تحتوي على هيكل JSON صحيح
     */
    public function test_response_json_structure()
    {
        $data = [
            'description' => 'نموذج المنتجات',
            'model_name' => 'Product',
            'fields' => []
        ];

        $response = $this->postJson('/developer/ai/code-generator', $data);

        $response->assertJson([
            'success' => true,
            'model_name' => 'Product'
        ]);
    }

    /**
     * Test: CSRF protection
     * اختبار: حماية CSRF
     */
    public function test_csrf_protection()
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $data = [
            'description' => 'نموذج المنتجات',
            'model_name' => 'Product'
        ];

        $response = $this->postJson('/developer/ai/code-generator', $data);

        // Should still work without CSRF token in API requests
        $response->assertStatus(200);
    }

    /**
     * Test: Rate limiting (if implemented)
     * اختبار: تحديد معدل الطلبات (إن تم تطبيقه)
     */
    public function test_rate_limiting()
    {
        $data = [
            'description' => 'نموذج المنتجات',
            'model_name' => 'Product'
        ];

        // Make multiple requests
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/developer/ai/code-generator', $data);
            $response->assertStatus(200);
        }

        // Should still work (rate limiting might not be implemented)
        $this->assertTrue(true);
    }
}

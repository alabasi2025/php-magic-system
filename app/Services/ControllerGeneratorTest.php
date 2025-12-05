<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ControllerGeneratorService;
use App\Models\ControllerGeneration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

/**
 * Controller Generator Test
 * اختبار مولد المتحكمات
 * 
 * Comprehensive automated testing for Controller Generator v3.27.0
 * اختبار آلي شامل لمولد المتحكمات v3.27.0
 * 
 * @package Tests\Feature
 * @author Manus AI - Controller Generator v3.27.0
 * @generated 2025-12-03
 */
class ControllerGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected ControllerGeneratorService $service;
    protected User $user;

    /**
     * Setup the test environment.
     * إعداد بيئة الاختبار
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = app(ControllerGeneratorService::class);
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * Test: Service can be instantiated
     * اختبار: يمكن إنشاء الخدمة
     *
     * @return void
     */
    public function test_service_can_be_instantiated(): void
    {
        $this->assertInstanceOf(ControllerGeneratorService::class, $this->service);
    }

    /**
     * Test: Can generate Resource Controller
     * اختبار: يمكن توليد Resource Controller
     *
     * @return void
     */
    public function test_can_generate_resource_controller(): void
    {
        $data = [
            'name' => 'ProductController',
            'type' => 'resource',
            'model_name' => 'Product',
            'namespace' => 'App\\Http\\Controllers',
            'generate_requests' => true,
            'generate_resources' => false,
        ];

        $result = $this->service->generate($data);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('files', $result);
        $this->assertNotEmpty($result['files']);
        
        // Check if controller file was generated
        $this->assertArrayHasKey('controller', $result['files']);
    }

    /**
     * Test: Can generate API Controller
     * اختبار: يمكن توليد API Controller
     *
     * @return void
     */
    public function test_can_generate_api_controller(): void
    {
        $data = [
            'name' => 'ProductController',
            'type' => 'api',
            'model_name' => 'Product',
            'namespace' => 'App\\Http\\Controllers\\Api',
            'generate_requests' => true,
            'generate_resources' => true,
        ];

        $result = $this->service->generate($data);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('controller', $result['files']);
        $this->assertArrayHasKey('resource', $result['files']);
    }

    /**
     * Test: Can generate Form Requests
     * اختبار: يمكن توليد Form Requests
     *
     * @return void
     */
    public function test_can_generate_form_requests(): void
    {
        $data = [
            'name' => 'ProductController',
            'type' => 'resource',
            'model_name' => 'Product',
            'generate_requests' => true,
        ];

        $result = $this->service->generate($data);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('store_request', $result['files']);
        $this->assertArrayHasKey('update_request', $result['files']);
    }

    /**
     * Test: Can generate API Resources
     * اختبار: يمكن توليد API Resources
     *
     * @return void
     */
    public function test_can_generate_api_resources(): void
    {
        $data = [
            'name' => 'ProductController',
            'type' => 'api',
            'model_name' => 'Product',
            'generate_resources' => true,
        ];

        $result = $this->service->generate($data);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('resource', $result['files']);
        $this->assertArrayHasKey('collection', $result['files']);
    }

    /**
     * Test: Generation is recorded in database
     * اختبار: يتم تسجيل التوليد في قاعدة البيانات
     *
     * @return void
     */
    public function test_generation_is_recorded_in_database(): void
    {
        $data = [
            'name' => 'ProductController',
            'type' => 'resource',
            'model_name' => 'Product',
        ];

        $this->service->generate($data);

        $this->assertDatabaseHas('controller_generations', [
            'name' => 'ProductController',
            'type' => 'resource',
            'model_name' => 'Product',
            'status' => 'completed',
        ]);
    }

    /**
     * Test: Can retrieve generation history
     * اختبار: يمكن استرجاع سجل التوليدات
     *
     * @return void
     */
    public function test_can_retrieve_generation_history(): void
    {
        // Generate multiple controllers
        for ($i = 1; $i <= 3; $i++) {
            ControllerGeneration::create([
                'name' => "TestController{$i}",
                'type' => 'resource',
                'model_name' => "Test{$i}",
                'input_type' => 'text',
                'input_data' => [],
                'status' => 'completed',
                'user_id' => $this->user->id,
            ]);
        }

        $history = ControllerGeneration::where('user_id', $this->user->id)->get();

        $this->assertCount(3, $history);
    }

    /**
     * Test: Can filter by controller type
     * اختبار: يمكن التصفية حسب نوع المتحكم
     *
     * @return void
     */
    public function test_can_filter_by_controller_type(): void
    {
        ControllerGeneration::create([
            'name' => 'ResourceController',
            'type' => 'resource',
            'model_name' => 'Test',
            'input_type' => 'text',
            'input_data' => [],
            'status' => 'completed',
        ]);

        ControllerGeneration::create([
            'name' => 'ApiController',
            'type' => 'api',
            'model_name' => 'Test',
            'input_type' => 'text',
            'input_data' => [],
            'status' => 'completed',
        ]);

        $resourceControllers = ControllerGeneration::ofType('resource')->get();
        $apiControllers = ControllerGeneration::ofType('api')->get();

        $this->assertCount(1, $resourceControllers);
        $this->assertCount(1, $apiControllers);
    }

    /**
     * Test: Can mark generation as failed
     * اختبار: يمكن وضع علامة على التوليد كفاشل
     *
     * @return void
     */
    public function test_can_mark_generation_as_failed(): void
    {
        $generation = ControllerGeneration::create([
            'name' => 'FailedController',
            'type' => 'resource',
            'model_name' => 'Test',
            'input_type' => 'text',
            'input_data' => [],
            'status' => 'pending',
        ]);

        $generation->markAsFailed('Test error message');

        $this->assertEquals('failed', $generation->fresh()->status);
        $this->assertEquals('Test error message', $generation->fresh()->error_message);
    }

    /**
     * Test: Can mark generation as completed
     * اختبار: يمكن وضع علامة على التوليد كمكتمل
     *
     * @return void
     */
    public function test_can_mark_generation_as_completed(): void
    {
        $generation = ControllerGeneration::create([
            'name' => 'CompletedController',
            'type' => 'resource',
            'model_name' => 'Test',
            'input_type' => 'text',
            'input_data' => [],
            'status' => 'pending',
        ]);

        $files = [
            'controller' => 'path/to/controller.php',
            'request' => 'path/to/request.php',
        ];

        $generation->markAsCompleted($files);

        $this->assertEquals('completed', $generation->fresh()->status);
        $this->assertEquals($files, $generation->fresh()->generated_files);
    }

    /**
     * Test: Generated code follows PSR-12 standards
     * اختبار: الكود المولد يتبع معايير PSR-12
     *
     * @return void
     */
    public function test_generated_code_follows_psr12_standards(): void
    {
        $data = [
            'name' => 'ProductController',
            'type' => 'resource',
            'model_name' => 'Product',
        ];

        $result = $this->service->generate($data);
        $code = $result['files']['controller'];

        // Check for proper namespace
        $this->assertStringContainsString('namespace App\\Http\\Controllers;', $code);
        
        // Check for proper class declaration
        $this->assertStringContainsString('class ProductController extends Controller', $code);
        
        // Check for type hints
        $this->assertStringContainsString(': View', $code);
        $this->assertStringContainsString(': RedirectResponse', $code);
    }

    /**
     * Test: Generated code has complete PHPDoc
     * اختبار: الكود المولد يحتوي على PHPDoc كامل
     *
     * @return void
     */
    public function test_generated_code_has_complete_phpdoc(): void
    {
        $data = [
            'name' => 'ProductController',
            'type' => 'resource',
            'model_name' => 'Product',
        ];

        $result = $this->service->generate($data);
        $code = $result['files']['controller'];

        // Check for class documentation
        $this->assertStringContainsString('/**', $code);
        $this->assertStringContainsString('* @package', $code);
        
        // Check for method documentation
        $this->assertStringContainsString('* Display a listing', $code);
        $this->assertStringContainsString('* @param', $code);
        $this->assertStringContainsString('* @return', $code);
    }

    /**
     * Test: Web interface is accessible
     * اختبار: واجهة الويب يمكن الوصول إليها
     *
     * @return void
     */
    public function test_web_interface_is_accessible(): void
    {
        $response = $this->get(route('developer.controller-generator.index'));

        $response->assertStatus(200);
        $response->assertViewIs('developer.code-generator.controller');
    }

    /**
     * Test: Can submit generation form
     * اختبار: يمكن إرسال نموذج التوليد
     *
     * @return void
     */
    public function test_can_submit_generation_form(): void
    {
        $data = [
            'name' => 'ProductController',
            'type' => 'resource',
            'model_name' => 'Product',
            'generate_requests' => true,
            'generate_resources' => false,
        ];

        $response = $this->post(route('developer.controller-generator.generate'), $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'files',
        ]);
    }

    /**
     * Test: Validation works correctly
     * اختبار: التحقق من الصحة يعمل بشكل صحيح
     *
     * @return void
     */
    public function test_validation_works_correctly(): void
    {
        $data = [
            'name' => '', // Invalid: empty name
            'type' => 'invalid_type', // Invalid: wrong type
        ];

        $response = $this->post(route('developer.controller-generator.generate'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'type']);
    }

    /**
     * Cleanup after tests.
     * تنظيف بعد الاختبارات
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // Clean up any generated test files
        $testFiles = [
            app_path('Http/Controllers/ProductController.php'),
            app_path('Http/Requests/StoreProductRequest.php'),
            app_path('Http/Requests/UpdateProductRequest.php'),
        ];

        foreach ($testFiles as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        parent::tearDown();
    }
}

<?php

namespace Tests\Feature;

use App\Models\ResourceGeneration;
use App\Services\ResourceGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * ResourceGeneratorTest
 *
 * اختبارات شاملة لمولد API Resources.
 * Comprehensive tests for API Resource Generator.
 *
 * @package Tests\Feature
 * @version v3.30.0
 * @author Manus AI
 */
class ResourceGeneratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var ResourceGeneratorService
     */
    protected ResourceGeneratorService $service;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create service instance
        $this->service = app(ResourceGeneratorService::class);
    }

    /**
     * Test: يمكن عرض صفحة القائمة الرئيسية.
     * Test: Can view index page.
     */
    public function test_can_view_index_page(): void
    {
        $response = $this->get(route('resource-generator.index'));

        $response->assertStatus(200);
        $response->assertViewIs('resource-generator.index');
        $response->assertViewHas('generations');
        $response->assertViewHas('statistics');
    }

    /**
     * Test: يمكن عرض صفحة الإنشاء.
     * Test: Can view create page.
     */
    public function test_can_view_create_page(): void
    {
        $response = $this->get(route('resource-generator.create'));

        $response->assertStatus(200);
        $response->assertViewIs('resource-generator.create');
        $response->assertViewHas('models');
    }

    /**
     * Test: يمكن توليد Single Resource بنجاح.
     * Test: Can generate Single Resource successfully.
     */
    public function test_can_generate_single_resource(): void
    {
        $data = [
            'name' => 'TestResource',
            'type' => 'single',
            'attributes' => ['id', 'name', 'email'],
        ];

        $generation = $this->service->generateResource(
            $data['name'],
            $data['type'],
            $data
        );

        $this->assertInstanceOf(ResourceGeneration::class, $generation);
        $this->assertEquals('TestResource', $generation->name);
        $this->assertEquals('single', $generation->type);
        $this->assertEquals('success', $generation->status);
        $this->assertNotEmpty($generation->content);
        $this->assertTrue(File::exists($generation->file_path));

        // Cleanup
        File::delete($generation->file_path);
    }

    /**
     * Test: يمكن توليد Collection Resource بنجاح.
     * Test: Can generate Collection Resource successfully.
     */
    public function test_can_generate_collection_resource(): void
    {
        $data = [
            'name' => 'TestCollection',
            'type' => 'collection',
        ];

        $generation = $this->service->generateResource(
            $data['name'],
            $data['type'],
            $data
        );

        $this->assertInstanceOf(ResourceGeneration::class, $generation);
        $this->assertEquals('TestCollection', $generation->name);
        $this->assertEquals('collection', $generation->type);
        $this->assertEquals('success', $generation->status);
        $this->assertStringContainsString('ResourceCollection', $generation->content);

        // Cleanup
        File::delete($generation->file_path);
    }

    /**
     * Test: يمكن توليد Nested Resource مع العلاقات.
     * Test: Can generate Nested Resource with relations.
     */
    public function test_can_generate_nested_resource_with_relations(): void
    {
        $data = [
            'name' => 'UserResource',
            'type' => 'nested',
            'attributes' => ['id', 'name', 'email'],
            'relations' => ['posts', 'comments'],
        ];

        $generation = $this->service->generateResource(
            $data['name'],
            $data['type'],
            $data
        );

        $this->assertInstanceOf(ResourceGeneration::class, $generation);
        $this->assertEquals('success', $generation->status);
        $this->assertStringContainsString('posts', $generation->content);
        $this->assertStringContainsString('comments', $generation->content);
        $this->assertStringContainsString('whenLoaded', $generation->content);

        // Cleanup
        File::delete($generation->file_path);
    }

    /**
     * Test: يتم تنسيق اسم الـ Resource بشكل صحيح.
     * Test: Resource name is formatted correctly.
     */
    public function test_resource_name_is_formatted_correctly(): void
    {
        $data = [
            'name' => 'user',
            'type' => 'single',
        ];

        $generation = $this->service->generateResource(
            $data['name'],
            $data['type'],
            $data
        );

        $this->assertEquals('UserResource', $generation->name);

        // Cleanup
        File::delete($generation->file_path);
    }

    /**
     * Test: يمكن حذف Resource.
     * Test: Can delete Resource.
     */
    public function test_can_delete_resource(): void
    {
        $generation = ResourceGeneration::create([
            'name' => 'TestResource',
            'type' => 'single',
            'attributes' => ['id', 'name'],
            'file_path' => base_path('app/Http/Resources/TestResource.php'),
            'content' => '<?php // Test',
            'status' => 'success',
        ]);

        // Create file
        File::put($generation->file_path, $generation->content);

        $this->assertTrue(File::exists($generation->file_path));

        // Delete
        $result = $this->service->deleteGeneration($generation->id);

        $this->assertTrue($result);
        $this->assertFalse(File::exists($generation->file_path));
        $this->assertDatabaseMissing('resource_generations', ['id' => $generation->id]);
    }

    /**
     * Test: يتم تسجيل الإحصائيات بشكل صحيح.
     * Test: Statistics are recorded correctly.
     */
    public function test_statistics_are_recorded_correctly(): void
    {
        // Create test generations
        ResourceGeneration::create([
            'name' => 'Test1',
            'type' => 'single',
            'attributes' => [],
            'file_path' => '/test1.php',
            'content' => 'test',
            'status' => 'success',
        ]);

        ResourceGeneration::create([
            'name' => 'Test2',
            'type' => 'collection',
            'attributes' => [],
            'file_path' => '/test2.php',
            'content' => 'test',
            'status' => 'failed',
            'error_message' => 'Test error',
        ]);

        ResourceGeneration::create([
            'name' => 'Test3',
            'type' => 'nested',
            'attributes' => [],
            'file_path' => '/test3.php',
            'content' => 'test',
            'status' => 'success',
            'ai_generated' => true,
        ]);

        $stats = ResourceGeneration::getStatistics();

        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(2, $stats['successful']);
        $this->assertEquals(1, $stats['failed']);
        $this->assertEquals(0, $stats['pending']);
        $this->assertEquals(1, $stats['ai_generated']);
        $this->assertEquals(1, $stats['by_type']['single']);
        $this->assertEquals(1, $stats['by_type']['collection']);
        $this->assertEquals(1, $stats['by_type']['nested']);
    }

    /**
     * Test: يمكن إنشاء Resource عبر HTTP POST.
     * Test: Can create Resource via HTTP POST.
     */
    public function test_can_create_resource_via_http_post(): void
    {
        $data = [
            'name' => 'ProductResource',
            'type' => 'single',
            'attributes' => ['id', 'name', 'price'],
        ];

        $response = $this->post(route('resource-generator.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('resource_generations', [
            'name' => 'ProductResource',
            'type' => 'single',
        ]);

        // Cleanup
        $generation = ResourceGeneration::where('name', 'ProductResource')->first();
        if ($generation && File::exists($generation->file_path)) {
            File::delete($generation->file_path);
        }
    }

    /**
     * Test: التحقق من البيانات المطلوبة.
     * Test: Validates required data.
     */
    public function test_validates_required_data(): void
    {
        $response = $this->post(route('resource-generator.store'), []);

        $response->assertSessionHasErrors(['name', 'type']);
    }

    /**
     * Test: يمكن عرض تفاصيل Resource.
     * Test: Can view Resource details.
     */
    public function test_can_view_resource_details(): void
    {
        $generation = ResourceGeneration::create([
            'name' => 'TestResource',
            'type' => 'single',
            'attributes' => ['id', 'name'],
            'file_path' => '/test.php',
            'content' => '<?php // Test',
            'status' => 'success',
        ]);

        $response = $this->get(route('resource-generator.show', $generation->id));

        $response->assertStatus(200);
        $response->assertViewIs('resource-generator.show');
        $response->assertViewHas('generation');
        $response->assertSee('TestResource');
    }

    /**
     * Cleanup after tests.
     */
    protected function tearDown(): void
    {
        // Clean up any test files
        $testFiles = [
            base_path('app/Http/Resources/TestResource.php'),
            base_path('app/Http/Resources/TestCollection.php'),
            base_path('app/Http/Resources/UserResource.php'),
            base_path('app/Http/Resources/ProductResource.php'),
        ];

        foreach ($testFiles as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        parent::tearDown();
    }
}

<?php

/**
 * 🧬 Gene: SeederGeneratorTest
 * 
 * اختبارات شاملة لنظام توليد الـ Seeders
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Tests
 * @package Tests\Feature
 */

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\SeederGeneration;
use App\Models\SeederTemplate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SeederGeneratorTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * المستخدم للاختبار
     */
    protected User $user;

    /**
     * إعداد بيئة الاختبار
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // إنشاء جدول وهمي للاختبار
        Schema::create('test_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    // ========== Web Routes Tests ==========

    /** @test */
    public function it_can_display_the_index_page()
    {
        SeederGeneration::factory()->count(5)->create();

        $response = $this->get(route('seeder-generator.index'));

        $response->assertStatus(200);
        $response->assertViewIs('seeder-generator.index');
        $response->assertSee('Seeder Generator');
    }

    /** @test */
    public function it_can_display_the_create_page()
    {
        $response = $this->get(route('seeder-generator.create'));

        $response->assertStatus(200);
        $response->assertViewIs('seeder-generator.create');
        $response->assertSee('إنشاء Seeder جديد');
    }

    /** @test */
    public function it_can_generate_a_seeder_from_text()
    {
        $description = 'أنشئ seeder لجدول test_products مع 10 منتجات';

        $response = $this->post(route('seeder-generator.generate.text'), [
            'description' => $description,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('seeder_generations', [
            'table_name' => 'test_products',
            'count' => 10,
        ]);
    }

    /** @test */
    public function it_can_generate_a_seeder_from_json()
    {
        $schema = '
        {
            "table_name": "test_products",
            "model_name": "TestProduct",
            "count": 20,
            "columns": {
                "name": {"type": "name"},
                "price": {"type": "price"}
            }
        }
        ';

        $response = $this->post(route('seeder-generator.generate.json'), [
            'schema' => $schema,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('seeder_generations', [
            'table_name' => 'test_products',
            'count' => 20,
        ]);
    }

    /** @test */
    public function it_can_generate_a_seeder_from_template()
    {
        $template = SeederTemplate::factory()->create([
            'table_name' => 'test_products',
            'model_name' => 'TestProduct',
        ]);

        $response = $this->post(route('seeder-generator.generate.template'), [
            'template_id' => $template->id,
            'count' => 15,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('seeder_generations', [
            'table_name' => 'test_products',
            'count' => 15,
        ]);
    }

    /** @test */
    public function it_can_display_a_seeder()
    {
        $seeder = SeederGeneration::factory()->create();

        $response = $this->get(route('seeder-generator.show', $seeder->id));

        $response->assertStatus(200);
        $response->assertSee($seeder->name);
    }

    /** @test */
    public function it_can_update_a_seeder()
    {
        $seeder = SeederGeneration::factory()->create();
        $newName = 'Updated Seeder Name';

        $response = $this->put(route('seeder-generator.update', $seeder->id), [
            'name' => $newName,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('seeder_generations', [
            'id' => $seeder->id,
            'name' => $newName,
        ]);
    }

    /** @test */
    public function it_can_delete_a_seeder()
    {
        $seeder = SeederGeneration::factory()->create();

        $response = $this->delete(route('seeder-generator.destroy', $seeder->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('seeder_generations', ['id' => $seeder->id]);
    }

    /** @test */
    public function it_can_download_a_seeder_file()
    {
        $seeder = SeederGeneration::factory()->create([
            'generated_content' => '<?php echo "test";',
        ]);

        $response = $this->get(route('seeder-generator.download', $seeder->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="' . $seeder->getSeederFileName() . '"');
    }

    /** @test */
    public function it_can_save_a_seeder_to_file()
    {
        $seeder = SeederGeneration::factory()->create();
        $filePath = $seeder->getSeederFilePath();

        File::shouldReceive('put')->once()->with($filePath, $seeder->generated_content);

        $response = $this->post(route('seeder-generator.save-file', $seeder->id));

        $response->assertRedirect();
    }

    // ========== API Routes Tests ==========

    /** @test */
    public function api_it_can_list_seeders()
    {
        SeederGeneration::factory()->count(3)->create();

        $response = $this->getJson(route('api.seeder-generator.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function api_it_can_generate_a_seeder()
    {
        $data = [
            'method' => 'json',
            'data' => [
                'table_name' => 'test_products',
                'model_name' => 'TestProduct',
                'count' => 5,
                'columns' => ['name' => ['type' => 'name']],
            ]
        ];

        $response = $this->postJson(route('api.seeder-generator.generate'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('seeder_generations', ['table_name' => 'test_products']);
    }

    /** @test */
    public function api_it_can_show_a_seeder()
    {
        $seeder = SeederGeneration::factory()->create();

        $response = $this->getJson(route('api.seeder-generator.show', $seeder->id));

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $seeder->id);
    }

    /** @test */
    public function api_it_can_update_a_seeder()
    {
        $seeder = SeederGeneration::factory()->create();
        $newName = 'API Updated Name';

        $response = $this->putJson(route('api.seeder-generator.update', $seeder->id), [
            'name' => $newName,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('seeder_generations', ['id' => $seeder->id, 'name' => $newName]);
    }

    /** @test */
    public function api_it_can_delete_a_seeder()
    {
        $seeder = SeederGeneration::factory()->create();

        $response = $this->deleteJson(route('api.seeder-generator.destroy', $seeder->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('seeder_generations', ['id' => $seeder->id]);
    }

    /** @test */
    public function api_it_can_list_templates()
    {
        SeederTemplate::factory()->count(5)->create();

        $response = $this->getJson(route('api.seeder-generator.templates'));

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    // ========== Service Logic Tests ==========

    /** @test */
    public function service_can_parse_text_description()
    {
        $service = $this->app->make(\App\Services\SeederGeneratorService::class);
        $description = 'أنشئ seeder لجدول users مع 50 مستخدم';

        $parsed = $this->invokeMethod($service, 'parseTextDescription', [$description]);

        $this->assertEquals('users', $parsed['table_name']);
        $this->assertEquals(50, $parsed['count']);
    }

    /** @test */
    public function service_can_build_seeder_content()
    {
        $service = $this->app->make(\App\Services\SeederGeneratorService::class);
        $schema = [
            'table_name' => 'test_products',
            'model_name' => 'TestProduct',
            'count' => 10,
            'columns' => [
                'name' => ['type' => 'name'],
                'price' => ['type' => 'price']
            ]
        ];

        $content = $this->invokeMethod($service, 'buildSeederFromJson', [$schema]);

        $this->assertStringContainsString('class TestProductsSeeder extends Seeder', $content);
        $this->assertStringContainsString('TestProduct::create', $content);
        $this->assertStringContainsString('\$faker->name', $content);
    }

    /**
     * Helper to call protected/private methods
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}

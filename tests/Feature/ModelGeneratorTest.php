<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ModelGeneratorService;
use App\Models\ModelGeneration;
use App\Models\ModelTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

/**
 * 🧪 Test: ModelGeneratorTest
 * 
 * اختبارات شاملة لمولد الـ Models
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Tests
 * @package Tests\Feature
 */
class ModelGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected ModelGeneratorService $service;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ModelGeneratorService();
    }

    /**
     * Test: توليد Model من وصف نصي
     */
    public function test_generate_from_text_description()
    {
        $description = <<<DESC
Model للمنتج (Product)
- الاسم (name) نص مطلوب
- الوصف (description) نص اختياري
- السعر (price) decimal مطلوب
- الكمية (quantity) integer default: 0
- نشط (is_active) boolean default: true
- belongsTo مع Category
- hasMany مع OrderItem
DESC;

        $generation = $this->service->generateFromText($description);

        $this->assertInstanceOf(ModelGeneration::class, $generation);
        $this->assertEquals('Product', $generation->name);
        $this->assertEquals('products', $generation->table_name);
        $this->assertEquals(ModelGeneration::STATUS_GENERATED, $generation->status);
        $this->assertNotEmpty($generation->generated_content);
        $this->assertIsArray($generation->attributes);
        $this->assertIsArray($generation->fillable);
        $this->assertIsArray($generation->relations);
    }

    /**
     * Test: توليد Model من JSON Schema
     */
    public function test_generate_from_json_schema()
    {
        $schema = [
            'name' => 'Customer',
            'description' => 'نموذج العميل',
            'table' => 'customers',
            'attributes' => [
                ['name' => 'name', 'type' => 'string', 'nullable' => false],
                ['name' => 'email', 'type' => 'string', 'nullable' => false],
                ['name' => 'phone', 'type' => 'string', 'nullable' => true],
            ],
            'fillable' => ['name', 'email', 'phone'],
            'casts' => [],
            'relations' => [
                ['type' => 'hasMany', 'model' => 'Order', 'method' => 'orders'],
            ],
            'timestamps' => true,
            'soft_deletes' => false,
        ];

        $generation = $this->service->generateFromJson($schema);

        $this->assertInstanceOf(ModelGeneration::class, $generation);
        $this->assertEquals('Customer', $generation->name);
        $this->assertEquals('customers', $generation->table_name);
        $this->assertEquals(ModelGeneration::STATUS_GENERATED, $generation->status);
        $this->assertNotEmpty($generation->generated_content);
        $this->assertCount(1, $generation->relations);
    }

    /**
     * Test: التحقق من صحة Model مولد
     */
    public function test_validate_generated_model()
    {
        $schema = [
            'name' => 'TestModel',
            'table' => 'test_models',
            'attributes' => [
                ['name' => 'title', 'type' => 'string'],
            ],
            'fillable' => ['title'],
        ];

        $generation = $this->service->generateFromJson($schema);
        $results = $this->service->validate($generation);

        $this->assertIsArray($results);
        $this->assertArrayHasKey('valid', $results);
        $this->assertArrayHasKey('errors', $results);
        $this->assertArrayHasKey('warnings', $results);
        $this->assertArrayHasKey('checks', $results);
        $this->assertTrue($results['valid']);
    }

    /**
     * Test: نشر Model إلى نظام الملفات
     */
    public function test_deploy_model_to_filesystem()
    {
        $schema = [
            'name' => 'DeployTest',
            'table' => 'deploy_tests',
            'attributes' => [
                ['name' => 'name', 'type' => 'string'],
            ],
            'fillable' => ['name'],
        ];

        $generation = $this->service->generateFromJson($schema);
        $success = $this->service->deploy($generation);

        $this->assertTrue($success);
        $this->assertEquals(ModelGeneration::STATUS_DEPLOYED, $generation->fresh()->status);
        $this->assertNotEmpty($generation->file_path);
        $this->assertFileExists($generation->file_path);

        // تنظيف
        if (file_exists($generation->file_path)) {
            unlink($generation->file_path);
        }
    }

    /**
     * Test: الحصول على إحصائيات
     */
    public function test_get_statistics()
    {
        // إنشاء بعض Generations للاختبار
        ModelGeneration::factory()->count(5)->create(['status' => ModelGeneration::STATUS_GENERATED]);
        ModelGeneration::factory()->count(3)->create(['status' => ModelGeneration::STATUS_DEPLOYED]);
        ModelGeneration::factory()->count(2)->create(['status' => ModelGeneration::STATUS_FAILED]);

        $statistics = $this->service->getStatistics();

        $this->assertIsArray($statistics);
        $this->assertArrayHasKey('total', $statistics);
        $this->assertArrayHasKey('generated', $statistics);
        $this->assertArrayHasKey('deployed', $statistics);
        $this->assertArrayHasKey('failed', $statistics);
        $this->assertEquals(10, $statistics['total']);
        $this->assertEquals(5, $statistics['generated']);
        $this->assertEquals(3, $statistics['deployed']);
        $this->assertEquals(2, $statistics['failed']);
    }

    /**
     * Test: Model Scopes
     */
    public function test_model_generation_scopes()
    {
        ModelGeneration::factory()->create(['status' => ModelGeneration::STATUS_DRAFT]);
        ModelGeneration::factory()->create(['status' => ModelGeneration::STATUS_GENERATED]);
        ModelGeneration::factory()->create(['status' => ModelGeneration::STATUS_VALIDATED]);
        ModelGeneration::factory()->create(['status' => ModelGeneration::STATUS_DEPLOYED]);
        ModelGeneration::factory()->create(['status' => ModelGeneration::STATUS_FAILED]);

        $this->assertEquals(1, ModelGeneration::draft()->count());
        $this->assertEquals(1, ModelGeneration::generated()->count());
        $this->assertEquals(1, ModelGeneration::validated()->count());
        $this->assertEquals(1, ModelGeneration::deployed()->count());
        $this->assertEquals(1, ModelGeneration::failed()->count());
    }

    /**
     * Test: Model Relations
     */
    public function test_model_generation_relations()
    {
        $template = ModelTemplate::factory()->create();
        $generation = ModelGeneration::factory()->create(['template_id' => $template->id]);

        $this->assertInstanceOf(ModelTemplate::class, $generation->template);
        $this->assertEquals($template->id, $generation->template->id);
    }

    /**
     * Test: Model Template Usage Count
     */
    public function test_model_template_usage_count()
    {
        $template = ModelTemplate::factory()->create(['usage_count' => 0]);

        $this->assertEquals(0, $template->usage_count);

        $template->incrementUsage();
        $this->assertEquals(1, $template->fresh()->usage_count);

        $template->incrementUsage();
        $this->assertEquals(2, $template->fresh()->usage_count);
    }

    /**
     * Test: Model Template Success Rate
     */
    public function test_model_template_success_rate()
    {
        $template = ModelTemplate::factory()->create([
            'success_count' => 0,
            'failure_count' => 0,
            'success_rate' => 0,
        ]);

        $template->incrementSuccess();
        $this->assertEquals(100, $template->fresh()->success_rate);

        $template->incrementSuccess();
        $this->assertEquals(100, $template->fresh()->success_rate);

        $template->incrementFailure();
        $this->assertEquals(66.67, round($template->fresh()->success_rate, 2));
    }

    /**
     * Test: توليد محتوى Model صحيح
     */
    public function test_generated_model_content_is_valid_php()
    {
        $schema = [
            'name' => 'ValidModel',
            'table' => 'valid_models',
            'attributes' => [
                ['name' => 'title', 'type' => 'string'],
                ['name' => 'is_active', 'type' => 'boolean'],
            ],
            'fillable' => ['title', 'is_active'],
            'casts' => ['is_active' => 'boolean'],
        ];

        $generation = $this->service->generateFromJson($schema);

        // التحقق من أن المحتوى يبدأ بـ <?php
        $this->assertStringStartsWith('<?php', $generation->generated_content);

        // التحقق من وجود namespace
        $this->assertStringContainsString('namespace App\Models;', $generation->generated_content);

        // التحقق من وجود class
        $this->assertStringContainsString('class ValidModel extends Model', $generation->generated_content);

        // التحقق من وجود fillable
        $this->assertStringContainsString('protected $fillable', $generation->generated_content);

        // التحقق من وجود casts
        $this->assertStringContainsString('protected $casts', $generation->generated_content);
    }

    /**
     * Test: معالجة الأخطاء عند توليد من JSON غير صحيح
     */
    public function test_handle_invalid_json_schema()
    {
        $this->expectException(\Exception::class);

        $schema = [
            // name مفقود
            'table' => 'invalid_models',
        ];

        $this->service->generateFromJson($schema);
    }

    /**
     * Test: معالجة الأخطاء عند توليد من جدول غير موجود
     */
    public function test_handle_non_existent_table()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('غير موجود في قاعدة البيانات');

        $this->service->generateFromDatabase('non_existent_table');
    }
}

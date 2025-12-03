<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MigrationGeneration;
use App\Models\MigrationTemplate;
use App\Models\User;
use App\Services\MigrationGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

/**
 * 🧬 Test: MigrationGeneratorTest
 * 
 * اختبارات شاملة لنظام توليد الـ migrations
 * 
 * @version 1.0.0
 * @since 2025-12-03
 */
class MigrationGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected MigrationGeneratorService $service;
    protected User $user;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new MigrationGeneratorService();
        
        // Create a test user
        $this->user = User::factory()->create();
    }

    /**
     * Test: توليد migration من وصف نصي
     */
    public function test_generate_migration_from_text_description()
    {
        $description = "أريد إنشاء جدول للمنتجات يحتوي على اسم المنتج والسعر";
        
        $generation = $this->service->generateFromText($description, 'web', $this->user->id);
        
        $this->assertInstanceOf(MigrationGeneration::class, $generation);
        $this->assertEquals(MigrationGeneration::STATUS_GENERATED, $generation->status);
        $this->assertNotEmpty($generation->generated_content);
        $this->assertStringContainsString('Schema::create', $generation->generated_content);
    }

    /**
     * Test: توليد migration من JSON Schema
     */
    public function test_generate_migration_from_json_schema()
    {
        $schema = [
            'table_name' => 'products',
            'description' => 'جدول المنتجات',
            'type' => 'create',
            'columns' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'length' => 255,
                    'comment' => 'اسم المنتج',
                ],
                [
                    'name' => 'price',
                    'type' => 'decimal',
                    'precision' => 10,
                    'scale' => 2,
                    'comment' => 'السعر',
                ],
            ],
        ];
        
        $generation = $this->service->generateFromJson($schema, 'json', $this->user->id);
        
        $this->assertInstanceOf(MigrationGeneration::class, $generation);
        $this->assertEquals('products', $generation->table_name);
        $this->assertEquals('create', $generation->migration_type);
        $this->assertStringContainsString('products', $generation->generated_content);
        $this->assertStringContainsString('name', $generation->generated_content);
        $this->assertStringContainsString('price', $generation->generated_content);
    }

    /**
     * Test: التحقق من صحة JSON Schema
     */
    public function test_validate_json_schema_requires_table_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('table_name is required');
        
        $schema = [
            'columns' => [],
        ];
        
        $this->service->generateFromJson($schema);
    }

    /**
     * Test: التحقق من صحة JSON Schema يتطلب columns
     */
    public function test_validate_json_schema_requires_columns()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('columns array is required');
        
        $schema = [
            'table_name' => 'test',
        ];
        
        $this->service->generateFromJson($schema);
    }

    /**
     * Test: حفظ migration كملف
     */
    public function test_save_migration_to_file()
    {
        $schema = [
            'table_name' => 'test_table',
            'columns' => [
                ['name' => 'test_column', 'type' => 'string', 'comment' => 'test'],
            ],
        ];
        
        $generation = $this->service->generateFromJson($schema);
        $filePath = $this->service->saveToFile($generation);
        
        $this->assertFileExists($filePath);
        $this->assertEquals($filePath, $generation->fresh()->file_path);
        
        // Cleanup
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }

    /**
     * Test: توليد migration مع foreign keys
     */
    public function test_generate_migration_with_foreign_keys()
    {
        $schema = [
            'table_name' => 'orders',
            'columns' => [
                [
                    'name' => 'customer_id',
                    'type' => 'foreignId',
                    'references' => 'customers',
                    'onDelete' => 'cascade',
                    'comment' => 'العميل',
                ],
            ],
        ];
        
        $generation = $this->service->generateFromJson($schema);
        
        $this->assertStringContainsString('foreignId', $generation->generated_content);
        $this->assertStringContainsString('constrained', $generation->generated_content);
        $this->assertStringContainsString('onDelete', $generation->generated_content);
    }

    /**
     * Test: توليد migration مع indexes
     */
    public function test_generate_migration_with_indexes()
    {
        $schema = [
            'table_name' => 'products',
            'columns' => [
                ['name' => 'name', 'type' => 'string', 'comment' => 'الاسم'],
            ],
            'indexes' => [
                ['columns' => ['name'], 'unique' => false],
            ],
        ];
        
        $generation = $this->service->generateFromJson($schema);
        
        $this->assertStringContainsString('index', $generation->generated_content);
    }

    /**
     * Test: توليد migration مع enum
     */
    public function test_generate_migration_with_enum_column()
    {
        $schema = [
            'table_name' => 'orders',
            'columns' => [
                [
                    'name' => 'status',
                    'type' => 'enum',
                    'values' => ['pending', 'completed', 'cancelled'],
                    'default' => 'pending',
                    'comment' => 'الحالة',
                ],
            ],
        ];
        
        $generation = $this->service->generateFromJson($schema);
        
        $this->assertStringContainsString('enum', $generation->generated_content);
        $this->assertStringContainsString('pending', $generation->generated_content);
    }

    /**
     * Test: الحصول على جميع الـ migrations
     */
    public function test_get_all_generations()
    {
        // Create some test generations
        MigrationGeneration::factory()->count(3)->create();
        
        $generations = $this->service->getAllGenerations();
        
        $this->assertCount(3, $generations);
    }

    /**
     * Test: حذف migration
     */
    public function test_delete_generation()
    {
        $generation = MigrationGeneration::factory()->create();
        $id = $generation->id;
        
        $result = $this->service->deleteGeneration($id);
        
        $this->assertTrue($result);
        $this->assertSoftDeleted('migration_generations', ['id' => $id]);
    }

    /**
     * Test: Model - تحديث الحالة
     */
    public function test_model_update_status()
    {
        $generation = MigrationGeneration::factory()->create([
            'status' => MigrationGeneration::STATUS_DRAFT,
        ]);
        
        $result = $generation->updateStatus(MigrationGeneration::STATUS_GENERATED);
        
        $this->assertTrue($result);
        $this->assertEquals(MigrationGeneration::STATUS_GENERATED, $generation->fresh()->status);
    }

    /**
     * Test: Model - التحقق من الحالة
     */
    public function test_model_status_checks()
    {
        $generation = MigrationGeneration::factory()->create([
            'status' => MigrationGeneration::STATUS_GENERATED,
        ]);
        
        $this->assertTrue($generation->isGenerated());
        $this->assertFalse($generation->isDraft());
        $this->assertFalse($generation->isTested());
        $this->assertFalse($generation->isApplied());
    }

    /**
     * Test: Model - Scopes
     */
    public function test_model_scopes()
    {
        MigrationGeneration::factory()->create(['status' => MigrationGeneration::STATUS_DRAFT]);
        MigrationGeneration::factory()->create(['status' => MigrationGeneration::STATUS_GENERATED]);
        MigrationGeneration::factory()->create(['status' => MigrationGeneration::STATUS_GENERATED]);
        
        $drafts = MigrationGeneration::byStatus(MigrationGeneration::STATUS_DRAFT)->get();
        $generated = MigrationGeneration::byStatus(MigrationGeneration::STATUS_GENERATED)->get();
        
        $this->assertCount(1, $drafts);
        $this->assertCount(2, $generated);
    }

    /**
     * Test: Template - استبدال المتغيرات
     */
    public function test_template_render_variables()
    {
        $template = MigrationTemplate::factory()->create([
            'template_content' => 'Table: {{table_name}}, Column: {{column_name}}',
        ]);
        
        $rendered = $template->render([
            'table_name' => 'products',
            'column_name' => 'name',
        ]);
        
        $this->assertEquals('Table: products, Column: name', $rendered);
    }

    /**
     * Test: Template - زيادة عداد الاستخدام
     */
    public function test_template_increment_usage()
    {
        $template = MigrationTemplate::factory()->create(['usage_count' => 5]);
        
        $template->incrementUsage();
        
        $this->assertEquals(6, $template->fresh()->usage_count);
    }

    /**
     * Test: توليد اسم migration صحيح
     */
    public function test_generate_correct_migration_name()
    {
        $schema = [
            'table_name' => 'products',
            'name' => 'create_products_table',
            'columns' => [
                ['name' => 'name', 'type' => 'string', 'comment' => 'الاسم'],
            ],
        ];
        
        $generation = $this->service->generateFromJson($schema);
        
        $this->assertEquals('create_products_table', $generation->name);
    }

    /**
     * Test: توليد Gene Pattern في التوثيق
     */
    public function test_generate_gene_pattern_documentation()
    {
        $schema = [
            'table_name' => 'products',
            'columns' => [
                ['name' => 'name', 'type' => 'string', 'comment' => 'الاسم'],
            ],
        ];
        
        $generation = $this->service->generateFromJson($schema);
        
        $this->assertStringContainsString('🧬 Gene:', $generation->generated_content);
        $this->assertStringContainsString('PRODUCTS', $generation->generated_content);
        $this->assertStringContainsString('@version', $generation->generated_content);
        $this->assertStringContainsString('@since', $generation->generated_content);
    }

    /**
     * Test: توليد أعمدة افتراضية (timestamps, softDeletes)
     */
    public function test_generate_default_columns()
    {
        $schema = [
            'table_name' => 'products',
            'columns' => [
                ['name' => 'name', 'type' => 'string', 'comment' => 'الاسم'],
            ],
        ];
        
        $generation = $this->service->generateFromJson($schema);
        
        $this->assertStringContainsString('timestamps()', $generation->generated_content);
        $this->assertStringContainsString('softDeletes()', $generation->generated_content);
    }
}

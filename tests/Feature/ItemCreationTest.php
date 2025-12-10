<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ItemUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating an item with all required fields.
     */
    public function test_can_create_item_with_required_fields(): void
    {
        // Arrange: Create a unit
        $unit = ItemUnit::factory()->create(['name' => 'لتر']);

        $itemData = [
            'sku' => 'TEST-001',
            'name' => 'صنف تجريبي',
            'unit_id' => $unit->id,
            'unit_price' => 10.50,
            'min_stock' => 50,
            'max_stock' => 500,
            'status' => 'active',
        ];

        // Act: Create the item
        $response = $this->post(route('inventory.items.store'), $itemData);

        // Assert: Check response and database
        $response->assertRedirect(route('inventory.items.index'));
        $this->assertDatabaseHas('items', [
            'sku' => 'TEST-001',
            'name' => 'صنف تجريبي',
            'unit_id' => $unit->id,
        ]);
    }

    /**
     * Test creating items with all available unit types.
     */
    public function test_can_create_items_with_all_unit_types(): void
    {
        // Arrange: Create all unit types
        $unitTypes = [
            'لتر', 'كيلوجرام', 'جرام', 'متر', 'سنتيمتر', 
            'ملليلتر', 'قطعة', 'كرتون', 'حزمة', 'دزينة', 
            'طقم', 'باليت'
        ];

        foreach ($unitTypes as $index => $unitName) {
            // Create unit
            $unit = ItemUnit::factory()->create(['name' => $unitName]);

            $itemData = [
                'sku' => 'UNIT-TEST-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'name' => "صنف بوحدة {$unitName}",
                'unit_id' => $unit->id,
                'unit_price' => rand(5, 100) + (rand(0, 99) / 100),
                'min_stock' => rand(10, 100),
                'max_stock' => rand(500, 10000),
                'status' => 'active',
            ];

            // Act: Create the item
            $response = $this->post(route('inventory.items.store'), $itemData);

            // Assert: Check response and database
            $response->assertRedirect(route('inventory.items.index'));
            $this->assertDatabaseHas('items', [
                'sku' => $itemData['sku'],
                'name' => $itemData['name'],
                'unit_id' => $unit->id,
            ]);
        }

        // Assert: Check total items created
        $this->assertEquals(count($unitTypes), Item::count());
    }

    /**
     * Test creating diesel item specifically.
     */
    public function test_can_create_diesel_item(): void
    {
        // Arrange: Create liter unit
        $unit = ItemUnit::factory()->create(['name' => 'لتر']);

        $dieselData = [
            'sku' => 'DIESEL-001',
            'name' => 'ديزل',
            'description' => 'وقود ديزل عالي الجودة',
            'unit_id' => $unit->id,
            'unit_price' => 5.50,
            'min_stock' => 100,
            'max_stock' => 10000,
            'status' => 'active',
        ];

        // Act: Create diesel item
        $response = $this->post(route('inventory.items.store'), $dieselData);

        // Assert: Check response and database
        $response->assertRedirect(route('inventory.items.index'));
        $this->assertDatabaseHas('items', [
            'sku' => 'DIESEL-001',
            'name' => 'ديزل',
            'unit_price' => 5.50,
            'min_stock' => 100,
            'max_stock' => 10000,
        ]);

        // Assert: Check item details
        $diesel = Item::where('sku', 'DIESEL-001')->first();
        $this->assertNotNull($diesel);
        $this->assertEquals('ديزل', $diesel->name);
        $this->assertEquals(5.50, $diesel->unit_price);
        $this->assertEquals('active', $diesel->status);
    }

    /**
     * Test creating benzene item.
     */
    public function test_can_create_benzene_item(): void
    {
        // Arrange: Create liter unit
        $unit = ItemUnit::factory()->create(['name' => 'لتر']);

        $benzeneData = [
            'sku' => 'BENZENE-001',
            'name' => 'بنزين',
            'description' => 'بنزين 95 أوكتان',
            'unit_id' => $unit->id,
            'unit_price' => 6.00,
            'min_stock' => 50,
            'max_stock' => 8000,
            'status' => 'active',
        ];

        // Act: Create benzene item
        $response = $this->post(route('inventory.items.store'), $benzeneData);

        // Assert: Check response and database
        $response->assertRedirect(route('inventory.items.index'));
        $this->assertDatabaseHas('items', [
            'sku' => 'BENZENE-001',
            'name' => 'بنزين',
            'unit_price' => 6.00,
        ]);
    }

    /**
     * Test validation: SKU is required.
     */
    public function test_sku_is_required(): void
    {
        // Arrange: Create unit
        $unit = ItemUnit::factory()->create(['name' => 'لتر']);

        $itemData = [
            'sku' => '', // Empty SKU
            'name' => 'صنف بدون SKU',
            'unit_id' => $unit->id,
            'unit_price' => 10.00,
            'min_stock' => 10,
            'max_stock' => 100,
            'status' => 'active',
        ];

        // Act: Try to create item
        $response = $this->post(route('inventory.items.store'), $itemData);

        // Assert: Should fail validation
        $response->assertSessionHasErrors('sku');
    }

    /**
     * Test validation: SKU must be unique.
     */
    public function test_sku_must_be_unique(): void
    {
        // Arrange: Create unit and first item
        $unit = ItemUnit::factory()->create(['name' => 'لتر']);
        Item::factory()->create([
            'sku' => 'DUPLICATE-001',
            'unit_id' => $unit->id,
        ]);

        $itemData = [
            'sku' => 'DUPLICATE-001', // Duplicate SKU
            'name' => 'صنف مكرر',
            'unit_id' => $unit->id,
            'unit_price' => 10.00,
            'min_stock' => 10,
            'max_stock' => 100,
            'status' => 'active',
        ];

        // Act: Try to create item with duplicate SKU
        $response = $this->post(route('inventory.items.store'), $itemData);

        // Assert: Should fail validation
        $response->assertSessionHasErrors('sku');
    }

    /**
     * Test validation: Name is required.
     */
    public function test_name_is_required(): void
    {
        // Arrange: Create unit
        $unit = ItemUnit::factory()->create(['name' => 'لتر']);

        $itemData = [
            'sku' => 'TEST-002',
            'name' => '', // Empty name
            'unit_id' => $unit->id,
            'unit_price' => 10.00,
            'min_stock' => 10,
            'max_stock' => 100,
            'status' => 'active',
        ];

        // Act: Try to create item
        $response = $this->post(route('inventory.items.store'), $itemData);

        // Assert: Should fail validation
        $response->assertSessionHasErrors('name');
    }

    /**
     * Test validation: Unit ID is required.
     */
    public function test_unit_id_is_required(): void
    {
        $itemData = [
            'sku' => 'TEST-003',
            'name' => 'صنف بدون وحدة',
            'unit_id' => null, // No unit
            'unit_price' => 10.00,
            'min_stock' => 10,
            'max_stock' => 100,
            'status' => 'active',
        ];

        // Act: Try to create item
        $response = $this->post(route('inventory.items.store'), $itemData);

        // Assert: Should fail validation
        $response->assertSessionHasErrors('unit_id');
    }

    /**
     * Test validation: Max stock must be greater than min stock.
     */
    public function test_max_stock_must_be_greater_than_min_stock(): void
    {
        // Arrange: Create unit
        $unit = ItemUnit::factory()->create(['name' => 'لتر']);

        $itemData = [
            'sku' => 'TEST-004',
            'name' => 'صنف بحدود خاطئة',
            'unit_id' => $unit->id,
            'unit_price' => 10.00,
            'min_stock' => 100,
            'max_stock' => 50, // Max < Min (invalid)
            'status' => 'active',
        ];

        // Act: Try to create item
        $response = $this->post(route('inventory.items.store'), $itemData);

        // Assert: Should fail validation
        $response->assertSessionHasErrors('max_stock');
    }

    /**
     * Test creating item with optional fields.
     */
    public function test_can_create_item_with_optional_fields(): void
    {
        // Arrange: Create unit
        $unit = ItemUnit::factory()->create(['name' => 'كيلوجرام']);

        $itemData = [
            'sku' => 'FULL-001',
            'name' => 'صنف كامل',
            'description' => 'صنف مع جميع الحقول',
            'barcode' => '1234567890123',
            'unit_id' => $unit->id,
            'unit_price' => 25.75,
            'min_stock' => 20,
            'max_stock' => 200,
            'status' => 'active',
        ];

        // Act: Create item
        $response = $this->post(route('inventory.items.store'), $itemData);

        // Assert: Check all fields
        $response->assertRedirect(route('inventory.items.index'));
        $this->assertDatabaseHas('items', [
            'sku' => 'FULL-001',
            'name' => 'صنف كامل',
            'description' => 'صنف مع جميع الحقول',
            'barcode' => '1234567890123',
        ]);
    }

    /**
     * Test creating inactive item.
     */
    public function test_can_create_inactive_item(): void
    {
        // Arrange: Create unit
        $unit = ItemUnit::factory()->create(['name' => 'قطعة']);

        $itemData = [
            'sku' => 'INACTIVE-001',
            'name' => 'صنف معطل',
            'unit_id' => $unit->id,
            'unit_price' => 15.00,
            'min_stock' => 10,
            'max_stock' => 100,
            'status' => 'inactive',
        ];

        // Act: Create inactive item
        $response = $this->post(route('inventory.items.store'), $itemData);

        // Assert: Check status
        $response->assertRedirect(route('inventory.items.index'));
        $this->assertDatabaseHas('items', [
            'sku' => 'INACTIVE-001',
            'status' => 'inactive',
        ]);
    }
}

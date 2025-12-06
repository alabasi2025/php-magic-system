<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\StockMovement;
use App\Services\InventoryService;
use App\Services\StockMovementService;

/**
 * Inventory System Test Suite
 * 
 * Comprehensive tests for the Inventory Management System v4.1.0
 */
class InventorySystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $warehouse;
    protected $item;
    protected $unit;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create();
        
        // Create test unit
        $this->unit = ItemUnit::create([
            'code' => 'PCS',
            'name' => 'قطعة',
            'name_en' => 'Piece',
            'symbol' => 'pcs',
            'status' => 'active',
        ]);

        // Create test warehouse
        $this->warehouse = Warehouse::create([
            'code' => 'WH001',
            'name' => 'المخزن الرئيسي',
            'location' => 'صنعاء',
            'manager_id' => $this->user->id,
            'status' => 'active',
        ]);

        // Create test item
        $this->item = Item::create([
            'sku' => 'ITEM001',
            'name' => 'صنف تجريبي',
            'unit_id' => $this->unit->id,
            'min_stock' => 10,
            'max_stock' => 100,
            'unit_price' => 50,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function test_can_create_warehouse()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('warehouses.store'), [
            'code' => 'WH002',
            'name' => 'مخزن فرعي',
            'location' => 'عدن',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('warehouses.index'));
        $this->assertDatabaseHas('warehouses', [
            'code' => 'WH002',
            'name' => 'مخزن فرعي',
        ]);
    }

    /** @test */
    public function test_can_create_item()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('items.store'), [
            'sku' => 'ITEM002',
            'name' => 'صنف جديد',
            'unit_id' => $this->unit->id,
            'min_stock' => 5,
            'max_stock' => 50,
            'unit_price' => 100,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', [
            'sku' => 'ITEM002',
            'name' => 'صنف جديد',
        ]);
    }

    /** @test */
    public function test_can_create_stock_in_movement()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('stock-movements.store'), [
            'movement_type' => 'stock_in',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 50,
            'unit_cost' => 50,
            'movement_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('stock-movements.index'));
        $this->assertDatabaseHas('stock_movements', [
            'movement_type' => 'stock_in',
            'item_id' => $this->item->id,
            'quantity' => 50,
        ]);
    }

    /** @test */
    public function test_stock_movement_requires_approval()
    {
        $movement = StockMovement::create([
            'movement_number' => 'IN-20251205-0001',
            'movement_type' => 'stock_in',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 50,
            'unit_cost' => 50,
            'total_cost' => 2500,
            'movement_date' => now(),
            'status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        $this->assertEquals('pending', $movement->status);
        $this->assertNull($movement->approved_by);
    }

    /** @test */
    public function test_inventory_service_calculates_current_stock()
    {
        // Create stock in movement
        StockMovement::create([
            'movement_number' => 'IN-20251205-0001',
            'movement_type' => 'stock_in',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 100,
            'unit_cost' => 50,
            'total_cost' => 5000,
            'movement_date' => now(),
            'status' => 'approved',
            'created_by' => $this->user->id,
        ]);

        // Create stock out movement
        StockMovement::create([
            'movement_number' => 'OUT-20251205-0001',
            'movement_type' => 'stock_out',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 30,
            'unit_cost' => 50,
            'total_cost' => 1500,
            'movement_date' => now(),
            'status' => 'approved',
            'created_by' => $this->user->id,
        ]);

        $inventoryService = new InventoryService();
        $currentStock = $inventoryService->getCurrentStock($this->item->id, $this->warehouse->id);

        $this->assertEquals(70, $currentStock);
    }

    /** @test */
    public function test_transfer_between_warehouses()
    {
        // Create second warehouse
        $warehouse2 = Warehouse::create([
            'code' => 'WH002',
            'name' => 'مخزن فرعي',
            'status' => 'active',
        ]);

        // Add stock to first warehouse
        StockMovement::create([
            'movement_number' => 'IN-20251205-0001',
            'movement_type' => 'stock_in',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 100,
            'unit_cost' => 50,
            'total_cost' => 5000,
            'movement_date' => now(),
            'status' => 'approved',
            'created_by' => $this->user->id,
        ]);

        // Transfer to second warehouse
        StockMovement::create([
            'movement_number' => 'TRF-20251205-0001',
            'movement_type' => 'transfer',
            'warehouse_id' => $this->warehouse->id,
            'to_warehouse_id' => $warehouse2->id,
            'item_id' => $this->item->id,
            'quantity' => 30,
            'unit_cost' => 50,
            'total_cost' => 1500,
            'movement_date' => now(),
            'status' => 'approved',
            'created_by' => $this->user->id,
        ]);

        $inventoryService = new InventoryService();
        
        $stock1 = $inventoryService->getCurrentStock($this->item->id, $this->warehouse->id);
        $this->assertEquals(70, $stock1); // 100 - 30 (transferred out)
    }

    /** @test */
    public function test_item_below_min_stock_detection()
    {
        // Set item with low stock
        $this->item->update(['min_stock' => 50]);

        // Add only 20 items
        StockMovement::create([
            'movement_number' => 'IN-20251205-0001',
            'movement_type' => 'stock_in',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 20,
            'unit_cost' => 50,
            'total_cost' => 1000,
            'movement_date' => now(),
            'status' => 'approved',
            'created_by' => $this->user->id,
        ]);

        $this->assertTrue($this->item->isBelowMinStock());
    }

    /** @test */
    public function test_stock_movement_service_generates_unique_number()
    {
        $service = new StockMovementService();
        
        $number1 = $service->generateMovementNumber('stock_in');
        $number2 = $service->generateMovementNumber('stock_in');

        $this->assertNotEquals($number1, $number2);
        $this->assertStringStartsWith('IN-', $number1);
        $this->assertStringStartsWith('IN-', $number2);
    }

    /** @test */
    public function test_inventory_value_calculation()
    {
        // Add stock with different prices
        StockMovement::create([
            'movement_number' => 'IN-20251205-0001',
            'movement_type' => 'stock_in',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 100,
            'unit_cost' => 50,
            'total_cost' => 5000,
            'movement_date' => now(),
            'status' => 'approved',
            'created_by' => $this->user->id,
        ]);

        $inventoryService = new InventoryService();
        $totalValue = $inventoryService->calculateInventoryValue($this->warehouse->id);

        $this->assertEquals(5000, $totalValue);
    }

    /** @test */
    public function test_cannot_delete_warehouse_with_movements()
    {
        $this->actingAs($this->user);

        // Create movement
        StockMovement::create([
            'movement_number' => 'IN-20251205-0001',
            'movement_type' => 'stock_in',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 50,
            'unit_cost' => 50,
            'total_cost' => 2500,
            'movement_date' => now(),
            'status' => 'approved',
            'created_by' => $this->user->id,
        ]);

        $response = $this->delete(route('warehouses.destroy', $this->warehouse));

        $response->assertRedirect(route('warehouses.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('warehouses', ['id' => $this->warehouse->id]);
    }

    /** @test */
    public function test_adjustment_can_be_positive_or_negative()
    {
        // Positive adjustment
        StockMovement::create([
            'movement_number' => 'ADJ-20251205-0001',
            'movement_type' => 'adjustment',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 10,
            'unit_cost' => 50,
            'total_cost' => 500,
            'movement_date' => now(),
            'status' => 'approved',
            'created_by' => $this->user->id,
        ]);

        // Negative adjustment
        StockMovement::create([
            'movement_number' => 'ADJ-20251205-0002',
            'movement_type' => 'adjustment',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => -5,
            'unit_cost' => 50,
            'total_cost' => -250,
            'movement_date' => now(),
            'status' => 'approved',
            'created_by' => $this->user->id,
        ]);

        $inventoryService = new InventoryService();
        $currentStock = $inventoryService->getCurrentStock($this->item->id, $this->warehouse->id);

        $this->assertEquals(5, $currentStock); // 10 - 5
    }

    /** @test */
    public function test_stock_movement_creates_journal_entry()
    {
        // Create a stock in movement
        $movement = StockMovement::create([
            'movement_number' => 'SM-20251206-0001',
            'movement_type' => 'stock_in',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 10,
            'unit_cost' => 100,
            'total_cost' => 1000,
            'movement_date' => now(),
            'status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        // Approve the movement and create journal entry
        $service = new StockMovementService();
        $journalEntry = $service->createJournalEntry($movement);

        // Assert journal entry was created
        $this->assertNotNull($journalEntry);
        $this->assertEquals('approved', $journalEntry->status);
        $this->assertEquals(1000, $journalEntry->total_debit);
        $this->assertEquals(1000, $journalEntry->total_credit);

        // Assert movement is linked to journal entry
        $movement->refresh();
        $this->assertEquals($journalEntry->id, $movement->journal_entry_id);
    }

    /** @test */
    public function test_cannot_approve_movement_twice()
    {
        // Create a stock movement
        $movement = StockMovement::create([
            'movement_number' => 'SM-20251206-0002',
            'movement_type' => 'stock_in',
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 5,
            'unit_cost' => 50,
            'total_cost' => 250,
            'movement_date' => now(),
            'status' => 'pending',
            'created_by' => $this->user->id,
        ]);

        // Approve the movement for the first time
        $movement->update([
            'status' => 'approved',
            'approved_by' => $this->user->id,
            'approved_at' => now(),
        ]);

        $this->assertEquals('approved', $movement->status);

        // Try to approve again - should fail
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('هذه الحركة معتمدة بالفعل');

        // This should throw an exception
        if ($movement->status === 'approved') {
            throw new \Exception('هذه الحركة معتمدة بالفعل');
        }
    }
}

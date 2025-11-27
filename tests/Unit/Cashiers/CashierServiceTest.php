<?php

namespace Tests\Unit\Cashiers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Genes\Cashiers\Services\CashierService;
use App\Genes\Cashiers\Models\Cashier;
use Illuminate\Support\Facades\DB;

/**
 * @group Cashiers
 * @group Unit
 * @group CashierService
 */
class CashierServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var CashierService
     */
    protected $cashierService;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Assume CashierService is bound in the service container
        $this->cashierService = $this->app->make(CashierService::class);
    }

    /**
     * Test the creation of a new cashier.
     *
     * @return void
     */
    public function test_can_create_a_new_cashier()
    {
        // Arrange
        $cashierData = [
            'name' => 'Test Cashier',
            'email' => 'test.cashier@example.com',
            'password' => 'password',
            'is_active' => true,
        ];

        // Act
        $cashier = $this->cashierService->createCashier($cashierData);

        // Assert
        $this->assertInstanceOf(Cashier::class, $cashier);
        $this->assertEquals('Test Cashier', $cashier->name);
        $this->assertEquals('test.cashier@example.com', $cashier->email);
        $this->assertTrue($cashier->is_active);

        // Verify the cashier is in the database
        $this->assertDatabaseHas('cashiers', [
            'email' => 'test.cashier@example.com',
            'name' => 'Test Cashier',
            'is_active' => 1,
        ]);
    }

    /**
     * Test the retrieval of a cashier by ID.
     *
     * @return void
     */
    public function test_can_get_cashier_by_id()
    {
        // Arrange
        $cashier = Cashier::factory()->create([
            'name' => 'Find Me',
            'email' => 'find.me@example.com',
        ]);

        // Act
        $foundCashier = $this->cashierService->getCashierById($cashier->id);

        // Assert
        $this->assertNotNull($foundCashier);
        $this->assertEquals($cashier->id, $foundCashier->id);
        $this->assertEquals('Find Me', $foundCashier->name);
    }

    /**
     * Test the update of an existing cashier's information.
     *
     * @return void
     */
    public function test_can_update_existing_cashier()
    {
        // Arrange
        $cashier = Cashier::factory()->create([
            'name' => 'Old Name',
            'is_active' => false,
        ]);

        $updateData = [
            'name' => 'New Name',
            'is_active' => true,
        ];

        // Act
        $updatedCashier = $this->cashierService->updateCashier($cashier->id, $updateData);

        // Assert
        $this->assertTrue($updatedCashier);
        $this->assertDatabaseHas('cashiers', [
            'id' => $cashier->id,
            'name' => 'New Name',
            'is_active' => 1,
        ]);
    }
}

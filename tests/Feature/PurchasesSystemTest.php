<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseReceiptItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\ChartAccount;
use App\Services\PurchaseOrderService;
use App\Services\PurchaseReceiptService;
use App\Services\PurchaseInvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Purchases System Test
 * اختبارات شاملة لنظام المشتريات v4.1.0
 * 
 * @package Tests\Feature
 */
class PurchasesSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $supplier;
    protected $warehouse;
    protected $item;
    protected $account;

    /**
     * Setup test environment
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Create test data
        $this->account = ChartAccount::factory()->create([
            'account_name' => 'حساب مورد تجريبي',
            'account_type' => 'liability',
        ]);

        $this->supplier = Supplier::factory()->create([
            'code' => 'SUP-001',
            'name' => 'مورد تجريبي',
            'account_id' => $this->account->id,
            'payment_terms' => 'credit',
            'credit_days' => 30,
        ]);

        $this->warehouse = Warehouse::factory()->create([
            'name' => 'مخزن رئيسي',
        ]);

        $this->item = Item::factory()->create([
            'code' => 'ITEM-001',
            'name' => 'صنف تجريبي',
            'unit_price' => 100,
        ]);
    }

    /**
     * Test 1: إنشاء مورد جديد
     * Test creating a new supplier
     */
    public function test_can_create_supplier()
    {
        $supplierData = [
            'code' => 'SUP-002',
            'name' => 'مورد جديد',
            'phone' => '0501234567',
            'email' => 'supplier@example.com',
            'payment_terms' => 'cash',
            'status' => 'active',
        ];

        $supplier = Supplier::create($supplierData);

        $this->assertDatabaseHas('suppliers', [
            'code' => 'SUP-002',
            'name' => 'مورد جديد',
        ]);

        $this->assertEquals('SUP-002', $supplier->code);
        $this->assertTrue($supplier->isActive());
    }

    /**
     * Test 2: إنشاء أمر شراء
     * Test creating a purchase order
     */
    public function test_can_create_purchase_order()
    {
        $service = app(PurchaseOrderService::class);

        $orderData = [
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'order_date' => now()->toDateString(),
            'expected_date' => now()->addDays(7)->toDateString(),
            'status' => 'draft',
        ];

        $order = $service->createOrder($orderData);

        $this->assertDatabaseHas('purchase_orders', [
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $this->assertNotNull($order->order_number);
        $this->assertTrue($order->isDraft());
    }

    /**
     * Test 3: إضافة أصناف لأمر الشراء
     * Test adding items to purchase order
     */
    public function test_can_add_items_to_purchase_order()
    {
        $service = app(PurchaseOrderService::class);

        $order = PurchaseOrder::factory()->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $itemData = [
            'item_id' => $this->item->id,
            'quantity' => 10,
            'unit_price' => 100,
            'tax_rate' => 15,
            'discount_rate' => 5,
        ];

        $item = $service->addItem($order, $itemData);

        $this->assertDatabaseHas('purchase_order_items', [
            'purchase_order_id' => $order->id,
            'item_id' => $this->item->id,
            'quantity' => 10,
        ]);

        $this->assertEquals(1075, $item->total_amount); // (10 * 100) - 5% + 15%
    }

    /**
     * Test 4: حساب إجماليات الأمر
     * Test calculating order totals
     */
    public function test_can_calculate_order_totals()
    {
        $service = app(PurchaseOrderService::class);

        $order = PurchaseOrder::factory()->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        // Add multiple items
        $service->addItem($order, [
            'item_id' => $this->item->id,
            'quantity' => 10,
            'unit_price' => 100,
            'tax_rate' => 15,
            'discount_rate' => 0,
        ]);

        $order->refresh();

        $this->assertEquals(1000, $order->subtotal);
        $this->assertEquals(150, $order->tax_amount);
        $this->assertEquals(1150, $order->total_amount);
    }

    /**
     * Test 5: اعتماد أمر الشراء
     * Test approving purchase order
     */
    public function test_can_approve_purchase_order()
    {
        $service = app(PurchaseOrderService::class);

        $order = PurchaseOrder::factory()->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'draft',
        ]);

        $result = $service->approveOrder($order);

        $this->assertTrue($result);
        $this->assertTrue($order->fresh()->isApproved());
        $this->assertEquals('confirmed', $order->fresh()->status);
    }

    /**
     * Test 6: إلغاء أمر الشراء
     * Test cancelling purchase order
     */
    public function test_can_cancel_purchase_order()
    {
        $service = app(PurchaseOrderService::class);

        $order = PurchaseOrder::factory()->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'draft',
        ]);

        $result = $service->cancelOrder($order, 'تغيير في المتطلبات');

        $this->assertTrue($result);
        $this->assertTrue($order->fresh()->isCancelled());
    }

    /**
     * Test 7: استلام بضاعة مع أمر شراء
     * Test receiving goods with purchase order
     */
    public function test_can_create_receipt_with_order()
    {
        $service = app(PurchaseReceiptService::class);

        $order = PurchaseOrder::factory()->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $receiptData = [
            'purchase_order_id' => $order->id,
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'receipt_date' => now()->toDateString(),
            'status' => 'pending',
        ];

        $receipt = $service->createReceipt($receiptData);

        $this->assertDatabaseHas('purchase_receipts', [
            'purchase_order_id' => $order->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $this->assertTrue($receipt->hasOrder());
    }

    /**
     * Test 8: استلام بضاعة بدون أمر شراء
     * Test receiving goods without purchase order
     */
    public function test_can_create_receipt_without_order()
    {
        $service = app(PurchaseReceiptService::class);

        $receiptData = [
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'receipt_date' => now()->toDateString(),
            'status' => 'pending',
        ];

        $receipt = $service->createReceipt($receiptData);

        $this->assertDatabaseHas('purchase_receipts', [
            'supplier_id' => $this->supplier->id,
        ]);

        $this->assertFalse($receipt->hasOrder());
    }

    /**
     * Test 9: اعتماد الاستلام وإنشاء حركة مخزون
     * Test approving receipt and creating stock movement
     */
    public function test_can_approve_receipt_and_create_stock_movement()
    {
        $service = app(PurchaseReceiptService::class);

        $receipt = PurchaseReceipt::factory()->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'status' => 'pending',
        ]);

        // Add item to receipt
        $service->addItem($receipt, [
            'item_id' => $this->item->id,
            'quantity' => 10,
            'unit_price' => 100,
        ]);

        $result = $service->approveReceipt($receipt);

        $this->assertTrue($result);
        $this->assertTrue($receipt->fresh()->isApproved());

        // Check stock movement created
        $this->assertDatabaseHas('stock_movements', [
            'reference_type' => 'purchase_receipt',
            'reference_id' => $receipt->id,
            'warehouse_id' => $this->warehouse->id,
            'item_id' => $this->item->id,
            'quantity' => 10,
        ]);
    }

    /**
     * Test 10: إنشاء فاتورة مورد
     * Test creating purchase invoice
     */
    public function test_can_create_purchase_invoice()
    {
        $service = app(PurchaseInvoiceService::class);

        $invoiceData = [
            'invoice_number' => 'INV-2025-001',
            'supplier_id' => $this->supplier->id,
            'invoice_date' => now()->toDateString(),
            'status' => 'draft',
        ];

        $invoice = $service->createInvoice($invoiceData);

        $this->assertDatabaseHas('purchase_invoices', [
            'invoice_number' => 'INV-2025-001',
            'supplier_id' => $this->supplier->id,
        ]);

        $this->assertNotNull($invoice->internal_number);
        $this->assertTrue($invoice->isDraft());
    }

    /**
     * Test 11: اعتماد الفاتورة وإنشاء قيد محاسبي
     * Test approving invoice and creating journal entry
     */
    public function test_can_approve_invoice_and_create_journal_entry()
    {
        $service = app(PurchaseInvoiceService::class);

        $invoice = PurchaseInvoice::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => 'draft',
            'total_amount' => 1000,
        ]);

        // Add item
        $service->addItem($invoice, [
            'item_id' => $this->item->id,
            'quantity' => 10,
            'unit_price' => 100,
            'tax_rate' => 0,
            'discount_rate' => 0,
        ]);

        $result = $service->approveInvoice($invoice);

        $this->assertTrue($result);
        $this->assertTrue($invoice->fresh()->isApproved());
        $this->assertTrue($invoice->fresh()->hasJournalEntry());

        // Check journal entry created
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => 'purchase_invoice',
            'reference_id' => $invoice->id,
        ]);
    }

    /**
     * Test 12: تسجيل دفعة للفاتورة
     * Test recording payment for invoice
     */
    public function test_can_record_payment_for_invoice()
    {
        $service = app(PurchaseInvoiceService::class);

        $invoice = PurchaseInvoice::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => 'approved',
            'total_amount' => 1000,
            'paid_amount' => 0,
            'remaining_amount' => 1000,
        ]);

        $paymentData = [
            'amount' => 500,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ];

        $service->recordPayment($invoice, $paymentData);

        $invoice->refresh();

        $this->assertEquals(500, $invoice->paid_amount);
        $this->assertEquals(500, $invoice->remaining_amount);
        $this->assertEquals('partially_paid', $invoice->payment_status);
    }

    /**
     * Test 13: حساب المبلغ المتبقي
     * Test calculating remaining amount
     */
    public function test_can_calculate_remaining_amount()
    {
        $invoice = PurchaseInvoice::factory()->create([
            'total_amount' => 1000,
            'paid_amount' => 300,
        ]);

        $remaining = $invoice->calculateRemainingAmount();

        $this->assertEquals(700, $remaining);
    }

    /**
     * Test 14: منع حذف مورد له عمليات
     * Test preventing deletion of supplier with transactions
     */
    public function test_cannot_delete_supplier_with_orders()
    {
        $supplier = Supplier::factory()->create();

        PurchaseOrder::factory()->create([
            'supplier_id' => $supplier->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        $supplier->delete();
    }

    /**
     * Test 15: التقارير - تقرير أوامر الشراء
     * Test reports - purchase orders report
     */
    public function test_can_generate_purchase_orders_report()
    {
        $service = app(\App\Services\PurchaseReportService::class);

        // Create test orders
        PurchaseOrder::factory()->count(5)->create([
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
        ]);

        $report = $service->getPurchaseOrdersReport([
            'start_date' => now()->subDays(30)->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $this->assertGreaterThanOrEqual(5, $report->count());
    }

    /**
     * Test 16: التقارير - تقرير المشتريات حسب المورد
     * Test reports - purchases by supplier
     */
    public function test_can_generate_purchases_by_supplier_report()
    {
        $service = app(\App\Services\PurchaseReportService::class);

        // Create test invoices
        PurchaseInvoice::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'status' => 'approved',
        ]);

        $report = $service->getPurchasesBySupplierReport([
            'start_date' => now()->subDays(30)->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $this->assertGreaterThan(0, $report->count());
    }

    /**
     * Test 17: التقارير - الفواتير المستحقة
     * Test reports - due invoices
     */
    public function test_can_generate_due_invoices_report()
    {
        $service = app(\App\Services\PurchaseReportService::class);

        // Create overdue invoice
        PurchaseInvoice::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => 'approved',
            'payment_status' => 'unpaid',
            'due_date' => now()->subDays(5)->toDateString(),
        ]);

        $report = $service->getDueInvoicesReport();

        $this->assertGreaterThan(0, $report->count());
    }

    /**
     * Test 18: إحصائيات لوحة التحكم
     * Test dashboard statistics
     */
    public function test_can_get_dashboard_statistics()
    {
        $service = app(\App\Services\PurchaseReportService::class);

        $stats = $service->getDashboardStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_orders', $stats);
        $this->assertArrayHasKey('total_invoices', $stats);
        $this->assertArrayHasKey('overdue_invoices', $stats);
    }
}

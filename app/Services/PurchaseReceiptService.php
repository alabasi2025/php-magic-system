<?php

namespace App\Services;

use App\Models\PurchaseReceipt;
use App\Models\PurchaseReceiptItem;
use App\Models\StockMovement;
use App\Models\ItemWarehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * Purchase Receipt Service
 * منطق الأعمال لاستلام البضاعة
 */
class PurchaseReceiptService
{
    protected $purchaseOrderService;

    public function __construct(PurchaseOrderService $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }

    /**
     * توليد رقم استلام فريد
     * Generate unique receipt number
     *
     * @return string
     */
    public function generateReceiptNumber(): string
    {
        $date = now()->format('Ymd');
        $lastReceipt = PurchaseReceipt::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastReceipt ? (intval(substr($lastReceipt->receipt_number, -4)) + 1) : 1;
        
        return 'PR-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * إنشاء استلام بضاعة جديد
     * Create new purchase receipt
     *
     * @param array $data
     * @return PurchaseReceipt
     * @throws Exception
     */
    public function createReceipt(array $data): PurchaseReceipt
    {
        try {
            DB::beginTransaction();

            // Generate receipt number if not provided
            if (!isset($data['receipt_number'])) {
                $data['receipt_number'] = $this->generateReceiptNumber();
            }

            // Set created_by if not provided
            if (!isset($data['created_by'])) {
                $data['created_by'] = Auth::id();
            }

            // Set receipt_date if not provided
            if (!isset($data['receipt_date'])) {
                $data['receipt_date'] = now()->toDateString();
            }

            // Create receipt
            $receipt = PurchaseReceipt::create($data);

            DB::commit();
            return $receipt;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل إنشاء استلام البضاعة: ' . $e->getMessage());
        }
    }

    /**
     * إضافة صنف لاستلام البضاعة
     * Add item to purchase receipt
     *
     * @param PurchaseReceipt $receipt
     * @param array $itemData
     * @return PurchaseReceiptItem
     * @throws Exception
     */
    public function addItem(PurchaseReceipt $receipt, array $itemData): PurchaseReceiptItem
    {
        try {
            DB::beginTransaction();

            // Calculate total amount
            $itemData['total_amount'] = $itemData['quantity'] * $itemData['unit_price'];
            $itemData['purchase_receipt_id'] = $receipt->id;

            // Create item
            $item = PurchaseReceiptItem::create($itemData);

            DB::commit();
            return $item;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل إضافة الصنف: ' . $e->getMessage());
        }
    }

    /**
     * اعتماد استلام البضاعة
     * Approve purchase receipt
     *
     * @param PurchaseReceipt $receipt
     * @return bool
     * @throws Exception
     */
    public function approveReceipt(PurchaseReceipt $receipt): bool
    {
        try {
            DB::beginTransaction();

            if ($receipt->isApproved()) {
                throw new Exception('استلام البضاعة معتمد بالفعل');
            }

            if ($receipt->isRejected()) {
                throw new Exception('لا يمكن اعتماد استلام بضاعة مرفوض');
            }

            // Update receipt status
            $receipt->approved_by = Auth::id();
            $receipt->approved_at = now();
            $receipt->status = 'approved';
            $receipt->save();

            // Create stock movements (التكامل مع المخازن)
            $this->createStockMovements($receipt);

            // Update purchase order received quantities if linked
            if ($receipt->purchase_order_id) {
                $this->updateOrderReceivedQuantities($receipt);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل اعتماد استلام البضاعة: ' . $e->getMessage());
        }
    }

    /**
     * التكامل مع المخازن: إنشاء حركة مخزون تلقائية
     * Integration with Inventory: Create stock movements
     *
     * @param PurchaseReceipt $receipt
     * @return void
     * @throws Exception
     */
    public function createStockMovements(PurchaseReceipt $receipt): void
    {
        try {
            foreach ($receipt->items as $item) {
                // Create stock movement for each item
                StockMovement::create([
                    'movement_type' => 'stock_in',
                    'warehouse_id' => $receipt->warehouse_id,
                    'item_id' => $item->item_id,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_price,
                    'total_cost' => $item->total_amount,
                    'reference_type' => 'purchase_receipt',
                    'reference_id' => $receipt->id,
                    'movement_date' => $receipt->receipt_date,
                    'status' => 'approved',
                    'notes' => 'استلام بضاعة - ' . $receipt->receipt_number,
                    'created_by' => $receipt->created_by,
                ]);
                
                // Update item warehouse inventory
                $this->updateInventory(
                    $item->item_id,
                    $receipt->warehouse_id,
                    $item->quantity,
                    $item->unit_price
                );
            }
        } catch (Exception $e) {
            throw new Exception('فشل إنشاء حركات المخزون: ' . $e->getMessage());
        }
    }
    
    /**
     * تحديث رصيد المخزون
     * Update inventory balance
     *
     * @param int $itemId
     * @param int $warehouseId
     * @param float $quantity
     * @param float $cost
     * @return void
     */
    protected function updateInventory(int $itemId, int $warehouseId, float $quantity, float $cost): void
    {
        $inventory = ItemWarehouse::firstOrNew([
            'item_id' => $itemId,
            'warehouse_id' => $warehouseId,
        ]);
        
        $inventory->addQuantity($quantity, $cost);
    }

    /**
     * تحديث الكميات المستلمة في أمر الشراء
     * Update received quantities in purchase order
     *
     * @param PurchaseReceipt $receipt
     * @return void
     * @throws Exception
     */
    public function updateOrderReceivedQuantities(PurchaseReceipt $receipt): void
    {
        try {
            if (!$receipt->purchaseOrder) {
                return;
            }

            $order = $receipt->purchaseOrder;

            foreach ($receipt->items as $receiptItem) {
                // Find matching item in purchase order
                $orderItem = $order->items()
                    ->where('item_id', $receiptItem->item_id)
                    ->first();

                if ($orderItem) {
                    // Update received quantity
                    $orderItem->received_quantity += $receiptItem->quantity;
                    $orderItem->save();
                }
            }

            // Update order receipt status
            $this->purchaseOrderService->updateReceiptStatus($order);
        } catch (Exception $e) {
            throw new Exception('فشل تحديث الكميات المستلمة: ' . $e->getMessage());
        }
    }

    /**
     * رفض استلام البضاعة
     * Reject purchase receipt
     *
     * @param PurchaseReceipt $receipt
     * @param string $reason
     * @return bool
     * @throws Exception
     */
    public function rejectReceipt(PurchaseReceipt $receipt, string $reason = ''): bool
    {
        try {
            if ($receipt->isApproved()) {
                throw new Exception('لا يمكن رفض استلام بضاعة معتمد');
            }

            $receipt->status = 'rejected';
            $receipt->approved_by = Auth::id();
            $receipt->approved_at = now();
            
            if ($reason) {
                $receipt->notes = ($receipt->notes ? $receipt->notes . "\n" : '') . "سبب الرفض: " . $reason;
            }
            
            $receipt->save();

            return true;
        } catch (Exception $e) {
            throw new Exception('فشل رفض استلام البضاعة: ' . $e->getMessage());
        }
    }

    /**
     * حذف صنف من استلام البضاعة
     * Delete item from purchase receipt
     *
     * @param PurchaseReceiptItem $item
     * @return bool
     * @throws Exception
     */
    public function deleteItem(PurchaseReceiptItem $item): bool
    {
        try {
            DB::beginTransaction();

            $receipt = $item->purchaseReceipt;
            
            if ($receipt->isApproved()) {
                throw new Exception('لا يمكن حذف صنف من استلام معتمد');
            }

            $item->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل حذف الصنف: ' . $e->getMessage());
        }
    }
}

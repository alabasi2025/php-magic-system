<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * Purchase Order Service
 * منطق الأعمال لأوامر الشراء
 */
class PurchaseOrderService
{
    /**
     * توليد رقم أمر شراء فريد
     * Generate unique purchase order number
     *
     * @return string
     */
    public function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $lastOrder = PurchaseOrder::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastOrder ? (intval(substr($lastOrder->order_number, -4)) + 1) : 1;
        
        return 'PO-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * إنشاء أمر شراء جديد
     * Create new purchase order
     *
     * @param array $data
     * @return PurchaseOrder
     * @throws Exception
     */
    public function createOrder(array $data): PurchaseOrder
    {
        try {
            DB::beginTransaction();

            // Generate order number if not provided
            if (!isset($data['order_number'])) {
                $data['order_number'] = $this->generateOrderNumber();
            }

            // Set created_by if not provided
            if (!isset($data['created_by'])) {
                $data['created_by'] = Auth::id();
            }

            // Set order_date if not provided
            if (!isset($data['order_date'])) {
                $data['order_date'] = now()->toDateString();
            }

            // Create purchase order
            $order = PurchaseOrder::create($data);

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل إنشاء أمر الشراء: ' . $e->getMessage());
        }
    }

    /**
     * إضافة صنف لأمر الشراء
     * Add item to purchase order
     *
     * @param PurchaseOrder $order
     * @param array $itemData
     * @return PurchaseOrderItem
     * @throws Exception
     */
    public function addItem(PurchaseOrder $order, array $itemData): PurchaseOrderItem
    {
        try {
            DB::beginTransaction();

            // Calculate total amount for the item
            $subtotal = $itemData['quantity'] * $itemData['unit_price'];
            $discountAmount = $subtotal * ($itemData['discount_rate'] ?? 0) / 100;
            $amountAfterDiscount = $subtotal - $discountAmount;
            $taxAmount = $amountAfterDiscount * ($itemData['tax_rate'] ?? 0) / 100;
            $itemData['total_amount'] = $amountAfterDiscount + $taxAmount;

            // Add purchase_order_id
            $itemData['purchase_order_id'] = $order->id;

            // Create item
            $item = PurchaseOrderItem::create($itemData);

            // Recalculate order totals
            $this->calculateTotals($order);

            DB::commit();
            return $item;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل إضافة الصنف: ' . $e->getMessage());
        }
    }

    /**
     * حساب إجماليات أمر الشراء
     * Calculate purchase order totals
     *
     * @param PurchaseOrder $order
     * @return void
     */
    public function calculateTotals(PurchaseOrder $order): void
    {
        $items = $order->items;

        $subtotal = 0;
        $taxAmount = 0;
        $discountAmount = 0;

        foreach ($items as $item) {
            $itemSubtotal = $item->quantity * $item->unit_price;
            $itemDiscount = $itemSubtotal * ($item->discount_rate / 100);
            $itemAfterDiscount = $itemSubtotal - $itemDiscount;
            $itemTax = $itemAfterDiscount * ($item->tax_rate / 100);

            $subtotal += $itemSubtotal;
            $discountAmount += $itemDiscount;
            $taxAmount += $itemTax;
        }

        $order->subtotal = $subtotal;
        $order->discount_amount = $discountAmount;
        $order->tax_amount = $taxAmount;
        $order->total_amount = $subtotal - $discountAmount + $taxAmount;
        $order->save();
    }

    /**
     * اعتماد أمر الشراء
     * Approve purchase order
     *
     * @param PurchaseOrder $order
     * @return bool
     * @throws Exception
     */
    public function approveOrder(PurchaseOrder $order): bool
    {
        try {
            if ($order->isApproved()) {
                throw new Exception('أمر الشراء معتمد بالفعل');
            }

            if ($order->isCancelled()) {
                throw new Exception('لا يمكن اعتماد أمر شراء ملغي');
            }

            $order->approved_by = Auth::id();
            $order->approved_at = now();
            $order->status = 'confirmed';
            $order->save();

            return true;
        } catch (Exception $e) {
            throw new Exception('فشل اعتماد أمر الشراء: ' . $e->getMessage());
        }
    }

    /**
     * إلغاء أمر الشراء
     * Cancel purchase order
     *
     * @param PurchaseOrder $order
     * @param string $reason
     * @return bool
     * @throws Exception
     */
    public function cancelOrder(PurchaseOrder $order, string $reason = ''): bool
    {
        try {
            if ($order->isCancelled()) {
                throw new Exception('أمر الشراء ملغي بالفعل');
            }

            if ($order->isFullyReceived()) {
                throw new Exception('لا يمكن إلغاء أمر شراء تم استلامه بالكامل');
            }

            $order->status = 'cancelled';
            if ($reason) {
                $order->notes = ($order->notes ? $order->notes . "\n" : '') . "سبب الإلغاء: " . $reason;
            }
            $order->save();

            return true;
        } catch (Exception $e) {
            throw new Exception('فشل إلغاء أمر الشراء: ' . $e->getMessage());
        }
    }

    /**
     * تحديث حالة الاستلام لأمر الشراء
     * Update receipt status of purchase order
     *
     * @param PurchaseOrder $order
     * @return void
     */
    public function updateReceiptStatus(PurchaseOrder $order): void
    {
        $totalOrdered = $order->getTotalOrderedQuantity();
        $totalReceived = $order->getTotalReceivedQuantity();

        if ($totalReceived == 0) {
            // No items received yet
            if ($order->status !== 'cancelled' && $order->status !== 'draft') {
                $order->status = 'confirmed';
            }
        } elseif ($totalReceived >= $totalOrdered) {
            // All items received
            $order->status = 'received';
        } else {
            // Partially received
            $order->status = 'partially_received';
        }

        $order->save();
    }

    /**
     * تحديث صنف في أمر الشراء
     * Update item in purchase order
     *
     * @param PurchaseOrderItem $item
     * @param array $data
     * @return PurchaseOrderItem
     * @throws Exception
     */
    public function updateItem(PurchaseOrderItem $item, array $data): PurchaseOrderItem
    {
        try {
            DB::beginTransaction();

            // Update item data
            $item->update($data);

            // Recalculate item total
            $subtotal = $item->quantity * $item->unit_price;
            $discountAmount = $subtotal * ($item->discount_rate / 100);
            $amountAfterDiscount = $subtotal - $discountAmount;
            $taxAmount = $amountAfterDiscount * ($item->tax_rate / 100);
            $item->total_amount = $amountAfterDiscount + $taxAmount;
            $item->save();

            // Recalculate order totals
            $this->calculateTotals($item->purchaseOrder);

            DB::commit();
            return $item;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل تحديث الصنف: ' . $e->getMessage());
        }
    }

    /**
     * حذف صنف من أمر الشراء
     * Delete item from purchase order
     *
     * @param PurchaseOrderItem $item
     * @return bool
     * @throws Exception
     */
    public function deleteItem(PurchaseOrderItem $item): bool
    {
        try {
            DB::beginTransaction();

            $order = $item->purchaseOrder;
            $item->delete();

            // Recalculate order totals
            $this->calculateTotals($order);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل حذف الصنف: ' . $e->getMessage());
        }
    }
}

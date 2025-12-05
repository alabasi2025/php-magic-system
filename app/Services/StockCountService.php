<?php

namespace App\Services;

use App\Models\StockCount;
use App\Models\StockCountDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Exception;

/**
 * خدمة إدارة عمليات الجرد.
 */
class StockCountService
{
    /**
     * إنشاء عملية جرد جديدة.
     *
     * @param array $data البيانات الأساسية للجرد (warehouse_id, date, notes)
     * @param Collection $items مجموعة الأصناف المراد جردها (item_id, system_quantity)
     * @param int $userId معرف المستخدم المنشئ
     * @return StockCount
     * @throws Exception
     */
    public function createStockCount(array $data, Collection $items, int $userId): StockCount
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            // توليد رقم فريد لعملية الجرد (يمكن تحسين هذه الآلية)
            $data['number'] = 'SC-' . time();
            $data['created_by'] = $userId;
            $data['status'] = 'Draft';

            /** @var StockCount $stockCount */
            $stockCount = StockCount::create($data);

            // إضافة تفاصيل الجرد
            $details = $items->map(function ($item) use ($stockCount) {
                return new StockCountDetail([
                    'stock_count_id' => $stockCount->id,
                    'item_id' => $item['item_id'],
                    'system_quantity' => $item['system_quantity'],
                    'actual_quantity' => null, // الكمية الفعلية تكون فارغة في البداية
                    'difference' => 0,
                    'notes' => $item['notes'] ?? null,
                ]);
            });

            $stockCount->details()->saveMany($details);

            return $stockCount;
        });
    }

    /**
     * إدخال الكميات الفعلية وحساب الفروقات.
     *
     * @param StockCount $stockCount عملية الجرد
     * @param array $detailsData بيانات التفاصيل (id, actual_quantity, notes)
     * @return StockCount
     * @throws Exception
     */
    public function enterActualQuantities(StockCount $stockCount, array $detailsData): StockCount
    {
        if ($stockCount->status !== 'Draft') {
            throw new Exception('لا يمكن تعديل الكميات إلا في حالة المسودة.');
        }

        return DB::transaction(function () use ($stockCount, $detailsData) {
            foreach ($detailsData as $detailData) {
                /** @var StockCountDetail $detail */
                $detail = $stockCount->details()->findOrFail($detailData['id']);

                $actualQuantity = (float) $detailData['actual_quantity'];
                $systemQuantity = (float) $detail->system_quantity;

                $detail->actual_quantity = $actualQuantity;
                $detail->difference = $actualQuantity - $systemQuantity;
                $detail->notes = $detailData['notes'] ?? $detail->notes;
                $detail->save();
            }

            // تحديث حالة الجرد إلى قيد المراجعة
            $stockCount->status = 'Pending Approval';
            $stockCount->save();

            return $stockCount;
        });
    }

    /**
     * الموافقة على الجرد وتعديل المخزون بناءً على الفروقات.
     *
     * @param StockCount $stockCount عملية الجرد
     * @param int $approverId معرف المستخدم الموافق
     * @return StockCount
     * @throws Exception
     */
    public function approveAndAdjustStock(StockCount $stockCount, int $approverId): StockCount
    {
        if ($stockCount->status !== 'Pending Approval') {
            throw new Exception('لا يمكن الموافقة إلا على الجرود التي قيد المراجعة.');
        }

        return DB::transaction(function () use ($stockCount, $approverId) {
            // افتراض وجود خدمة لتعديل المخزون
            $stockAdjustmentService = app(StockAdjustmentService::class);

            foreach ($stockCount->details as $detail) {
                if ($detail->difference != 0) {
                    // تطبيق التعديل على المخزون الرئيسي
                    // إذا كان الفرق موجباً: زيادة في المخزون
                    // إذا كان الفرق سالباً: نقص في المخزون
                    $stockAdjustmentService->adjustStock(
                        $detail->item_id,
                        $stockCount->warehouse_id,
                        $detail->difference,
                        'Stock Count Adjustment: ' . $stockCount->number
                    );
                }
            }

            // تحديث حالة الجرد
            $stockCount->status = 'Adjusted';
            $stockCount->approved_by = $approverId;
            $stockCount->save();

            return $stockCount;
        });
    }

    /**
     * حذف عملية جرد في حالة المسودة.
     *
     * @param StockCount $stockCount عملية الجرد
     * @return bool
     * @throws Exception
     */
    public function deleteStockCount(StockCount $stockCount): bool
    {
        if ($stockCount->status !== 'Draft') {
            throw new Exception('لا يمكن حذف عملية جرد إلا في حالة المسودة.');
        }

        return $stockCount->delete();
    }
}

// ملاحظة: يجب إنشاء خدمة StockAdjustmentService لتطبيق التعديلات على جدول المخزون الفعلي (Stock)
class StockAdjustmentService
{
    public function adjustStock(int $itemId, int $warehouseId, float $quantity, string $reason): void
    {
        // منطق تعديل المخزون الفعلي هنا
        // مثال: البحث عن سجل المخزون وتحديث الكمية
        // Stock::where('item_id', $itemId)
        //      ->where('warehouse_id', $warehouseId)
        //      ->increment('quantity', $quantity);

        // في هذا المثال، سنكتفي بوضع تعليق يوضح المنطق
        // echo "Adjusting stock for Item $itemId in Warehouse $warehouseId by $quantity. Reason: $reason\n";
    }
}

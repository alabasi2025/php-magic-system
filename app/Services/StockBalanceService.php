<?php

namespace App\Services;

use App\Models\StockBalance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StockBalanceService
{
    /**
     * حساب وتحديث متوسط التكلفة والقيمة الإجمالية لرصيد معين.
     *
     * @param StockBalance $balance
     * @param float $newCost التكلفة الجديدة للكمية المضافة/المعدلة
     * @param float $newQuantity الكمية المضافة/المعدلة (يمكن أن تكون سالبة للسحب)
     * @return StockBalance
     */
    public function calculateAndSaveAverageCost(StockBalance $balance, float $newCost, float $newQuantity): StockBalance
    {
        // يجب أن يتم استدعاء هذه الدالة ضمن عملية (Transaction) لضمان سلامة البيانات

        // الرصيد الحالي قبل التحديث
        $oldQuantity = $balance->quantity;
        $oldTotalValue = $balance->total_value;

        // إذا كانت الكمية الحالية صفر، فإن متوسط التكلفة هو التكلفة الجديدة
        if ($oldQuantity <= 0) {
            $balance->average_cost = $newCost;
            $balance->total_value = $newQuantity * $newCost;
            $balance->quantity = $newQuantity;
        } else {
            // حساب القيمة الإجمالية الجديدة
            $newTotalValue = $oldTotalValue + ($newQuantity * $newCost);
            $newTotalQuantity = $oldQuantity + $newQuantity;

            // تحديث الكمية
            $balance->quantity = $newTotalQuantity;

            // تحديث متوسط التكلفة والقيمة الإجمالية
            if ($newTotalQuantity > 0) {
                $balance->average_cost = $newTotalValue / $newTotalQuantity;
                $balance->total_value = $newTotalValue;
            } else {
                // إذا أصبحت الكمية صفر أو أقل، يتم تصفير القيمة والمتوسط
                $balance->average_cost = 0;
                $balance->total_value = 0;
                $balance->quantity = 0; // ضمان عدم وجود كمية سالبة في الرصيد النهائي
            }
        }

        $balance->last_cost = $newCost;
        $balance->last_updated = now();
        $balance->save();

        return $balance;
    }

    /**
     * الحصول على تنبيهات الأصناف التي وصلت إلى الحد الأدنى للمخزون.
     *
     * @param int|null $warehouseId
     * @return Collection
     */
    public function getMinimumStockAlerts(?int $warehouseId = null): Collection
    {
        // نفترض أن نموذج Item يحتوي على حقل 'minimum_stock'
        $query = StockBalance::with('item', 'warehouse')
            ->join('items', 'items.id', '=', 'stock_balances.item_id')
            ->whereRaw('stock_balances.quantity <= items.minimum_stock');

        if ($warehouseId) {
            $query->where('stock_balances.warehouse_id', $warehouseId);
        }

        return $query->select('stock_balances.*')->get();
    }

    /**
     * تقرير الأصناف الراكدة (Slow-moving items report).
     *
     * يتم تعريف الصنف الراكد بناءً على عدم وجود حركة (سحب) له خلال فترة زمنية محددة (مثلاً 90 يوماً).
     * هذه الدالة تحتاج إلى بيانات حركات المخزون (Inventory Movements) التي لم يتم تطويرها هنا،
     * لذا سنقوم بتقديم حل مبسط يعتمد على حقل last_updated.
     *
     * @param int $days عدد الأيام التي تعتبر بعدها الحركة راكدة
     * @param int|null $warehouseId
     * @return Collection
     */
    public function getSlowMovingItemsReport(int $days = 90, ?int $warehouseId = null): Collection
    {
        $dateThreshold = now()->subDays($days);

        $query = StockBalance::with('item', 'warehouse')
            ->where('quantity', '>', 0) // الأصناف التي لا يزال لديها رصيد
            ->where('last_updated', '<', $dateThreshold); // لم يتم تحديثها (أي لم تحدث عليها حركة) منذ فترة

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return $query->get();
    }
}

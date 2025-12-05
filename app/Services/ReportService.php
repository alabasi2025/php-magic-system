<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * توليد تقرير رصيد المخزون الحالي.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getInventoryBalance()
    {
        // يتم افتراض أن حقل current_stock في جدول items يتم تحديثه تلقائياً
        // أو يتم حسابه بناءً على حركات المخزون. هنا نعتمد على الحقل الموجود.
        return Item::select('id', 'name', 'current_stock', 'cost_price')
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                // إضافة قيمة المخزون لسهولة العرض في التقرير
                $item->inventory_value = $item->current_stock * $item->cost_price;
                return $item;
            });
    }

    /**
     * توليد تقرير حركة الأصناف خلال فترة محددة.
     *
     * @param string $startDate
     * @param string $endDate
     * @param int|null $itemId
     * @return \Illuminate\Support\Collection
     */
    public function getItemMovement(string $startDate, string $endDate, ?int $itemId = null)
    {
        $query = StockTransaction::with('item')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc');

        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        return $query->get();
    }

    /**
     * توليد تقرير تقييم المخزون (القيمة الإجمالية للمخزون).
     *
     * @return float
     */
    public function getInventoryValuation(): float
    {
        // تقييم المخزون = مجموع (الرصيد الحالي * سعر التكلفة)
        return Item::select(DB::raw('SUM(current_stock * cost_price) as total_value'))
            ->value('total_value') ?? 0.00;
    }

    /**
     * توليد تقرير الأصناف تحت الحد الأدنى للمخزون.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBelowMinimumStock()
    {
        return Item::whereColumn('current_stock', '<', 'min_stock_level')
            ->orderBy('name')
            ->get();
    }

    /**
     * توليد تقرير الأصناف الراكدة (لم تحدث لها حركة خلال فترة).
     *
     * @param string $periodDays عدد الأيام التي تعتبر بعدها الأصناف راكدة
     * @return \Illuminate\Support\Collection
     */
    public function getSlowMovingItems(int $periodDays = 90)
    {
        $cutoffDate = now()->subDays($periodDays);

        // الأصناف التي لم تحدث لها أي حركة بعد تاريخ القطع
        $itemsWithRecentMovement = StockTransaction::where('transaction_date', '>=', $cutoffDate)
            ->pluck('item_id')
            ->unique();

        return Item::whereNotIn('id', $itemsWithRecentMovement)
            ->orderBy('name')
            ->get();
    }

    /**
     * توليد تقرير الأصناف الأكثر حركة (حسب عدد الحركات).
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getMostActiveItems(string $startDate, string $endDate, int $limit = 10)
    {
        return StockTransaction::select('item_id', DB::raw('COUNT(*) as total_movements'))
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('item_id')
            ->orderByDesc('total_movements')
            ->limit($limit)
            ->with('item')
            ->get();
    }

    /**
     * توليد تقرير المشتريات خلال فترة محددة.
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getPurchasesReport(string $startDate, string $endDate)
    {
        return Purchase::with('item')
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->select(DB::raw('item_id, SUM(quantity) as total_quantity, SUM(total_price) as total_cost'))
            ->groupBy('item_id')
            ->orderByDesc('total_cost')
            ->get();
    }

    /**
     * توليد تقرير المبيعات خلال فترة محددة.
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getSalesReport(string $startDate, string $endDate)
    {
        return Sale::with('item')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->select(DB::raw('item_id, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue'))
            ->groupBy('item_id')
            ->orderByDesc('total_revenue')
            ->get();
    }
}

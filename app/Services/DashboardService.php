<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMovement;
use App\Models\Alert;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * خدمة لوحة التحكم (DashboardService)
 * مسؤولة عن استخراج البيانات والإحصائيات اللازمة لعرضها في لوحة التحكم.
 */
class DashboardService
{
    /**
     * استخراج الإحصائيات السريعة (Quick Stats).
     *
     * @return array
     */
    public function getQuickStats(): array
    {
        // حساب تاريخ بداية الشهر الحالي
        $startOfMonth = Carbon::now()->startOfMonth();

        // حساب إجمالي المنتجات
        $totalProducts = Product::count();
        // حساب إجمالي المخازن النشطة
        $totalWarehouses = Warehouse::where('is_active', true)->count();
        // حساب إجمالي حركات الإدخال (In) خلال الشهر الحالي
        $inMovementsThisMonth = StockMovement::where('type', 'in')
            ->where('movement_date', '>=', $startOfMonth)
            ->sum('quantity');
        // حساب إجمالي حركات الإخراج (Out) خلال الشهر الحالي
        $outMovementsThisMonth = StockMovement::where('type', 'out')
            ->where('movement_date', '>=', $startOfMonth)
            ->sum('quantity');

        // ملاحظة: قيمة المخزون الإجمالية تتطلب نموذج تسعير غير متوفر حالياً، لذا سنستخدم إجمالي الكمية.
        $totalStockQuantity = Product::sum('current_stock');

        return [
            'total_products' => $totalProducts,
            'total_warehouses' => $totalWarehouses,
            'total_stock_quantity' => $totalStockQuantity,
            'in_movements_this_month' => $inMovementsThisMonth,
            'out_movements_this_month' => $outMovementsThisMonth,
        ];
    }

    /**
     * استخراج بيانات الرسوم البيانية لحركات المخزون.
     * (مثال: إجمالي الحركات حسب النوع خلال آخر 30 يوماً)
     *
     * @return array
     */
    public function getStockChartData(): array
    {
        $startDate = Carbon::now()->subDays(30)->startOfDay();

        $movements = StockMovement::select(
                DB::raw('DATE(movement_date) as date'),
                DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as total_in'),
                DB::raw('SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_out')
            )
            ->where('movement_date', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // تهيئة البيانات للرسم البياني
        $labels = $movements->pluck('date')->toArray();
        $data_in = $movements->pluck('total_in')->toArray();
        $data_out = $movements->pluck('total_out')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'إدخال (In)',
                    'data' => $data_in,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ],
                [
                    'label' => 'إخراج (Out)',
                    'data' => $data_out,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                ],
            ],
        ];
    }

    /**
     * استخراج التنبيهات النشطة (Alerts).
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveAlerts(int $limit = 5)
    {
        // تنبيهات انخفاض المخزون (Low Stock)
        $lowStockAlerts = Product::whereRaw('current_stock <= min_stock_level')
            ->select('name', 'current_stock', 'min_stock_level')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                return (object) [
                    'type' => 'low_stock',
                    'message' => "المنتج '{$product->name}' لديه مخزون منخفض: {$product->current_stock} (الحد الأدنى: {$product->min_stock_level})",
                    'created_at' => Carbon::now(), // تاريخ وهمي للتنبيهات المباشرة
                ];
            })->toArray();

        // التنبيهات المسجلة في جدول التنبيهات
        $generalAlerts = Alert::where('is_resolved', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($alert) {
                return (object) [
                    'type' => $alert->type,
                    'message' => $alert->message,
                    'created_at' => $alert->created_at,
                ];
            })->toArray();

        // دمج التنبيهات والحد من العدد الإجمالي
        $allAlerts = array_merge($lowStockAlerts, $generalAlerts);
        // فرز التنبيهات حسب تاريخ الإنشاء (الأحدث أولاً)
        usort($allAlerts, function ($a, $b) {
            return $b->created_at <=> $a->created_at;
        });

        return array_slice($allAlerts, 0, $limit);
    }

    /**
     * استخراج آخر حركات المخزون.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentMovements(int $limit = 10)
    {
        return StockMovement::with(['product', 'fromWarehouse', 'toWarehouse'])
            ->orderBy('movement_date', 'desc')
            ->limit($limit)
            ->get();
    }
}

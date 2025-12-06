<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Item;
use App\Models\StockMovement;
use App\Services\InventoryReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * InventoryReportController
 * 
 * Handles inventory reporting and analytics.
 */
class InventoryReportController extends Controller
{
    protected $reportService;

    public function __construct(InventoryReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display inventory dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_warehouses' => Warehouse::active()->count(),
            'total_items' => Item::active()->count(),
            'total_movements_today' => StockMovement::whereDate('movement_date', today())->count(),
            'pending_approvals' => StockMovement::pending()->count(),
            'items_below_min_stock' => $this->reportService->getItemsBelowMinStock()->count(),
        ];

        // Recent movements
        $recentMovements = StockMovement::with(['warehouse', 'item', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        // Stock value by warehouse
        $stockValueByWarehouse = $this->reportService->getStockValueByWarehouse();

        return view('inventory.dashboard', compact('stats', 'recentMovements', 'stockValueByWarehouse'));
    }

    /**
     * Current stock report.
     */
    public function currentStockReport(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        $stockData = $this->reportService->getCurrentStockReport($warehouseId);
        $warehouses = Warehouse::active()->get();

        return view('inventory.reports.stock-report', compact('stockData', 'warehouses', 'warehouseId'));
    }

    /**
     * Stock movements report.
     */
    public function movementsReport(Request $request)
    {
        $filters = $request->only(['warehouse_id', 'item_id', 'movement_type', 'date_from', 'date_to']);
        $movements = $this->reportService->getMovementsReport($filters);
        
        $warehouses = Warehouse::active()->get();
        $items = Item::active()->get();

        return view('inventory.reports.movements-report', compact('movements', 'warehouses', 'items', 'filters'));
    }

    /**
     * Items below minimum stock report.
     */
    public function belowMinStockReport()
    {
        $items = $this->reportService->getItemsBelowMinStock();
        return view('inventory.reports.below-min-stock', compact('items'));
    }

    /**
     * Dormant items report (no movement in last 90 days).
     */
    public function dormantItemsReport(Request $request)
    {
        $days = $request->get('days', 90);
        $items = $this->reportService->getDormantItems($days);
        
        return view('inventory.reports.dormant-items', compact('items', 'days'));
    }

    /**
     * Stock value report.
     */
    public function stockValueReport(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        $valueData = $this->reportService->getStockValueReport($warehouseId);
        $warehouses = Warehouse::active()->get();

        $totalValue = $valueData->sum('stock_value');

        return view('inventory.reports.stock-value', compact('valueData', 'warehouses', 'warehouseId', 'totalValue'));
    }

    /**
     * Item movement history.
     */
    public function itemMovementHistory(Request $request, Item $item)
    {
        $dateFrom = $request->get('date_from', now()->subMonths(3)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $movements = StockMovement::with(['warehouse', 'toWarehouse', 'creator', 'approver'])
            ->where('item_id', $item->id)
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->orderBy('movement_date', 'desc')
            ->get();

        return view('inventory.reports.item-history', compact('item', 'movements', 'dateFrom', 'dateTo'));
    }

    /**
     * Export current stock report to Excel.
     */
    public function exportCurrentStock(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        $stockData = $this->reportService->getCurrentStockReport($warehouseId);

        // Simple CSV export
        $filename = 'current_stock_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($stockData) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility with Arabic
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, ['رمز الصنف', 'اسم الصنف', 'المخزن', 'الكمية الحالية', 'الوحدة', 'سعر الوحدة', 'القيمة الإجمالية', 'الحد الأدنى', 'الحد الأقصى']);
            
            // Data
            foreach ($stockData as $row) {
                fputcsv($file, [
                    $row->sku,
                    $row->item_name,
                    $row->warehouse_name,
                    $row->current_stock,
                    $row->unit_name,
                    $row->unit_price,
                    $row->stock_value,
                    $row->min_stock,
                    $row->max_stock,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

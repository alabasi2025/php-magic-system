<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Warehouse;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * InventoryReportService
 * 
 * Generates various inventory reports and analytics.
 */
class InventoryReportService
{
    /**
     * Get current stock report.
     */
    public function getCurrentStockReport(?int $warehouseId = null): Collection
    {
        $query = DB::table('stock_movements')
            ->join('items', 'stock_movements.item_id', '=', 'items.id')
            ->join('item_units', 'items.unit_id', '=', 'item_units.id')
            ->join('warehouses', 'stock_movements.warehouse_id', '=', 'warehouses.id')
            ->where('stock_movements.status', 'approved');

        if ($warehouseId) {
            $query->where('stock_movements.warehouse_id', $warehouseId);
        }

        return $query->select(
                'items.id as item_id',
                'items.sku',
                'items.name as item_name',
                'warehouses.id as warehouse_id',
                'warehouses.name as warehouse_name',
                'item_units.name as unit_name',
                'items.min_stock',
                'items.max_stock',
                'items.unit_price',
                DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END) as current_stock'),
                DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END) * items.unit_price as stock_value')
            )
            ->groupBy('items.id', 'items.sku', 'items.name', 'warehouses.id', 'warehouses.name', 'item_units.name', 'items.min_stock', 'items.max_stock', 'items.unit_price')
            ->having('current_stock', '>', 0)
            ->orderBy('items.name')
            ->get();
    }

    /**
     * Get stock movements report with filters.
     */
    public function getMovementsReport(array $filters = []): Collection
    {
        $query = StockMovement::with(['warehouse', 'toWarehouse', 'item.unit', 'creator', 'approver'])
            ->where('status', 'approved');

        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['item_id'])) {
            $query->where('item_id', $filters['item_id']);
        }

        if (!empty($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('movement_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('movement_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('movement_date', 'desc')->get();
    }

    /**
     * Get items below minimum stock level.
     */
    public function getItemsBelowMinStock(): Collection
    {
        return Item::with('unit')
            ->select('items.*')
            ->selectRaw('(SELECT COALESCE(SUM(
                CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END
            ), 0) FROM stock_movements WHERE stock_movements.item_id = items.id AND stock_movements.status = "approved") as current_stock')
            ->havingRaw('current_stock < items.min_stock')
            ->get();
    }

    /**
     * Get dormant items (no movement in specified days).
     */
    public function getDormantItems(int $days = 90): Collection
    {
        $cutoffDate = now()->subDays($days);

        return Item::with('unit')
            ->select('items.*')
            ->leftJoin('stock_movements', 'items.id', '=', 'stock_movements.item_id')
            ->selectRaw('MAX(stock_movements.movement_date) as last_movement_date')
            ->groupBy('items.id', 'items.sku', 'items.name', 'items.description', 'items.unit_id', 'items.min_stock', 'items.max_stock', 'items.unit_price', 'items.barcode', 'items.image_path', 'items.status', 'items.created_at', 'items.updated_at', 'items.deleted_at')
            ->havingRaw('MAX(stock_movements.movement_date) < ? OR MAX(stock_movements.movement_date) IS NULL', [$cutoffDate])
            ->get();
    }

    /**
     * Get stock value report.
     */
    public function getStockValueReport(?int $warehouseId = null): Collection
    {
        $query = DB::table('stock_movements')
            ->join('items', 'stock_movements.item_id', '=', 'items.id')
            ->join('item_units', 'items.unit_id', '=', 'item_units.id')
            ->where('stock_movements.status', 'approved');

        if ($warehouseId) {
            $query->where('stock_movements.warehouse_id', $warehouseId);
        }

        return $query->select(
                'items.id',
                'items.sku',
                'items.name',
                'item_units.name as unit_name',
                'items.unit_price',
                DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END) as current_stock'),
                DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END) * items.unit_price as stock_value')
            )
            ->groupBy('items.id', 'items.sku', 'items.name', 'item_units.name', 'items.unit_price')
            ->having('current_stock', '>', 0)
            ->orderByDesc('stock_value')
            ->get();
    }

    /**
     * Get stock value by warehouse.
     */
    public function getStockValueByWarehouse(): Collection
    {
        return DB::table('stock_movements')
            ->join('items', 'stock_movements.item_id', '=', 'items.id')
            ->join('warehouses', 'stock_movements.warehouse_id', '=', 'warehouses.id')
            ->where('stock_movements.status', 'approved')
            ->select(
                'warehouses.id',
                'warehouses.name',
                DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity * items.unit_price
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity * items.unit_price
                    WHEN movement_type = "adjustment" THEN quantity * items.unit_price
                    ELSE 0
                END) as total_value')
            )
            ->groupBy('warehouses.id', 'warehouses.name')
            ->having('total_value', '>', 0)
            ->orderByDesc('total_value')
            ->get();
    }

    /**
     * Get top items by value.
     */
    public function getTopItemsByValue(int $limit = 10): Collection
    {
        return DB::table('stock_movements')
            ->join('items', 'stock_movements.item_id', '=', 'items.id')
            ->join('item_units', 'items.unit_id', '=', 'item_units.id')
            ->where('stock_movements.status', 'approved')
            ->select(
                'items.id',
                'items.sku',
                'items.name',
                'item_units.name as unit_name',
                'items.unit_price',
                DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END) as current_stock'),
                DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END) * items.unit_price as stock_value')
            )
            ->groupBy('items.id', 'items.sku', 'items.name', 'item_units.name', 'items.unit_price')
            ->having('current_stock', '>', 0)
            ->orderByDesc('stock_value')
            ->limit($limit)
            ->get();
    }

    /**
     * Get movement statistics for a date range.
     */
    public function getMovementStatistics(string $dateFrom, string $dateTo): array
    {
        $movements = StockMovement::where('status', 'approved')
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->get();

        return [
            'total_movements' => $movements->count(),
            'stock_in_count' => $movements->where('movement_type', 'stock_in')->count(),
            'stock_out_count' => $movements->where('movement_type', 'stock_out')->count(),
            'transfer_count' => $movements->where('movement_type', 'transfer')->count(),
            'adjustment_count' => $movements->where('movement_type', 'adjustment')->count(),
            'return_count' => $movements->where('movement_type', 'return')->count(),
            'total_value' => $movements->sum('total_cost'),
        ];
    }
}

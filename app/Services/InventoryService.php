<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Warehouse;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

/**
 * InventoryService
 * 
 * Core business logic for inventory management.
 */
class InventoryService
{
    /**
     * Get current stock level for an item in a specific warehouse.
     */
    public function getCurrentStock(int $itemId, int $warehouseId): float
    {
        $stockIn = StockMovement::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->whereIn('movement_type', ['stock_in', 'transfer_in', 'return'])
            ->where('status', 'approved')
            ->sum('quantity');

        $stockOut = StockMovement::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->whereIn('movement_type', ['stock_out', 'transfer_out'])
            ->where('status', 'approved')
            ->sum('quantity');

        $adjustments = StockMovement::where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('movement_type', 'adjustment')
            ->where('status', 'approved')
            ->sum('quantity');

        return $stockIn - $stockOut + $adjustments;
    }

    /**
     * Get total stock for an item across all warehouses.
     */
    public function getTotalStock(int $itemId): float
    {
        $stockIn = StockMovement::where('item_id', $itemId)
            ->whereIn('movement_type', ['stock_in', 'transfer_in', 'return'])
            ->where('status', 'approved')
            ->sum('quantity');

        $stockOut = StockMovement::where('item_id', $itemId)
            ->whereIn('movement_type', ['stock_out', 'transfer_out'])
            ->where('status', 'approved')
            ->sum('quantity');

        $adjustments = StockMovement::where('item_id', $itemId)
            ->where('movement_type', 'adjustment')
            ->where('status', 'approved')
            ->sum('quantity');

        return $stockIn - $stockOut + $adjustments;
    }

    /**
     * Get stock distribution across warehouses for an item.
     */
    public function getStockDistribution(int $itemId): array
    {
        $warehouses = Warehouse::active()->get();
        $distribution = [];

        foreach ($warehouses as $warehouse) {
            $stock = $this->getCurrentStock($itemId, $warehouse->id);
            if ($stock > 0) {
                $distribution[] = [
                    'warehouse_id' => $warehouse->id,
                    'warehouse_name' => $warehouse->name,
                    'stock' => $stock,
                ];
            }
        }

        return $distribution;
    }

    /**
     * Check if there's sufficient stock for a withdrawal.
     */
    public function hasSufficientStock(int $itemId, int $warehouseId, float $quantity): bool
    {
        $currentStock = $this->getCurrentStock($itemId, $warehouseId);
        return $currentStock >= $quantity;
    }

    /**
     * Get items that are below minimum stock level.
     */
    public function getItemsBelowMinStock()
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
     * Get items that haven't had any movement in the specified number of days.
     */
    public function getDormantItems(int $days = 90)
    {
        $cutoffDate = now()->subDays($days);

        return Item::with('unit')
            ->whereDoesntHave('stockMovements', function ($query) use ($cutoffDate) {
                $query->where('movement_date', '>=', $cutoffDate);
            })
            ->orWhereHas('stockMovements', function ($query) use ($cutoffDate) {
                $query->select('item_id')
                    ->groupBy('item_id')
                    ->havingRaw('MAX(movement_date) < ?', [$cutoffDate]);
            })
            ->get();
    }

    /**
     * Calculate total inventory value.
     */
    public function calculateInventoryValue(?int $warehouseId = null): float
    {
        $query = DB::table('stock_movements')
            ->join('items', 'stock_movements.item_id', '=', 'items.id')
            ->where('stock_movements.status', 'approved');

        if ($warehouseId) {
            $query->where('stock_movements.warehouse_id', $warehouseId);
        }

        $stockData = $query->select(
                'items.id',
                'items.unit_price',
                DB::raw('SUM(CASE 
                    WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                    WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                    WHEN movement_type = "adjustment" THEN quantity
                    ELSE 0
                END) as current_stock')
            )
            ->groupBy('items.id', 'items.unit_price')
            ->get();

        $totalValue = 0;
        foreach ($stockData as $item) {
            if ($item->current_stock > 0) {
                $totalValue += $item->current_stock * $item->unit_price;
            }
        }

        return $totalValue;
    }

    /**
     * Get stock turnover rate for an item (movements per month).
     */
    public function getStockTurnoverRate(int $itemId, int $months = 6): float
    {
        $startDate = now()->subMonths($months);

        $totalMovements = StockMovement::where('item_id', $itemId)
            ->whereIn('movement_type', ['stock_out'])
            ->where('movement_date', '>=', $startDate)
            ->where('status', 'approved')
            ->sum('quantity');

        $averageStock = $this->getTotalStock($itemId);

        if ($averageStock == 0) {
            return 0;
        }

        return $totalMovements / $averageStock / $months;
    }

    /**
     * Suggest optimal reorder quantity based on historical data.
     */
    public function suggestReorderQuantity(int $itemId): float
    {
        $item = Item::findOrFail($itemId);
        
        // Calculate average monthly consumption
        $monthlyConsumption = StockMovement::where('item_id', $itemId)
            ->where('movement_type', 'stock_out')
            ->where('movement_date', '>=', now()->subMonths(6))
            ->where('status', 'approved')
            ->avg('quantity') ?? 0;

        // Suggest reorder quantity: 2 months of average consumption
        $suggestedQuantity = $monthlyConsumption * 2;

        // Ensure it doesn't exceed max stock
        if ($item->max_stock > 0) {
            $currentStock = $this->getTotalStock($itemId);
            $suggestedQuantity = min($suggestedQuantity, $item->max_stock - $currentStock);
        }

        return max(0, $suggestedQuantity);
    }
}

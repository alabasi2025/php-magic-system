<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Item Model
 * 
 * Represents an inventory item/product.
 * 
 * @property int $id
 * @property string $sku Stock Keeping Unit (unique identifier)
 * @property string $name Item name
 * @property string|null $description
 * @property int $unit_id Primary unit of measurement
 * @property float $min_stock Minimum stock level (alert threshold)
 * @property float $max_stock Maximum stock level
 * @property float $unit_price Unit price for accounting
 * @property string|null $barcode
 * @property string|null $image_path
 * @property string $status active|inactive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sku',
        'name',
        'description',
        'unit_id',
        'min_stock',
        'max_stock',
        'unit_price',
        'barcode',
        'image_path',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_stock' => 'decimal:2',
        'max_stock' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the primary unit for this item.
     */
    public function unit()
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }

    /**
     * Get all stock movements for this item.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'item_id');
    }

    /**
     * Get all warehouses that have this item.
     */
    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'stock_movements')
            ->withPivot('quantity', 'movement_type', 'movement_date')
            ->withTimestamps();
    }

    /**
     * Scope to get only active items.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only inactive items.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope to get items below minimum stock level.
     */
    public function scopeBelowMinStock($query)
    {
        return $query->whereRaw('(SELECT COALESCE(SUM(
            CASE 
                WHEN movement_type IN ("stock_in", "transfer_in", "return") THEN quantity
                WHEN movement_type IN ("stock_out", "transfer_out") THEN -quantity
                WHEN movement_type = "adjustment" THEN quantity
                ELSE 0
            END
        ), 0) FROM stock_movements WHERE stock_movements.item_id = items.id) < items.min_stock');
    }

    /**
     * Check if item is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get total stock across all warehouses.
     */
    public function getTotalStock(): float
    {
        $stockIn = $this->stockMovements()
            ->whereIn('movement_type', ['stock_in', 'transfer_in', 'return'])
            ->sum('quantity');

        $stockOut = $this->stockMovements()
            ->whereIn('movement_type', ['stock_out', 'transfer_out'])
            ->sum('quantity');

        $adjustments = $this->stockMovements()
            ->where('movement_type', 'adjustment')
            ->sum('quantity');

        return $stockIn - $stockOut + $adjustments;
    }

    /**
     * Get stock in a specific warehouse.
     */
    public function getStockInWarehouse(int $warehouseId): float
    {
        $stockIn = $this->stockMovements()
            ->where('warehouse_id', $warehouseId)
            ->whereIn('movement_type', ['stock_in', 'transfer_in', 'return'])
            ->sum('quantity');

        $stockOut = $this->stockMovements()
            ->where('warehouse_id', $warehouseId)
            ->whereIn('movement_type', ['stock_out', 'transfer_out'])
            ->sum('quantity');

        $adjustments = $this->stockMovements()
            ->where('warehouse_id', $warehouseId)
            ->where('movement_type', 'adjustment')
            ->sum('quantity');

        return $stockIn - $stockOut + $adjustments;
    }

    /**
     * Check if item is below minimum stock level.
     */
    public function isBelowMinStock(): bool
    {
        return $this->getTotalStock() < $this->min_stock;
    }

    /**
     * Get image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}

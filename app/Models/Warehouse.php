<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Warehouse Model
 * 
 * Represents a warehouse/storage location in the inventory system.
 * 
 * @property int $id
 * @property string $code Unique warehouse code
 * @property string $name Warehouse name
 * @property string|null $location Physical location/address
 * @property int|null $manager_id User ID of warehouse manager
 * @property string $status active|inactive
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Warehouse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'location',
        'manager_id',
        'status',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the manager of this warehouse.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get all stock movements for this warehouse.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'warehouse_id');
    }

    /**
     * Get all items currently in this warehouse.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'stock_movements')
            ->withPivot('quantity', 'movement_type', 'movement_date')
            ->withTimestamps();
    }

    /**
     * Scope to get only active warehouses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only inactive warehouses.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Check if warehouse is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get current stock level for a specific item in this warehouse.
     */
    public function getCurrentStock(int $itemId): float
    {
        $stockIn = $this->stockMovements()
            ->where('item_id', $itemId)
            ->whereIn('movement_type', ['stock_in', 'transfer_in', 'return'])
            ->sum('quantity');

        $stockOut = $this->stockMovements()
            ->where('item_id', $itemId)
            ->whereIn('movement_type', ['stock_out', 'transfer_out'])
            ->sum('quantity');

        $adjustments = $this->stockMovements()
            ->where('item_id', $itemId)
            ->where('movement_type', 'adjustment')
            ->sum('quantity');

        return $stockIn - $stockOut + $adjustments;
    }
}

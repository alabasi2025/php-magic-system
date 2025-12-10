<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * StockMovementItem Model
 * 
 * تفاصيل الأصناف في الحركة المخزنية
 * 
 * @property int $id
 * @property int $stock_movement_id
 * @property int $item_id
 * @property float $quantity
 * @property string|null $unit
 * @property float $unit_cost
 * @property float $total_cost
 * @property string|null $batch_number
 * @property string|null $expiry_date
 * @property string|null $notes
 */
class StockMovementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_movement_id',
        'item_id',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost',
        'batch_number',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    /**
     * Boot method to auto-calculate total_cost.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_cost = $item->quantity * $item->unit_cost;
        });
    }

    /**
     * Get the stock movement.
     */
    public function stockMovement()
    {
        return $this->belongsTo(StockMovement::class);
    }

    /**
     * Get the item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

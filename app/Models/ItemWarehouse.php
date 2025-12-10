<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemWarehouse extends Model
{
    use HasFactory;

    protected $table = 'item_warehouse';

    protected $fillable = [
        'item_id',
        'warehouse_id',
        'quantity',
        'reserved_quantity',
        'available_quantity',
        'average_cost',
        'last_purchase_price',
        'last_purchase_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'reserved_quantity' => 'decimal:3',
        'available_quantity' => 'decimal:3',
        'average_cost' => 'decimal:4',
        'last_purchase_price' => 'decimal:4',
        'last_purchase_date' => 'date',
    ];

    /**
     * Get the item that owns the inventory.
     * الصنف الذي يملك هذا المخزون
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the warehouse that owns the inventory.
     * المخزن الذي يملك هذا المخزون
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Add quantity to inventory.
     * إضافة كمية إلى المخزون
     *
     * @param float $quantity الكمية المضافة
     * @param float $cost تكلفة الوحدة
     * @return void
     */
    public function addQuantity(float $quantity, float $cost): void
    {
        // حساب متوسط التكلفة الجديد
        $totalCost = ($this->quantity * $this->average_cost) + ($quantity * $cost);
        $totalQuantity = $this->quantity + $quantity;
        
        $this->quantity = $totalQuantity;
        $this->average_cost = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;
        $this->available_quantity = $this->quantity - $this->reserved_quantity;
        $this->last_purchase_price = $cost;
        $this->last_purchase_date = now();
        
        $this->save();
    }

    /**
     * Subtract quantity from inventory.
     * خصم كمية من المخزون
     *
     * @param float $quantity الكمية المخصومة
     * @return void
     */
    public function subtractQuantity(float $quantity): void
    {
        $this->quantity -= $quantity;
        $this->available_quantity = $this->quantity - $this->reserved_quantity;
        
        $this->save();
    }

    /**
     * Reserve quantity.
     * حجز كمية
     *
     * @param float $quantity الكمية المحجوزة
     * @return void
     */
    public function reserveQuantity(float $quantity): void
    {
        $this->reserved_quantity += $quantity;
        $this->available_quantity = $this->quantity - $this->reserved_quantity;
        
        $this->save();
    }

    /**
     * Release reserved quantity.
     * إلغاء حجز كمية
     *
     * @param float $quantity الكمية المحررة
     * @return void
     */
    public function releaseQuantity(float $quantity): void
    {
        $this->reserved_quantity -= $quantity;
        $this->available_quantity = $this->quantity - $this->reserved_quantity;
        
        $this->save();
    }

    /**
     * Check if there is enough available quantity.
     * التحقق من توفر كمية كافية
     *
     * @param float $quantity الكمية المطلوبة
     * @return bool
     */
    public function hasAvailableQuantity(float $quantity): bool
    {
        return $this->available_quantity >= $quantity;
    }
}

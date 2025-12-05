<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $warehouse_id
 * @property int $item_id
 * @property string $movement_type
 * @property string $reference_type
 * @property int $reference_id
 * @property float $quantity
 * @property float|null $unit_price
 * @property float $balance_before
 * @property float $balance_after
 * @property \Illuminate\Support\Carbon $date
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read \App\Models\Warehouse $warehouse
 * @property-read \App\Models\Item $item
 * @property-read \App\Models\User|null $creator
 */
class StockMovement extends Model
{
    use HasFactory;

    // اسم الجدول
    protected $table = 'stock_movements';

    // الحقول التي يمكن تعبئتها جماعياً
    protected $fillable = [
        'warehouse_id',
        'item_id',
        'movement_type',
        'reference_type',
        'reference_id',
        'quantity',
        'unit_price',
        'balance_before',
        'balance_after',
        'date',
        'created_by',
    ];

    // تحويل الحقول إلى أنواع بيانات محددة
    protected $casts = [
        'date' => 'datetime',
        'quantity' => 'float',
        'unit_price' => 'float',
        'balance_before' => 'float',
        'balance_after' => 'float',
    ];

    /**
     * علاقة الحركة بالمخزن.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * علاقة الحركة بالصنف.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * علاقة الحركة بالمستخدم الذي أنشأها.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * علاقة الحركة بالكيان المرجعي (Polymorphic Relation).
     */
    public function reference()
    {
        return $this->morphTo();
    }
}

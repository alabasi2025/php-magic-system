<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $warehouse_id
 * @property int $item_id
 * @property float $quantity
 * @property float $last_cost
 * @property float $average_cost
 * @property float $total_value
 * @property \Illuminate\Support\Carbon $last_updated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class StockBalance extends Model
{
    use HasFactory;

    // اسم الجدول في قاعدة البيانات
    protected $table = 'stock_balances';

    // الحقول التي يمكن تعبئتها جماعياً
    protected $fillable = [
        'warehouse_id',
        'item_id',
        'quantity',
        'last_cost',
        'average_cost',
        'total_value',
        'last_updated',
    ];

    // تحويل الحقول إلى أنواع بيانات محددة
    protected $casts = [
        'quantity' => 'float',
        'last_cost' => 'float',
        'average_cost' => 'float',
        'total_value' => 'float',
        'last_updated' => 'datetime',
    ];

    /**
     * العلاقة مع نموذج المخزن (Warehouse).
     *
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        // نفترض وجود نموذج Warehouse
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * العلاقة مع نموذج الصنف (Item).
     *
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        // نفترض وجود نموذج Item
        return $this->belongsTo(Item::class);
    }
}

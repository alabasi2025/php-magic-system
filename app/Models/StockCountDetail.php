<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $stock_count_id الجرد الذي ينتمي إليه التفصيل
 * @property int $item_id الصنف الذي تم جرده
 * @property float $system_quantity الكمية المسجلة في النظام
 * @property float $actual_quantity الكمية الفعلية التي تم عدها
 * @property float $difference الفرق بين الكمية الفعلية وكمية النظام
 * @property string|null $notes ملاحظات على الصنف
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\StockCount $stockCount
 * @property-read \App\Models\Item $item
 */
class StockCountDetail extends Model
{
    use HasFactory;

    // تحديد الحقول التي يمكن تعبئتها جماعياً
    protected $fillable = [
        'stock_count_id',
        'item_id',
        'system_quantity',
        'actual_quantity',
        'difference',
        'notes',
    ];

    // تحويل الحقول
    protected $casts = [
        'system_quantity' => 'float',
        'actual_quantity' => 'float',
        'difference' => 'float',
    ];

    /**
     * علاقة: التفصيل ينتمي إلى عملية جرد واحدة.
     */
    public function stockCount(): BelongsTo
    {
        return $this->belongsTo(StockCount::class);
    }

    /**
     * علاقة: التفصيل يخص صنفاً واحداً.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $stock_out_id معرف إذن الإخراج
 * @property int $item_id معرف الصنف/المنتج
 * @property float $quantity الكمية المخرجة
 * @property float $unit_price سعر الوحدة عند الإخراج
 * @property float $total_price إجمالي سعر البند
 */
class StockOutDetail extends Model
{
    use HasFactory;

    // تحديد الحقول التي يمكن تعبئتها جماعياً
    protected $fillable = [
        'stock_out_id',
        'item_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    // تحويل الحقول الرقمية إلى أرقام عشرية
    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'total_price' => 'float',
    ];

    /**
     * علاقة: إذن الإخراج الذي ينتمي إليه هذا التفصيل.
     * @return BelongsTo
     */
    public function stockOut(): BelongsTo
    {
        // تفصيل الإخراج ينتمي إلى إذن إخراج واحد
        return $this->belongsTo(StockOut::class);
    }

    /**
     * علاقة: الصنف/المنتج الذي تم إخراجه.
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        // تفصيل الإخراج مرتبط بصنف واحد
        return $this->belongsTo(Item::class);
    }
}

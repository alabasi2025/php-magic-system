<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockInDetail extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بتعبئتها جماعياً.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stock_in_id',
        'item_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    /**
     * تحويل بعض الحقول إلى أنواع بيانات محددة.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * علاقة: تفصيل الإدخال ينتمي إلى إذن إدخال واحد.
     */
    public function stockIn(): BelongsTo
    {
        return $this->belongsTo(StockIn::class);
    }

    /**
     * علاقة: تفصيل الإدخال يتعلق بصنف واحد.
     */
    public function item(): BelongsTo
    {
        // نفترض وجود نموذج Item
        return $this->belongsTo(Item::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $type
 * @property string $message
 * @property int|null $product_id
 * @property bool $is_resolved
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Product|null $product
 */
class Alert extends Model
{
    use HasFactory;

    /**
     * الحقول التي يمكن تعبئتها بشكل جماعي.
     * @var array<int, string>
     */
    protected $fillable = [
        'type', // 'low_stock', 'expired', 'system'
        'message',
        'product_id',
        'is_resolved',
    ];

    /**
     * تحويل الحقول إلى أنواع بيانات محددة.
     * @var array<string, string>
     */
    protected $casts = [
        'is_resolved' => 'boolean',
    ];

    /**
     * علاقة المنتج (Product) المرتبط بالتنبيه (إذا كان التنبيه خاصاً بمنتج).
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

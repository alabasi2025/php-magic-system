<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $sku
 * @property string $name
 * @property string|null $description
 * @property int $current_stock
 * @property int $min_stock_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StockMovement[] $stockMovements
 */
class Product extends Model
{
    use HasFactory;

    /**
     * الحقول التي يمكن تعبئتها بشكل جماعي.
     * @var array<int, string>
     */
    protected $fillable = [
        'sku',
        'name',
        'description',
        'current_stock',
        'min_stock_level',
    ];

    /**
     * تحويل الحقول إلى أنواع بيانات محددة.
     * @var array<string, string>
     */
    protected $casts = [
        'current_stock' => 'integer',
        'min_stock_level' => 'integer',
    ];

    /**
     * علاقة حركات المخزون (Stock Movements) المرتبطة بهذا المنتج.
     * @return HasMany
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}

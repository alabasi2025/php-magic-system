<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $location
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StockMovement[] $stockMovements
 */
class Warehouse extends Model
{
    use HasFactory;

    /**
     * الحقول التي يمكن تعبئتها بشكل جماعي.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location',
        'is_active',
    ];

    /**
     * تحويل الحقول إلى أنواع بيانات محددة.
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * علاقة حركات المخزون (Stock Movements) المرتبطة بهذا المخزن.
     * @return HasMany
     */
    public function stockMovements(): HasMany
    {
        // يمكن أن يكون المخزن هو المصدر أو الوجهة للحركة
        return $this->hasMany(StockMovement::class, 'from_warehouse_id');
    }
}

<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $symbol
 * @property bool $is_base_unit
 * @property int|null $base_unit_id
 * @property float $conversion_factor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Unit|null $baseUnit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Unit> $derivedUnits
 * @property-read int|null $derived_units_count
 */
class Unit extends Model
{
    use HasFactory;

    // اسم الجدول في قاعدة البيانات
    protected $table = 'units';

    // الحقول المسموح بتعبئتها جماعياً
    protected $fillable = [
        'name',
        'symbol',
        'is_base_unit',
        'base_unit_id',
        'conversion_factor',
    ];

    // تحويل أنواع الحقول
    protected $casts = [
        'is_base_unit' => 'boolean',
        'conversion_factor' => 'float',
        'base_unit_id' => 'integer',
    ];

    /**
     * علاقة الوحدة الأساسية (Base Unit).
     *
     * @return BelongsTo
     */
    public function baseUnit(): BelongsTo
    {
        // الوحدة تنتمي إلى وحدة أساسية (إذا لم تكن هي الأساس)
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    /**
     * علاقة الوحدات المشتقة (Derived Units).
     *
     * @return HasMany
     */
    public function derivedUnits(): HasMany
    {
        // الوحدة الأساسية لديها العديد من الوحدات المشتقة
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    /**
     * تحويل كمية من هذه الوحدة إلى الوحدة الأساسية.
     *
     * @param float $quantity الكمية المراد تحويلها
     * @return float الكمية بالوحدة الأساسية
     */
    public function convertToBase(float $quantity): float
    {
        // إذا كانت هي الوحدة الأساسية، لا يوجد تحويل
        if ($this->is_base_unit || $this->conversion_factor == 0) {
            return $quantity;
        }

        // التحويل يتم بضرب الكمية في معامل التحويل
        return $quantity * $this->conversion_factor;
    }

    /**
     * تحويل كمية من الوحدة الأساسية إلى هذه الوحدة.
     *
     * @param float $quantity الكمية بالوحدة الأساسية
     * @return float الكمية بالوحدة الحالية
     */
    public function convertFromBase(float $quantity): float
    {
        // إذا كانت هي الوحدة الأساسية، لا يوجد تحويل
        if ($this->is_base_unit || $this->conversion_factor == 0) {
            return $quantity;
        }

        // التحويل يتم بقسمة الكمية على معامل التحويل
        return $quantity / $this->conversion_factor;
    }
}

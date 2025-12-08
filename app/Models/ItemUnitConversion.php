<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemUnitConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'unit_id',
        'capacity',
        'is_primary',
        'price',
        'sort_order',
    ];

    protected $casts = [
        'capacity' => 'decimal:4',
        'price' => 'decimal:2',
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * العلاقة مع الصنف
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * العلاقة مع الوحدة
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }

    /**
     * حساب السعر بناءً على سعر الوحدة الرئيسية
     */
    public function calculatePrice(): float
    {
        if ($this->price) {
            return (float) $this->price;
        }

        // إذا لم يكن هناك سعر محدد، احسبه من الوحدة الرئيسية
        $primaryUnit = $this->item->unitConversions()
            ->where('is_primary', true)
            ->first();

        if ($primaryUnit && $primaryUnit->price) {
            return (float) ($primaryUnit->price * $this->capacity);
        }

        return 0;
    }

    /**
     * Scope للحصول على الوحدة الرئيسية فقط
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope للحصول على الوحدات مرتبة
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('capacity');
    }
}

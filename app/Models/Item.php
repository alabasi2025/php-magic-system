<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $sku
 * @property int $category_id
 * @property int $unit_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\Unit $unit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ItemVariant> $variants
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockBalance> $stockBalances
 */
class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'sku',
        'category_id',
        'unit_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'category_id' => 'integer',
        'unit_id' => 'integer',
    ];

    // --- Relationships ---

    /**
     * Get the category that owns the Item.
     * Assumes a Category model exists.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the unit of measure that owns the Item.
     * Assumes a Unit model exists.
     *
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the variants for the Item.
     * Assumes an ItemVariant model exists.
     *
     * @return HasMany
     */
    public function variants(): HasMany
    {
        // Assuming ItemVariant model has an 'item_id' foreign key
        return $this->hasMany(ItemVariant::class);
    }

    /**
     * Get the stock balances for the Item across different locations/warehouses.
     * Assumes a StockBalance model exists.
     *
     * @return HasMany
     */
    public function stockBalances(): HasMany
    {
        // Assuming StockBalance model has an 'item_id' foreign key
        return $this->hasMany(StockBalance::class);
    }

    // --- Custom Methods ---

    /**
     * Scope a query to only include active items.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the total current stock quantity for the item.
     * This is a simple example and might need optimization for large datasets.
     *
     * @return float
     */
    public function getTotalStockAttribute(): float
    {
        // Assuming StockBalance model has a 'quantity' column
        return (float) $this->stockBalances()->sum('quantity');
    }
}
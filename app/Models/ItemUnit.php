<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ItemUnit Model
 * 
 * Represents a unit of measurement for inventory items.
 * Examples: قطعة (piece), كرتون (carton), باليت (pallet), كيلو (kg), لتر (liter)
 * 
 * @property int $id
 * @property string $code Unique unit code
 * @property string $name Unit name (Arabic)
 * @property string|null $name_en Unit name (English)
 * @property string|null $symbol Unit symbol (e.g., kg, L, pcs)
 * @property string|null $description
 * @property string $status active|inactive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class ItemUnit extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'item_units';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'symbol',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get all items using this unit.
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'unit_id');
    }

    /**
     * Scope to get only active units.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only inactive units.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Check if unit is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get display name (with symbol if available).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->symbol ? "{$this->name} ({$this->symbol})" : $this->name;
    }
}

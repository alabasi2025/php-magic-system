<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'account_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the accounting account linked to this warehouse group.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'account_id');
    }

    /**
     * Get all warehouses in this group.
     */
    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class, 'warehouse_group_id');
    }

    /**
     * Scope a query to only include active groups.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get the warehouses count for this group.
     */
    public function getWarehousesCountAttribute(): int
    {
        return $this->warehouses()->count();
    }
}

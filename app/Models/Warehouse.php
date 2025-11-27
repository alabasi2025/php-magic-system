<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Warehouse",
 *     title="Warehouse",
 *     description="Warehouse model for managing physical storage locations.",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="Warehouse ID"),
 *     @OA\Property(property="name", type="string", description="Name of the warehouse"),
 *     @OA\Property(property="address", type="string", description="Physical address of the warehouse"),
 *     @OA\Property(property="manager_id", type="integer", description="ID of the user who manages the warehouse"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="Last update timestamp")
 * )
 */
class Warehouse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'manager_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // No specific casts needed for basic fields, but kept for best practice
    ];

    // --- Relationships ---

    /**
     * Get the locations associated with the warehouse.
     * A warehouse can have many specific storage locations (e.g., aisles, racks).
     *
     * @return HasMany
     */
    public function locations(): HasMany
    {
        // Assuming a Location model exists and has a 'warehouse_id' foreign key
        return $this->hasMany(Location::class);
    }

    /**
     * Get the zones associated with the warehouse.
     * A warehouse can be divided into multiple zones (e.g., receiving, picking, cold storage).
     *
     * @return HasMany
     */
    public function zones(): HasMany
    {
        // Assuming a Zone model exists and has a 'warehouse_id' foreign key
        return $this->hasMany(Zone::class);
    }

    /**
     * Get the user who manages the warehouse.
     * A warehouse is managed by one user.
     *
     * @return BelongsTo
     */
    public function manager(): BelongsTo
    {
        // Assuming a User model exists and the foreign key is 'manager_id'
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the stock balances held in this warehouse.
     * This represents the current inventory levels for various products within the warehouse.
     *
     * @return HasMany
     */
    public function stockBalances(): HasMany
    {
        // Assuming a StockBalance model exists and has a 'warehouse_id' foreign key
        return $this->hasMany(StockBalance::class);
    }

    // --- Custom Methods ---

    /**
     * Check if the warehouse is currently active.
     * This is a placeholder for potential future logic (e.g., checking a status column).
     *
     * @return bool
     */
    public function isActive(): bool
    {
        // For now, assume all created warehouses are active.
        // Future implementation might check $this->status === 'active'
        return true;
    }
}
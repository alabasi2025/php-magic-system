<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="CostCenter",
 *     title="Cost Center",
 *     description="Represents a cost center in the organization, supporting a hierarchical (tree) structure.",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="The unique identifier for the cost center."),
 *     @OA\Property(property="parent_id", type="integer", nullable="true", description="The ID of the parent cost center, null for root centers."),
 *     @OA\Property(property="name", type="string", description="The name of the cost center."),
 *     @OA\Property(property="code", type="string", description="The unique code for the cost center."),
 *     @OA\Property(property="description", type="string", nullable="true", description="A brief description of the cost center."),
 *     @OA\Property(property="is_active", type="boolean", description="Status of the cost center (active/inactive)."),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="Timestamp of creation."),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="Timestamp of last update.")
 * )
 */
class CostCenter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // --- Relationships for Tree Structure ---

    /**
     * Get the parent cost center.
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        // A cost center belongs to a parent cost center (self-referencing relationship)
        return $this->belongsTo(CostCenter::class, 'parent_id');
    }

    /**
     * Get the child cost centers.
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        // A cost center can have many child cost centers (self-referencing relationship)
        return $this->hasMany(CostCenter::class, 'parent_id');
    }

    // --- Other Relationships ---

    /**
     * Get the allocations associated with the cost center.
     *
     * Assumes an 'Allocation' model exists.
     *
     * @return HasMany
     */
    public function allocations(): HasMany
    {
        // A cost center can have many allocations
        return $this->hasMany(Allocation::class, 'cost_center_id');
    }

    // --- Custom Methods for Tree Traversal ---

    /**
     * Check if the cost center is a root node (has no parent).
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Get all ancestors (parents, grandparents, etc.) of the cost center.
     *
     * NOTE: This method is a basic example and might be inefficient for deep trees.
     * For production, consider using a package like 'kalnoy/nestedset' or a recursive query.
     *
     * @return \Illuminate\Support\Collection
     */
    public function ancestors(): \Illuminate\Support\Collection
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get all descendants (children, grandchildren, etc.) of the cost center.
     *
     * NOTE: This method is a basic example and might be inefficient for deep trees.
     * For production, consider using a package like 'kalnoy/nestedset' or a recursive query.
     *
     * @return \Illuminate\Support\Collection
     */
    public function descendants(): \Illuminate\Support\Collection
    {
        $descendants = collect();
        $this->load('children');

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }
}
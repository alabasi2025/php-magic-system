<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     title="Unit Model",
 *     description="Unit model for organizational structure",
 *     @OA\Xml(name="Unit")
 * )
 */
class Unit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     * @OA\Property(property="organization_id", type="integer", description="The ID of the organization this unit belongs to")
     * @OA\Property(property="name", type="string", description="The name of the unit")
     * @OA\Property(property="description", type="string", description="A brief description of the unit")
     * @OA\Property(property="is_active", type="boolean", description="Status of the unit (active/inactive)")
     */
    protected $fillable = [
        'organization_id',
        'name',
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

    // --- Relationships ---

    /**
     * Get the organization that owns the Unit.
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        // Assuming the foreign key is 'organization_id' on the units table
        // and the related model is 'App\Models\Organization'.
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the departments for the Unit.
     *
     * @return HasMany
     */
    public function departments(): HasMany
    {
        // Assuming the foreign key is 'unit_id' on the departments table
        // and the related model is 'App\Models\Department'.
        return $this->hasMany(Department::class);
    }

    // --- Custom Methods ---

    /**
     * Scope a query to only include active units.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get a short summary of the unit's details.
     *
     * @return string
     */
    public function getSummary(): string
    {
        return "Unit: {$this->name} (ID: {$this->id}) in Organization ID: {$this->organization_id}";
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $parent_organization_id The ID of the parent organization (Holding).
 * @property string $name The name of the organization.
 * @property string $code A unique code for the organization.
 * @property string $status The current status of the organization (e.g., active, inactive).
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Organization|null $holding The parent organization (Holding).
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Unit> $units The units belonging to this organization.
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users The users associated with this organization.
 */
class Organization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_organization_id',
        'name',
        'code',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parent_organization_id' => 'integer',
    ];

    // --- Relationships ---

    /**
     * Get the parent organization that acts as the holding company.
     *
     * This is a self-referencing relationship, where an Organization belongs to a parent Organization.
     * The foreign key is 'parent_organization_id'.
     *
     * @return BelongsTo
     */
    public function holding(): BelongsTo
    {
        // Assuming 'holding' refers to the parent organization in a hierarchical structure.
        return $this->belongsTo(Organization::class, 'parent_organization_id');
    }

    /**
     * Get the units associated with the organization.
     *
     * An Organization can have many Units.
     *
     * @return HasMany
     */
    public function units(): HasMany
    {
        // Assuming the Unit model has a foreign key 'organization_id'.
        // Note: The Unit model is assumed to exist and have an 'organization_id' foreign key.
        return $this->hasMany(Unit::class);
    }

    /**
     * Get the users associated with the organization.
     *
     * An Organization can have many Users.
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        // Assuming the User model has a foreign key 'organization_id'.
        // Note: The User model is assumed to exist and have an 'organization_id' foreign key.
        return $this->hasMany(User::class);
    }

    // --- Custom Methods ---

    /**
     * Check if the organization is a top-level holding company (i.e., has no parent).
     *
     * @return bool
     */
    public function isHoldingCompany(): bool
    {
        return is_null($this->parent_organization_id);
    }

    /**
     * Get the full hierarchy path of the organization.
     *
     * This method recursively traverses the parent organizations to build the path.
     *
     * @return string
     */
    public function getHierarchyPath(): string
    {
        $path = $this->name;
        $parent = $this->holding;

        while ($parent) {
            $path = $parent->name . ' -> ' . $path;
            $parent = $parent->holding;
        }

        return $path;
    }
}
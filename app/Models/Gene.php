<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @OA\Schema(
 *     schema="Gene",
 *     title="Gene",
 *     description="Gene model representing a biological or conceptual gene entity.",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="Gene ID"),
 *     @OA\Property(property="name", type="string", description="The full name of the gene"),
 *     @OA\Property(property="symbol", type="string", description="The short symbol or abbreviation for the gene"),
 *     @OA\Property(property="description", type="string", nullable="true", description="A detailed description of the gene"),
 *     @OA\Property(property="is_active", type="boolean", description="Status of the gene"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="Last update timestamp")
 * )
 */
class Gene extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'symbol',
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
     * The organizations that belong to the Gene.
     *
     * This establishes a many-to-many relationship between Gene and Organization
     * using the 'organization_genes' pivot table.
     *
     * @return BelongsToMany
     */
    public function organizations(): BelongsToMany
    {
        // The belongsToMany method takes the related model, the pivot table name,
        // the foreign key of the model on which you are defining the relationship,
        // and the foreign key of the model you are joining to.
        // NOTE: Assuming an Organization model exists in App\Models\Organization
        return $this->belongsToMany(
            Organization::class,
            'organization_genes',
            'gene_id', // Foreign key on the organization_genes table for the Gene model
            'organization_id' // Foreign key on the organization_genes table for the Organization model
        )
        // Include timestamps on the pivot table for tracking when the association was created/updated
        ->withTimestamps()
        // If the pivot table had extra columns, they would be included here, e.g.,
        // ->withPivot(['some_attribute']);
        ;
    }

    // --- Custom Methods ---

    /**
     * Scope a query to only include active genes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
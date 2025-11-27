<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="OrganizationGene",
 *     title="OrganizationGene",
 *     description="Model representing the relationship between an Organization and a Gene (Feature/Module).",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="The unique identifier for the OrganizationGene record."),
 *     @OA\Property(property="organization_id", type="integer", description="The ID of the Organization."),
 *     @OA\Property(property="gene_id", type="integer", description="The ID of the Gene (Feature/Module)."),
 *     @OA\Property(property="is_active", type="boolean", description="Status of the gene for the organization (active/inactive)."),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="Timestamp of creation."),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="Timestamp of last update.")
 * )
 */
class OrganizationGene extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organization_genes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'gene_id',
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

    /**
     * Get the organization that owns the OrganizationGene.
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        // Assuming 'Organization' model exists and organization_id is the foreign key
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Get the gene (feature/module) that the OrganizationGene belongs to.
     *
     * @return BelongsTo
     */
    public function gene(): BelongsTo
    {
        // Assuming 'Gene' model exists and gene_id is the foreign key
        return $this->belongsTo(Gene::class, 'gene_id');
    }

    // Custom methods can be added here if needed, e.g., scopes or business logic.
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @OA\Schema(
 *     schema="Holding",
 *     title="Holding",
 *     description="Holding model representing a top-level corporate entity that owns multiple organizations.",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="Holding ID"),
 *     @OA\Property(property="name", type="string", description="Name of the holding company"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="Last update timestamp")
 * )
 */
class Holding extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'holdings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // No specific casts needed for this simple model, but included for best practice.
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the organizations for the holding.
     *
     * A Holding has many Organizations. This is a one-to-many relationship.
     * The foreign key 'holding_id' is expected on the 'organizations' table.
     *
     * @return HasMany
     */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'holding_id');
    }

    /**
     * Get all of the users for the holding through the organizations.
     *
     * This is a HasManyThrough relationship.
     * 1. The 'Organization' model is the intermediate model.
     * 2. The 'User' model is the final model we wish to access.
     * 3. The foreign key on the intermediate table ('organizations') is 'holding_id'.
     * 4. The foreign key on the final table ('users') is 'organization_id'.
     *
     * @return HasManyThrough
     */
    public function users(): HasManyThrough
    {
        // Arguments:
        // 1. The final model name (User::class)
        // 2. The intermediate model name (Organization::class)
        // 3. Foreign key on the intermediate model (Organization) table (linking to Holding)
        // 4. Foreign key on the final model (User) table (linking to Organization)
        // 5. Local key on the current model (Holding)
        // 6. Local key on the intermediate model (Organization)
        return $this->hasManyThrough(
            User::class,
            Organization::class,
            'holding_id',      // Foreign key on the organizations table...
            'organization_id', // Foreign key on the users table...
            'id',              // Local key on the holdings table...
            'id'               // Local key on the organizations table...
        );
    }
}

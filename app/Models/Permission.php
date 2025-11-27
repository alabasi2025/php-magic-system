<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @OA\Schema(
 *     title="Permission Model",
 *     description="Permission model for managing user permissions in the SEMOP Magic System.",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="Permission ID"),
 *     @OA\Property(property="name", type="string", description="Human-readable name of the permission (e.g., 'View Users')"),
 *     @OA\Property(property="slug", type="string", description="Unique system slug for the permission (e.g., 'users.view')"),
 *     @OA\Property(property="description", type="string", nullable="true", description="Detailed description of what the permission allows"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="Last update timestamp")
 * )
 */
class Permission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // No specific casts needed for this model by default, but can be added later.
    ];

    /**
     * Get the roles that are assigned this permission.
     *
     * This defines a many-to-many relationship between Permissions and Roles.
     * The intermediate table is typically 'role_permission' or 'permission_role'.
     *
     * @return BelongsToMany<Role>
     */
    public function roles(): BelongsToMany
    {
        // Assuming a pivot table named 'role_permission' with foreign keys 'permission_id' and 'role_id'.
        return $this->belongsToMany(Role::class, 'role_permission', 'permission_id', 'role_id');
    }

    /**
     * Check if the permission is assigned to a specific role.
     *
     * @param Role|int $role The Role model instance or ID to check against.
     * @return bool
     */
    public function hasRole(Role|int $role): bool
    {
        if ($role instanceof Role) {
            return $this->roles()->where('role_id', $role->id)->exists();
        }

        return $this->roles()->where('role_id', $role)->exists();
    }

    /**
     * Find a permission by its unique slug.
     *
     * @param string $slug The slug of the permission.
     * @return Permission|null
     */
    public static function findBySlug(string $slug): ?Permission
    {
        return static::where('slug', $slug)->first();
    }
}

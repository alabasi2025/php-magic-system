<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @OA\Schema(
 *     schema="Role",
 *     title="Role",
 *     description="Role model for role-based access control (RBAC).",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="Role ID"),
 *     @OA\Property(property="name", type="string", description="Display name of the role (e.g., Administrator)"),
 *     @OA\Property(property="slug", type="string", description="Unique slug for the role (e.g., admin)"),
 *     @OA\Property(property="description", type="string", nullable="true", description="A brief description of the role"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="Last update timestamp")
 * )
 */
class Role extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

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
        // No specific casts needed for standard string/text fields, but included for completeness.
    ];

    // --- Relationships ---

    /**
     * The users that belong to the role.
     * This establishes a many-to-many relationship between roles and users.
     * The pivot table is typically 'role_user'.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        // Assuming the User model is in the default location App\Models\User
        // and the pivot table is 'role_user' with 'role_id' and 'user_id' foreign keys.
        // Note: The User model must be imported or fully qualified.
        return $this->belongsToMany(\App\Models\User::class, 'role_user', 'role_id', 'user_id');
    }

    /**
     * The permissions that belong to the role.
     * This establishes a many-to-many relationship between roles and permissions.
     * The pivot table is typically 'role_permission'.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        // Assuming the Permission model is in the default location App\Models\Permission
        // and the pivot table is 'role_permission' with 'role_id' and 'permission_id' foreign keys.
        // Note: The Permission model must be imported or fully qualified.
        return $this->belongsToMany(\App\Models\Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    // --- Custom Methods ---

    /**
     * Check if the role has a specific permission.
     *
     * @param string $permissionSlug The slug of the permission to check.
     * @return bool
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Assign a permission to the role.
     *
     * @param \App\Models\Permission|int $permission
     * @return void
     */
    public function givePermissionTo($permission): void
    {
        $this->permissions()->attach($permission);
    }

    /**
     * Revoke a permission from the role.
     *
     * @param \App\Models\Permission|int $permission
     * @return int
     */
    public function revokePermissionTo($permission): int
    {
        return $this->permissions()->detach($permission);
    }
}
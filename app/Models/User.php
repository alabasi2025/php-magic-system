<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User model for the SEMOP Magic System, including authentication, authorization, and organization management.",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="User ID"),
 *     @OA\Property(property="organization_id", type="integer", description="ID of the organization the user belongs to"),
 *     @OA\Property(property="name", type="string", description="User's full name"),
 *     @OA\Property(property="email", type="string", format="email", description="User's email address (unique)"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable="true", description="Timestamp of email verification"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="Last update timestamp"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", readOnly="true", nullable="true", description="Soft deletion timestamp")
 * )
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- Relationships ---

    /**
     * Get the organization that the user belongs to.
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        // Assuming a User belongs to one Organization
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the roles associated with the user.
     *
     * This relationship is typically handled by the Spatie\Permission\Traits\HasRoles trait,
     * but we include the method signature for clarity and potential custom extensions.
     *
     * @return BelongsToMany
     */
    // public function roles(): BelongsToMany
    // {
    //     // This is handled by the HasRoles trait from Spatie
    //     return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    // }

    /**
     * Get the permissions directly associated with the user.
     *
     * This relationship is typically handled by the Spatie\Permission\Traits\HasRoles trait,
     * but we include the method signature for clarity and potential custom extensions.
     *
     * @return BelongsToMany
     */
    // public function permissions(): BelongsToMany
    // {
    //     // This is handled by the HasRoles trait from Spatie
    //     return $this->belongsToMany(Permission::class, 'model_has_permissions', 'model_id', 'permission_id');
    // }

    /**
     * Get the journal entries created by this user.
     */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'created_by');
    }

    /**
     * Get the audit logs for this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // --- Custom Methods ---

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->hasPermissionTo($permission);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->hasAnyPermission($permissions);
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        return $this->hasAllPermissions($permissions);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is an accountant.
     */
    public function isAccountant(): bool
    {
        return $this->hasRole('accountant');
    }

    /**
     * Check if user is an auditor.
     */
    public function isAuditor(): bool
    {
        return $this->hasRole('auditor');
    }

    /**
     * Check if the user belongs to a specific organization.
     *
     * @param int $organizationId
     * @return bool
     */
    public function belongsToOrganization(int $organizationId): bool
    {
        return $this->organization_id === $organizationId;
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        // Example of an accessor
        return $this->name;
    }
}
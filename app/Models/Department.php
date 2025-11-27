<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $unit_id
 * @property int|null $parent_id
 * @property int|null $manager_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Unit $unit
 * @property-read \App\Models\Department|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Department> $children
 * @property-read \App\Models\User|null $manager
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $employees
 */
class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'unit_id',
        'parent_id',
        'manager_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Assuming unit_id, parent_id, and manager_id are foreign keys and should be integers
        'unit_id' => 'integer',
        'parent_id' => 'integer',
        'manager_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the Unit that the Department belongs to.
     *
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        // Assuming the foreign key is 'unit_id' and the related model is 'App\Models\Unit'
        // Unit model is assumed to exist for this relationship to be valid.
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get the parent Department (for hierarchical structure).
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        // Self-referencing relationship using 'parent_id'
        return $this->belongsTo(Department::class, 'parent_id');
    }

    /**
     * Get the child Departments (for hierarchical structure).
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        // Self-referencing relationship using 'parent_id' as the foreign key in the child model
        return $this->hasMany(Department::class, 'parent_id');
    }

    /**
     * Get the User who is the Manager of this Department.
     *
     * @return BelongsTo
     */
    public function manager(): BelongsTo
    {
        // Assuming the foreign key is 'manager_id' and the related model is 'App\Models\User'
        // User model is assumed to exist for this relationship to be valid.
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get all the Users (employees) that belong to this Department.
     *
     * Note: This assumes the 'users' table has a 'department_id' foreign key.
     *
     * @return HasMany
     */
    public function employees(): HasMany
    {
        // Assuming the foreign key on the User model is 'department_id'
        // User model is assumed to exist for this relationship to be valid.
        return $this->hasMany(User::class, 'department_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the department has a parent department.
     *
     * @return bool
     */
    public function hasParent(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if the department has sub-departments.
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $department_id
 * @property string $employee_code
 * @property string $job_title
 * @property \Illuminate\Support\Carbon $hire_date
 * @property string|null $phone_number
 * @property string|null $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Department $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 */
class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'department_id',
        'employee_code',
        'job_title',
        'hire_date',
        'phone_number',
        'address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hire_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the user (personal account) associated with the employee.
     * This is a one-to-one relationship where the employee record belongs to a user.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        // Assuming 'user_id' is the foreign key on the 'employees' table
        // and the User model exists in App\Models\User
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department the employee belongs to.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        // Assuming 'department_id' is the foreign key on the 'employees' table
        // and the Department model exists in App\Models\Department
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the documents associated with the employee (e.g., contracts, performance reviews).
     *
     * @return HasMany
     */
    public function documents(): HasMany
    {
        // Assuming 'employee_id' is the foreign key on the 'documents' table
        // and the Document model exists in App\Models\Document
        return $this->hasMany(Document::class);
    }

    // =========================================================================
    // CUSTOM METHODS
    // =========================================================================

    /**
     * Get the full name of the employee from the associated User model.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        // Safely access the user's name, returning 'N/A' if the user relationship is not loaded or null.
        return $this->user->name ?? 'N/A';
    }
}

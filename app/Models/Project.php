<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @class Project
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $members
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Milestone> $milestones
 */
class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * Assuming a basic project structure with a name, description, status, and dates.
     * The migration for the 'projects' table should include these columns.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * Casting dates to Carbon instances for easier manipulation.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // --- Relationships ---

    /**
     * Get the users (members) that belong to the project.
     *
     * This establishes a many-to-many relationship between Project and User.
     * The pivot table is assumed to be 'project_user' (Laravel's convention).
     *
     * @return BelongsToMany
     */
    public function members(): BelongsToMany
    {
        // The second argument is the name of the intermediate table.
        // The third argument is the foreign key name of the model on which you are defining the relationship.
        // The fourth argument is the foreign key name of the model you are joining to.
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
                    ->withTimestamps(); // Include created_at and updated_at on the pivot table
    }

    /**
     * Get the tasks for the project.
     *
     * This establishes a one-to-many relationship.
     * The 'tasks' table is assumed to have a 'project_id' foreign key.
     *
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the milestones for the project.
     *
     * This establishes a one-to-many relationship.
     * The 'milestones' table is assumed to have a 'project_id' foreign key.
     *
     * @return HasMany
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    // --- Custom Methods (Example) ---

    /**
     * Check if the project is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     title="Task Model",
 *     description="Task model for managing project tasks",
 *     @OA\Xml(name="Task")
 * )
 */
class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'assignee_id',
        'title',
        'description',
        'status', // e.g., 'open', 'in_progress', 'completed', 'closed'
        'priority', // e.g., 'low', 'medium', 'high', 'urgent'
        'due_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'open',
        'priority' => 'medium',
    ];

    // --- Relationships ---

    /**
     * Get the project that owns the task.
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        // Assuming a Project model exists
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user assigned to the task.
     *
     * @return BelongsTo
     */
    public function assignee(): BelongsTo
    {
        // The assignee_id column links to the User model
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Get the comments for the task.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        // Assuming a Comment model exists
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the attachments for the task.
     *
     * @return HasMany
     */
    public function attachments(): HasMany
    {
        // Assuming an Attachment model exists
        return $this->hasMany(Attachment::class);
    }

    // --- Custom Methods ---

    /**
     * Check if the task is overdue.
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Scope a query to only include tasks with a specific status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include tasks assigned to a specific user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assignee_id', $userId);
    }
}
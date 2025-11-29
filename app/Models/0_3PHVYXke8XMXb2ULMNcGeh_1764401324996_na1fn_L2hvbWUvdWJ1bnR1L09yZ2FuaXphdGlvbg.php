<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'established_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'established_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the users for the organization.
     */
    public function users(): HasMany
    {
        // Assuming a User model exists and has a foreign key 'organization_id'
        return $this->hasMany(User::class);
    }

    // يمكن إضافة علاقات أخرى مثل:
    // public function projects(): HasMany
    // {
    //     return $this->hasMany(Project::class);
    // }
}

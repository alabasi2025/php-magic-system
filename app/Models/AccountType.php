<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="AccountType",
 *     title="AccountType",
 *     description="Model representing a type of account (e.g., Box, Bank, Supplier, Customer) used for analytical accounting.",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="The unique identifier for the account type."),
 *     @OA\Property(property="name", type="string", description="The name of the account type (e.g., 'Box', 'Bank')."),
 *     @OA\Property(property="description", type="string", nullable="true", description="A brief description of the account type."),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of creation."),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update.")
 * )
 */
class AccountType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_system_defined', // To indicate if the type is a core system type
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_system_defined' => 'boolean',
    ];

    // --- Relationships ---

    /**
     * Get the accounts that are of this type.
     *
     * This establishes a one-to-many relationship where one AccountType
     * can be associated with many Accounts (e.g., all 'Bank' accounts).
     *
     * @return HasMany
     */
    public function accounts(): HasMany
    {
        // Assuming the related model is named 'Account' and it has a foreign key 'account_type_id'
        return $this->hasMany(Account::class, 'account_type_id');
    }

    // --- Custom Methods ---

    /**
     * Check if the account type is a system-defined type.
     *
     * @return bool
     */
    public function isSystemDefined(): bool
    {
        return (bool) $this->is_system_defined;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Supplier",
 *     title="Supplier",
 *     description="Supplier model for managing vendor information.",
 *     @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *     @OA\Property(property="name", type="string", example="Acme Corp"),
 *     @OA\Property(property="email", type="string", format="email", example="contact@acmecorp.com"),
 *     @OA\Property(property="phone", type="string", example="+1-555-123-4567"),
 *     @OA\Property(property="address", type="string", example="123 Main St, Anytown, USA"),
 *     @OA\Property(property="is_active", type="boolean", example="true"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true")
 * )
 */
class Supplier extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Uses snake_case convention for database tables.
     *
     * @var string
     */
    protected $table = 'suppliers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // =========================================================================
    // RELATIONSHIPS (camelCase for methods)
    // =========================================================================

    /**
     * Get all contacts associated with the supplier.
     * Relationship: One-to-Many (Supplier has many Contacts).
     *
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        // Assuming a 'contacts' table with a 'supplier_id' foreign key
        return $this->hasMany(Contact::class);
    }

    /**
     * Get all transactions (e.g., purchases) made with the supplier.
     * Relationship: One-to-Many (Supplier has many Transactions).
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        // Assuming a 'transactions' table with a 'supplier_id' foreign key
        return $this->hasMany(Transaction::class);
    }

    // =========================================================================
    // CUSTOM METHODS (camelCase for methods)
    // =========================================================================

    /**
     * Scope a query to only include active suppliers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the total number of transactions for this supplier.
     *
     * @return int
     */
    public function getTotalTransactionsCount(): int
    {
        // Optimized way to count related records without loading them all
        return $this->transactions()->count();
    }
}
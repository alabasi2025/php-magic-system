<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="AccountBalance",
 *     title="AccountBalance",
 *     description="Model representing the balance of an account for a specific fiscal period.",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="The unique identifier for the account balance record."),
 *     @OA\Property(property="account_id", type="integer", description="Foreign key to the Account model."),
 *     @OA\Property(property="fiscal_period_id", type="integer", description="Foreign key to the FiscalPeriod model."),
 *     @OA\Property(property="opening_balance", type="number", format="float", description="The balance at the start of the fiscal period."),
 *     @OA\Property(property="closing_balance", type="number", format="float", description="The balance at the end of the fiscal period."),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="Timestamp of creation."),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="Timestamp of last update.")
 * )
 */
class AccountBalance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_balances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'fiscal_period_id',
        'opening_balance',
        'closing_balance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'opening_balance' => 'decimal:4', // Assuming 4 decimal places for precision
        'closing_balance' => 'decimal:4',
    ];

    /**
     * Get the account that owns the balance record.
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        // Assuming 'Account' model exists in the same namespace
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * Get the fiscal period associated with the balance record.
     *
     * @return BelongsTo
     */
    public function fiscalPeriod(): BelongsTo
    {
        // Assuming 'FiscalPeriod' model exists in the same namespace
        return $this->belongsTo(FiscalPeriod::class, 'fiscal_period_id');
    }

    // --- Custom Methods (Example of best practice) ---

    /**
     * Calculate the net change in balance during the fiscal period.
     *
     * @return float
     */
    public function calculateNetChange(): float
    {
        // Ensure the attributes are accessed as floats for calculation
        return (float) $this->closing_balance - (float) $this->opening_balance;
    }

    /**
     * Scope a query to only include balances for a specific account.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $accountId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAccount($query, int $accountId)
    {
        return $query->where('account_id', $accountId);
    }
}
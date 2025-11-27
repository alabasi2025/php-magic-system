<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="FiscalPeriod",
 *     title="Fiscal Period",
 *     description="Represents a specific period within a fiscal year, used for financial reporting and balance tracking.",
 *     @OA\Property(property="id", type="integer", readOnly="true", description="The unique identifier for the fiscal period."),
 *     @OA\Property(property="fiscal_year_id", type="integer", description="Foreign key to the associated fiscal year."),
 *     @OA\Property(property="period_name", type="string", description="The name of the period (e.g., 'Q1', 'Month 1')."),
 *     @OA\Property(property="start_date", type="string", format="date", description="The start date of the fiscal period."),
 *     @OA\Property(property="end_date", type="string", format="date", description="The end date of the fiscal period."),
 *     @OA\Property(property="is_closed", type="boolean", description="Indicates if the period is closed for transactions."),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", description="Timestamp of creation."),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", description="Timestamp of last update.")
 * )
 */
class FiscalPeriod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fiscal_year_id',
        'period_name',
        'start_date',
        'end_date',
        'is_closed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_closed' => 'boolean',
    ];

    /**
     * Get the fiscal year that this period belongs to.
     *
     * @return BelongsTo
     */
    public function fiscalYear(): BelongsTo
    {
        // Assumes a 'fiscal_year_id' foreign key on the 'fiscal_periods' table
        // and a 'FiscalYear' model exists.
        return $this->belongsTo(FiscalYear::class);
    }

    /**
     * Get the account balances associated with this fiscal period.
     *
     * @return HasMany
     */
    public function accountBalances(): HasMany
    {
        // Assumes a 'fiscal_period_id' foreign key on the 'account_balances' table
        // and an 'AccountBalance' model exists.
        return $this->hasMany(AccountBalance::class);
    }

    /**
     * Scope a query to only include open (not closed) periods.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeOpen($query): void
    {
        $query->where('is_closed', false);
    }

    /**
     * Check if the current fiscal period is closed.
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return (bool) $this->is_closed;
    }
}
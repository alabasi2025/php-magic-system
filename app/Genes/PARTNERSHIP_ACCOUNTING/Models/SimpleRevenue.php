<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class SimpleRevenue extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'alabasi_simple_revenues';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'partner_id',
        'user_id',
        'amount',
        'revenue_date',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'revenue_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // ========================================================================
    //  RELATIONSHIPS
    // ========================================================================

    /**
     * Get the partner that owns the simple revenue.
     */
    public function partner(): BelongsTo
    {
        // Assuming a Partner model exists in the same Gene namespace or is globally accessible
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the user who recorded the simple revenue.
     */
    public function recorder(): BelongsTo
    {
        // Assuming a User model exists, typically in the App\Models namespace
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // ========================================================================
    //  SCOPES
    // ========================================================================

    /**
     * Scope a query to only include revenues for a given partner.
     */
    public function scopeForPartner(Builder $query, int $partnerId): void
    {
        $query->where('partner_id', $partnerId);
    }

    /**
     * Scope a query to only include revenues recorded on a specific date.
     */
    public function scopeByDate(Builder $query, string $date): void
    {
        $query->whereDate('revenue_date', $date);
    }

    // ========================================================================
    //  ACCESSORS
    // ========================================================================

    /**
     * Get the formatted amount attribute (e.g., $1,000.00).
     */
    protected function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get the formatted revenue date attribute (e.g., 2025-11-30).
     */
    protected function getRevenueDateFormattedAttribute(): string
    {
        return $this->revenue_date ? Carbon::parse($this->revenue_date)->format('Y-m-d') : '';
    }
}

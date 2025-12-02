<?php

namespace App\Genes\PARTNER_ACCOUNTING\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Genes\PARTNER_ACCOUNTING\Models\Partner; // افتراض وجود موديل Partner في نفس المسار

class PartnerSettlement extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'partner_settlements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'partner_id',
        'total_amount',
        'paid_amount',
        'status',
        'settlement_date',
    ];

    /**
     * Get the partner that owns the settlement.
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Scope a query to only include pending settlements.
     */
    public function scopePending(Builder $query): void
    {
        $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include paid settlements.
     */
    public function scopePaid(Builder $query): void
    {
        $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include partially paid settlements.
     */
    public function scopePartial(Builder $query): void
    {
        $query->where('status', 'partial');
    }

    /**
     * Scope a query to include settlements between two dates.
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('settlement_date', [$startDate, $endDate]);
    }

    /**
     * Calculate the remaining amount to be paid.
     */
    public function getRemainingAmount(): float
    {
        return (float) $this->total_amount - (float) $this->paid_amount;
    }
}

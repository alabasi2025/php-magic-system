<?php

namespace App\Genes\CASH_BOXES\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBoxTransaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cash_box_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cash_box_id',
        'amount',
        'type', // 'in' or 'out'
        'description',
        'transaction_date',
    ];

    /**
     * Get the cash box that owns the transaction.
     */
    public function cashBox(): BelongsTo
    {
        // Assuming the CashBox model is in the same namespace
        return $this->belongsTo(CashBox::class);
    }

    /**
     * Scope a query to include only 'in' transactions.
     */
    public function scopeInTransactions(Builder $query): void
    {
        $query->where('type', 'in');
    }

    /**
     * Scope a query to include only 'out' transactions.
     */
    public function scopeOutTransactions(Builder $query): void
    {
        $query->where('type', 'out');
    }

    /**
     * Scope a query to include transactions between two dates.
     */
    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }
}

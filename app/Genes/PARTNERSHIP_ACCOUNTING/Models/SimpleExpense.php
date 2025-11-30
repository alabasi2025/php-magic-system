<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SimpleExpense extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'simple_expenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'expense_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'float',
    ];

    // --- Relations (1) ---

    /**
     * Get the user that owns the simple expense.
     */
    public function user(): BelongsTo
    {
        // Assuming a standard User model exists in the App namespace
        return $this->belongsTo(\App\Models\User::class);
    }

    // --- Scopes (2) ---

    /**
     * Scope a query to only include expenses for the current authenticated user.
     */
    public function scopeOfUser(Builder $query, int $userId = null): void
    {
        $userId = $userId ?? Auth::id();
        $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include expenses from the last 30 days.
     */
    public function scopeRecent(Builder $query): void
    {
        $query->where('expense_date', '>=', now()->subDays(30));
    }

    // --- Accessors (1) ---

    /**
     * Get the formatted amount as a currency string.
     *
     * @return string
     */
    public function getFormattedAmountAttribute(): string
    {
        // Assuming a default currency format (e.g., SAR)
        return number_format($this->amount, 2) . ' SAR';
    }
}

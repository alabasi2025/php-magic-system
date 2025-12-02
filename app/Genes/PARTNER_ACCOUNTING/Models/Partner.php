<?php

namespace App\Genes\PARTNER_ACCOUNTING\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

// افتراض وجود هذه النماذج في نفس النطاق أو تم استيرادها
// يجب استبدالها بالمسارات الصحيحة إذا كانت مختلفة
class Transaction extends Model {}
class Settlement extends Model {}

class Partner extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'partners';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'code',
        'is_active',
        // Add other relevant fields
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // --- Relationships ---

    /**
     * Get the transactions for the partner.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the settlements for the partner.
     */
    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    // --- Scopes ---

    /**
     * Scope a query to only include active partners.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope a query to only include partners of a given type.
     */
    public function scopeByType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    /**
     * Scope a query to only include partners with a given code.
     */
    public function scopeByCode(Builder $query, string $code): void
    {
        $query->where('code', $code);
    }

    // --- Methods ---

    /**
     * Calculate the current balance for the partner.
     * Balance = Total Deposits - Total Withdrawals - Total Share Amount
     */
    public function getBalance(): float
    {
        $deposits = $this->getTotalDeposits();
        $withdrawals = $this->getTotalWithdrawals();
        $shareAmount = $this->getShareAmount();

        return $deposits - $withdrawals - $shareAmount;
    }

    /**
     * Calculate the total deposits for the partner.
     * Assuming 'transactions' has a 'type' column and 'amount' column.
     */
    public function getTotalDeposits(): float
    {
        // Placeholder logic: assuming 'deposit' is a transaction type
        return $this->transactions()
                    ->where('type', 'deposit')
                    ->sum('amount');
    }

    /**
     * Calculate the total withdrawals for the partner.
     * Assuming 'transactions' has a 'type' column and 'amount' column.
     */
    public function getTotalWithdrawals(): float
    {
        // Placeholder logic: assuming 'withdrawal' is a transaction type
        return $this->transactions()
                    ->where('type', 'withdrawal')
                    ->sum('amount');
    }

    /**
     * Calculate the total share amount for the partner.
     * Assuming 'settlements' has an 'amount' column representing the share.
     */
    public function getShareAmount(): float
    {
        return $this->settlements()->sum('amount');
    }
}

<?php

namespace App\Genes\CASH_BOXES\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashBox extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cash_boxes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    /**
     * The transactions relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        // Assuming a Transaction model exists in the same namespace or is imported
        // and the foreign key is 'cash_box_id'
        return $this->hasMany(Transaction::class, 'cash_box_id');
    }

    /**
     * Scope a query to only include active cash boxes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope a query to only include cash boxes with a given code.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $code
     * @return void
     */
    public function scopeByCode(Builder $query, string $code): void
    {
        $query->where('code', $code);
    }

    /**
     * Get the total amount of money that has come into the cash box.
     *
     * @return float
     */
    public function getTotalIn(): float
    {
        // Assuming 'transactions' are related and 'type' is used to distinguish 'in'
        return $this->transactions()
                    ->where('type', 'in')
                    ->sum('amount');
    }

    /**
     * Get the total amount of money that has gone out of the cash box.
     *
     * @return float
     */
    public function getTotalOut(): float
    {
        // Assuming 'transactions' are related and 'type' is used to distinguish 'out'
        return $this->transactions()
                    ->where('type', 'out')
                    ->sum('amount');
    }

    /**
     * Get the current balance of the cash box.
     *
     * @return float
     */
    public function getBalance(): float
    {
        return $this->getTotalIn() - $this->getTotalOut();
    }
}

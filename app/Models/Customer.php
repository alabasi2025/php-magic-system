<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property float $credit_limit The maximum credit allowed for the customer.
 * @property float $current_credit The current outstanding credit/debt of the customer.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Contact[] $contacts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 */
class Customer extends Model
{
    use HasFactory;

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
        'credit_limit',
        'current_credit',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Assuming credit fields are stored as DECIMAL in DB, casting to float for usage.
        'credit_limit' => 'float',
        'current_credit' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the contacts associated with the customer.
     *
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        // Assuming a Contact model exists and has a foreign key 'customer_id'
        return $this->hasMany(Contact::class);
    }

    /**
     * Get the transactions associated with the customer.
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        // Assuming a Transaction model exists and has a foreign key 'customer_id'
        return $this->hasMany(Transaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Credit Management Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the customer has enough available credit for a new transaction.
     *
     * @param float $amount The amount of the new transaction (should be positive).
     * @return bool
     */
    public function hasSufficientCredit(float $amount): bool
    {
        // Available credit is the limit minus the current outstanding credit.
        $availableCredit = $this->credit_limit - $this->current_credit;

        // Check if the new amount is within the available credit.
        return $amount <= $availableCredit;
    }

    /**
     * Increase the customer's current credit/debt (e.g., when a sale is made on credit).
     *
     * @param float $amount The amount to increase the credit by. Must be positive.
     * @return bool
     * @throws \Exception If the amount is not positive or exceeds the credit limit.
     */
    public function increaseCredit(float $amount): bool
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit increase amount must be positive.');
        }

        // Check if the new credit amount will exceed the limit
        if (($this->current_credit + $amount) > $this->credit_limit) {
            throw new \Exception('Credit increase exceeds the customer\'s credit limit.');
        }

        // Use a transaction to ensure atomicity
        return DB::transaction(function () use ($amount) {
            $this->current_credit += $amount;
            return $this->save();
        });
    }

    /**
     * Decrease the customer's current credit/debt (e.g., when a payment is received).
     *
     * @param float $amount The amount to decrease the credit by. Must be positive.
     * @return bool
     * @throws \Exception If the amount is not positive or results in a negative credit balance.
     */
    public function decreaseCredit(float $amount): bool
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit decrease amount must be positive.');
        }

        // Use a transaction to ensure atomicity
        return DB::transaction(function () use ($amount) {
            // Ensure current_credit doesn't go below zero
            $this->current_credit = max(0, $this->current_credit - $amount);
            return $this->save();
        });
    }

    /**
     * Get the available credit remaining for the customer.
     *
     * @return float
     */
    public function getAvailableCredit(): float
    {
        return $this->credit_limit - $this->current_credit;
    }

    /**
     * Check if the customer is currently over their credit limit.
     *
     * @return bool
     */
    public function isOverLimit(): bool
    {
        return $this->current_credit > $this->credit_limit;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'bank_name',
        'account_number',
        'iban',
        'swift_code',
        'branch',
        'currency',
        'balance',
        'is_active',
        'intermediate_account_id',
        'unit_id',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the intermediate account
     */
    public function intermediateAccount()
    {
        return $this->belongsTo(ChartAccount::class, 'intermediate_account_id');
    }

    /**
     * Get the unit
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all receipts
     */
    public function receipts()
    {
        return $this->morphMany(CashReceipt::class, 'account');
    }

    /**
     * Get all payments
     */
    public function payments()
    {
        return $this->morphMany(CashPayment::class, 'account');
    }
}

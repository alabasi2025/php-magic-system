<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_number',
        'payment_date',
        'account_type', // 'cash_box' or 'bank_account'
        'account_id',
        'amount',
        'currency',
        'exchange_rate',
        'amount_in_base_currency',
        'paid_to',
        'paid_to_type', // 'customer', 'supplier', 'employee', 'other'
        'paid_to_id',
        'payment_method', // 'cash', 'check', 'transfer', 'card'
        'check_number',
        'check_date',
        'check_bank',
        'transfer_reference',
        'card_reference',
        'description',
        'notes',
        'category', // 'purchases', 'expenses', 'salaries', 'loan', 'investment', 'other'
        'reference_type', // 'invoice', 'contract', 'loan', 'expense', 'other'
        'reference_id',
        'reference_number',
        'status', // 'draft', 'pending', 'approved', 'posted', 'cancelled'
        'journal_entry_id',
        'attachments',
        'ai_suggestions', // JSON field for AI suggestions
        'created_by',
        'approved_by',
        'posted_by',
        'cancelled_by',
        'approved_at',
        'posted_at',
        'cancelled_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'check_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_in_base_currency' => 'decimal:2',
        'attachments' => 'array',
        'ai_suggestions' => 'array',
        'approved_at' => 'datetime',
        'posted_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the account (CashBox or BankAccount)
     */
    public function account()
    {
        return $this->morphTo();
    }

    /**
     * Get the paid to entity
     */
    public function paidTo()
    {
        return $this->morphTo('paid_to');
    }

    /**
     * Get the reference entity
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Get the journal entry
     */
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Get the creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the approver
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the poster
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Get the canceller
     */
    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Scope for active payments
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['approved', 'posted']);
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Generate payment number
     */
    public static function generatePaymentNumber()
    {
        $lastPayment = self::latest('id')->first();
        $number = $lastPayment ? intval(substr($lastPayment->payment_number, 3)) + 1 : 1;
        return 'CP-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

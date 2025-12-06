<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt_number',
        'receipt_date',
        'account_type', // 'cash_box' or 'bank_account'
        'account_id',
        'amount',
        'currency',
        'exchange_rate',
        'amount_in_base_currency',
        'received_from',
        'received_from_type', // 'customer', 'supplier', 'employee', 'other'
        'received_from_id',
        'payment_method', // 'cash', 'check', 'transfer', 'card'
        'check_number',
        'check_date',
        'check_bank',
        'transfer_reference',
        'card_reference',
        'description',
        'notes',
        'category', // 'sales', 'services', 'loan', 'investment', 'other'
        'reference_type', // 'invoice', 'contract', 'loan', 'other'
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
        'receipt_date' => 'date',
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
     * Get the received from entity
     */
    public function receivedFrom()
    {
        return $this->morphTo('received_from');
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
     * Scope for active receipts
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['approved', 'posted']);
    }

    /**
     * Scope for pending receipts
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Generate receipt number
     */
    public static function generateReceiptNumber()
    {
        $lastReceipt = self::latest('id')->first();
        $number = $lastReceipt ? intval(substr($lastReceipt->receipt_number, 3)) + 1 : 1;
        return 'CR-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

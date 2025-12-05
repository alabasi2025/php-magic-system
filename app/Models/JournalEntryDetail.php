<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntryDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journal_entry_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit',
        'credit',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    /**
     * Get the journal entry that owns the detail.
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Get the account for this detail.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'account_id');
    }

    /**
     * Alias for account() to maintain backward compatibility.
     */
    public function chartAccount(): BelongsTo
    {
        return $this->account();
    }

    /**
     * Check if this is a debit entry.
     */
    public function isDebit(): bool
    {
        return $this->debit > 0;
    }

    /**
     * Check if this is a credit entry.
     */
    public function isCredit(): bool
    {
        return $this->credit > 0;
    }

    /**
     * Get the amount (debit or credit).
     */
    public function getAmount(): float
    {
        return $this->isDebit() ? $this->debit : $this->credit;
    }

    /**
     * Get the type (debit or credit).
     */
    public function getType(): string
    {
        return $this->isDebit() ? 'debit' : 'credit';
    }
}

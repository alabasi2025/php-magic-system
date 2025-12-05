<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journal_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entry_number',
        'entry_date',
        'description',
        'reference',
        'unit_id',
        'user_id',
        'status',
        'notes',
        'total_debit',
        'total_credit',
        'is_balanced',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'is_balanced' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the unit that owns the journal entry.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the user that owns the journal entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the journal entry.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the journal entry.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved the journal entry.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the details for the journal entry.
     */
    public function details(): HasMany
    {
        return $this->hasMany(JournalEntryDetail::class);
    }

    /**
     * Alias for details() to maintain backward compatibility.
     */
    public function journalEntryDetails(): HasMany
    {
        return $this->details();
    }

    /**
     * Calculate total debit from details.
     */
    public function calculateTotalDebit(): float
    {
        return $this->details()->sum('debit');
    }

    /**
     * Calculate total credit from details.
     */
    public function calculateTotalCredit(): float
    {
        return $this->details()->sum('credit');
    }

    /**
     * Check if the journal entry is balanced.
     */
    public function checkBalance(): bool
    {
        $debit = $this->calculateTotalDebit();
        $credit = $this->calculateTotalCredit();
        
        return abs($debit - $credit) < 0.01;
    }

    /**
     * Get the total debit amount.
     */
    public function totalDebit(): float
    {
        return (float) $this->total_debit;
    }

    /**
     * Get the total credit amount.
     */
    public function totalCredit(): float
    {
        return (float) $this->total_credit;
    }

    /**
     * Check if entry is balanced.
     */
    public function isBalanced(): bool
    {
        return $this->is_balanced;
    }

    /**
     * Scope a query to only include draft entries.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include pending entries.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved entries.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include posted entries.
     */
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    /**
     * Scope a query to only include rejected entries.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}

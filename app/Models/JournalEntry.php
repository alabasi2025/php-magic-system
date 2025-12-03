<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $fillable = [
        'unit_id',
        'user_id',
        'date',
        'description',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function journalEntryDetails(): HasMany
    {
        return $this->hasMany(JournalEntryDetail::class);
    }

    public function totalDebit(): float
    {
        return $this->journalEntryDetails()->sum('debit');
    }

    public function totalCredit(): float
    {
        return $this->journalEntryDetails()->sum('credit');
    }

    public function isBalanced(): bool
    {
        return round($this->totalDebit(), 2) === round($this->totalCredit(), 2);
    }
}
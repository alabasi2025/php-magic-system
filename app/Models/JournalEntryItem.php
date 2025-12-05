<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $journal_entry_id
 * @property int $account_id
 * @property string $type (debit/credit)
 * @property float $amount
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class JournalEntryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'type', // debit or credit
        'amount',
    ];

    // علاقة تفصيل القيد مع القيد الرئيسي
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    // علاقة تفصيل القيد مع الحساب (نفترض وجود نموذج Account)
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

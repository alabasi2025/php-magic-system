<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $entry_date
 * @property string $description
 * @property string $reference_number
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_date',
        'description',
        'reference_number',
        'user_id',
    ];

    // علاقة القيد مع تفاصيله (أطراف القيد)
    public function items()
    {
        return $this->hasMany(JournalEntryItem::class);
    }
}

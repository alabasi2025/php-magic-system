<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $item_id
 * @property string $type // 'in' or 'out'
 * @property float $quantity
 * @property \Illuminate\Support\Carbon $transaction_date
 */
class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'type',
        'quantity',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    /**
     * علاقة حركة المخزون بالصنف.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

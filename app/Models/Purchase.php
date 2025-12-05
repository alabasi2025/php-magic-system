<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $item_id
 * @property float $quantity
 * @property float $total_price
 * @property \Illuminate\Support\Carbon $purchase_date
 */
class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'quantity',
        'total_price',
        'purchase_date',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
    ];

    /**
     * علاقة المشتريات بالصنف.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

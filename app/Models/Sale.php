<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $item_id
 * @property float $quantity
 * @property float $total_price
 * @property \Illuminate\Support\Carbon $sale_date
 */
class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'quantity',
        'total_price',
        'sale_date',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
    ];

    /**
     * علاقة المبيعات بالصنف.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

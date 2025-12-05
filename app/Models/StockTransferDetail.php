<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

/**
 * نموذج تفاصيل تحويل المخزون
 * يمثل المواد والكميات في طلب التحويل.
 */
class StockTransferDetail extends Model
{
    use HasFactory;

    // الحقول المسموح بتعبئتها
    protected $fillable = [
        'stock_transfer_id',
        'item_id',
        'quantity',
    ];

    /**
     * علاقة التحويل الذي تنتمي إليه التفاصيل.
     */
    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class);
    }

    /**
     * علاقة المادة المحولة.
     */
    public function item()
    {
        // نفترض وجود نموذج Item
        return $this->belongsTo(Item::class);
    }
}

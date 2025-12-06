<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Purchase Receipt Item Model
 * أصناف استلام البضاعة
 * 
 * @property int $id
 * @property int $purchase_receipt_id استلام البضاعة
 * @property int $item_id الصنف
 * @property float $quantity الكمية المستلمة
 * @property float $unit_price سعر الوحدة
 * @property float $total_amount المجموع
 * @property string|null $notes ملاحظات
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class PurchaseReceiptItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_receipt_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_receipt_id',
        'item_id',
        'quantity',
        'unit_price',
        'total_amount',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the purchase receipt that owns the item.
     * استلام البضاعة المرتبط بالصنف
     *
     * @return BelongsTo
     */
    public function purchaseReceipt(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceipt::class, 'purchase_receipt_id');
    }

    /**
     * Get the item details.
     * تفاصيل الصنف
     *
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Calculate the total amount.
     * حساب المجموع
     *
     * @return float
     */
    public function calculateTotal(): float
    {
        return $this->quantity * $this->unit_price;
    }
}

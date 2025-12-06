<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Purchase Order Item Model
 * أصناف أمر الشراء
 * 
 * @property int $id
 * @property int $purchase_order_id أمر الشراء
 * @property int $item_id الصنف
 * @property float $quantity الكمية المطلوبة
 * @property float $received_quantity الكمية المستلمة
 * @property float $unit_price سعر الوحدة
 * @property float $tax_rate نسبة الضريبة
 * @property float $discount_rate نسبة الخصم
 * @property float $total_amount المجموع
 * @property string|null $notes ملاحظات
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class PurchaseOrderItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'quantity',
        'received_quantity',
        'unit_price',
        'tax_rate',
        'discount_rate',
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
        'received_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the purchase order that owns the item.
     * أمر الشراء المرتبط بالصنف
     *
     * @return BelongsTo
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
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
     * Calculate the line total before tax and discount.
     * حساب المجموع قبل الضريبة والخصم
     *
     * @return float
     */
    public function getSubtotal(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Calculate the discount amount.
     * حساب قيمة الخصم
     *
     * @return float
     */
    public function getDiscountAmount(): float
    {
        return $this->getSubtotal() * ($this->discount_rate / 100);
    }

    /**
     * Calculate the amount after discount.
     * حساب المبلغ بعد الخصم
     *
     * @return float
     */
    public function getAmountAfterDiscount(): float
    {
        return $this->getSubtotal() - $this->getDiscountAmount();
    }

    /**
     * Calculate the tax amount.
     * حساب قيمة الضريبة
     *
     * @return float
     */
    public function getTaxAmount(): float
    {
        return $this->getAmountAfterDiscount() * ($this->tax_rate / 100);
    }

    /**
     * Calculate the total amount including tax and discount.
     * حساب المجموع الكلي شامل الضريبة والخصم
     *
     * @return float
     */
    public function calculateTotal(): float
    {
        return $this->getAmountAfterDiscount() + $this->getTaxAmount();
    }

    /**
     * Get the remaining quantity to be received.
     * الحصول على الكمية المتبقية للاستلام
     *
     * @return float
     */
    public function getRemainingQuantity(): float
    {
        return $this->quantity - $this->received_quantity;
    }

    /**
     * Check if the item is fully received.
     * التحقق من استلام الصنف بالكامل
     *
     * @return bool
     */
    public function isFullyReceived(): bool
    {
        return $this->received_quantity >= $this->quantity;
    }

    /**
     * Check if the item is partially received.
     * التحقق من استلام الصنف جزئياً
     *
     * @return bool
     */
    public function isPartiallyReceived(): bool
    {
        return $this->received_quantity > 0 && $this->received_quantity < $this->quantity;
    }

    /**
     * Update the received quantity.
     * تحديث الكمية المستلمة
     *
     * @param float $quantity
     * @return void
     */
    public function addReceivedQuantity(float $quantity): void
    {
        $this->received_quantity += $quantity;
        $this->save();
    }
}

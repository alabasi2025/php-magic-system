<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Purchase Invoice Item Model
 * أصناف فاتورة المشتريات
 * 
 * @property int $id
 * @property int $purchase_invoice_id فاتورة المشتريات
 * @property int $item_id الصنف
 * @property float $quantity الكمية
 * @property float $unit_price سعر الوحدة
 * @property float $tax_rate نسبة الضريبة
 * @property float $discount_rate نسبة الخصم
 * @property float $total_amount المجموع
 * @property string|null $notes ملاحظات
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class PurchaseInvoiceItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_invoice_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_invoice_id',
        'item_id',
        'quantity',
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
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the purchase invoice that owns the item.
     * فاتورة المشتريات المرتبطة بالصنف
     *
     * @return BelongsTo
     */
    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Purchase Order Model
 * أوامر الشراء
 * 
 * @property int $id
 * @property string $order_number رقم الأمر (تلقائي فريد)
 * @property int $supplier_id المورد
 * @property string $order_date تاريخ الأمر
 * @property string|null $expected_date تاريخ التسليم المتوقع
 * @property int $warehouse_id المخزن المستلم
 * @property float $subtotal المجموع الفرعي
 * @property float $tax_amount قيمة الضريبة
 * @property float $discount_amount قيمة الخصم
 * @property float $total_amount المجموع الكلي
 * @property string $status الحالة (draft/sent/confirmed/partially_received/received/cancelled)
 * @property string $payment_status حالة الدفع (unpaid/partially_paid/paid)
 * @property string|null $notes ملاحظات
 * @property int $created_by المستخدم المنشئ
 * @property int|null $approved_by المستخدم المعتمد
 * @property \Illuminate\Support\Carbon|null $approved_at تاريخ الاعتماد
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'supplier_id',
        'order_date',
        'expected_date',
        'warehouse_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'payment_status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        // للتوافق مع النظام القديم
        'name',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the supplier that owns the purchase order.
     * المورد المرتبط بأمر الشراء
     *
     * @return BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get the warehouse that owns the purchase order.
     * المخزن المرتبط بأمر الشراء
     *
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Get all items for this purchase order.
     * جميع الأصناف في أمر الشراء
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    /**
     * Get the user who created the purchase order.
     * المستخدم الذي أنشأ أمر الشراء
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the purchase order.
     * المستخدم الذي اعتمد أمر الشراء
     *
     * @return BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all receipts for this purchase order.
     * جميع استلامات البضاعة لأمر الشراء
     *
     * @return HasMany
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(PurchaseReceipt::class, 'purchase_order_id');
    }

    /**
     * Get all invoices for this purchase order.
     * جميع الفواتير لأمر الشراء
     *
     * @return HasMany
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(PurchaseInvoice::class, 'purchase_order_id');
    }

    /**
     * Scope a query to filter by status.
     * تصفية حسب الحالة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by payment status.
     * تصفية حسب حالة الدفع
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $paymentStatus
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPaymentStatus($query, string $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Scope a query to filter by date range.
     * تصفية حسب نطاق التاريخ
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }

    /**
     * Get the attributes that should be searchable (للتوافق مع النظام القديم).
     *
     * @return array<int, string>
     */
    public function getSearchableAttributes(): array
    {
        return ['order_number', 'notes', 'name', 'description'];
    }

    /**
     * Check if order is approved.
     * التحقق من اعتماد الأمر
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return !is_null($this->approved_by) && !is_null($this->approved_at);
    }

    /**
     * Check if order is draft.
     * التحقق من أن الأمر مسودة
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if order is fully received.
     * التحقق من استلام الأمر بالكامل
     *
     * @return bool
     */
    public function isFullyReceived(): bool
    {
        return $this->status === 'received';
    }

    /**
     * Check if order is cancelled.
     * التحقق من إلغاء الأمر
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get the total received quantity for all items.
     * الحصول على إجمالي الكميات المستلمة
     *
     * @return float
     */
    public function getTotalReceivedQuantity(): float
    {
        return $this->items()->sum('received_quantity');
    }

    /**
     * Get the total ordered quantity for all items.
     * الحصول على إجمالي الكميات المطلوبة
     *
     * @return float
     */
    public function getTotalOrderedQuantity(): float
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Calculate the receipt completion percentage.
     * حساب نسبة اكتمال الاستلام
     *
     * @return float
     */
    public function getReceiptCompletionPercentage(): float
    {
        $totalOrdered = $this->getTotalOrderedQuantity();
        if ($totalOrdered == 0) {
            return 0;
        }
        $totalReceived = $this->getTotalReceivedQuantity();
        return ($totalReceived / $totalOrdered) * 100;
    }
}

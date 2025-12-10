<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Purchase Receipt Model
 * استلام البضاعة
 * 
 * @property int $id
 * @property string $receipt_number رقم الاستلام (تلقائي فريد)
 * @property int|null $purchase_order_id أمر الشراء (اختياري)
 * @property int $supplier_id المورد
 * @property int $warehouse_id المخزن
 * @property string $receipt_date تاريخ الاستلام
 * @property string|null $reference_number رقم المرجع (من المورد)
 * @property string $status الحالة (pending/approved/rejected)
 * @property string|null $notes ملاحظات
 * @property int $created_by المستخدم المنشئ
 * @property int|null $approved_by المستخدم المعتمد
 * @property \Illuminate\Support\Carbon|null $approved_at تاريخ الاعتماد
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class PurchaseReceipt extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_receipts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receipt_number',
        'purchase_order_id',
        'purchase_invoice_id',
        'supplier_id',
        'warehouse_id',
        'receipt_date',
        'reference_number',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'receipt_date' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the purchase order associated with the receipt.
     * أمر الشراء المرتبط بالاستلام
     *
     * @return BelongsTo
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Get the purchase invoice associated with the receipt.
     * فاتورة الشراء المرتبطة بالاستلام
     *
     * @return BelongsTo
     */
    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    /**
     * Get the supplier associated with the receipt.
     * المورد المرتبط بالاستلام
     *
     * @return BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get the warehouse associated with the receipt.
     * المخزن المرتبط بالاستلام
     *
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Get all items for this receipt.
     * جميع الأصناف في الاستلام
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReceiptItem::class, 'purchase_receipt_id');
    }

    /**
     * Get the user who created the receipt.
     * المستخدم الذي أنشأ الاستلام
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the receipt.
     * المستخدم الذي اعتمد الاستلام
     *
     * @return BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all stock movements created from this receipt.
     * جميع حركات المخزون المنشأة من هذا الاستلام
     *
     * @return HasMany
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'reference_id')
            ->where('reference_type', 'purchase_receipt');
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
     * Scope a query to filter pending receipts.
     * تصفية الاستلامات المعلقة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to filter approved receipts.
     * تصفية الاستلامات المعتمدة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Check if receipt is approved.
     * التحقق من اعتماد الاستلام
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if receipt is pending.
     * التحقق من أن الاستلام معلق
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if receipt is rejected.
     * التحقق من رفض الاستلام
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if receipt is linked to a purchase order.
     * التحقق من ربط الاستلام بأمر شراء
     *
     * @return bool
     */
    public function hasOrder(): bool
    {
        return !is_null($this->purchase_order_id);
    }

    /**
     * Get the total value of all items in the receipt.
     * الحصول على القيمة الإجمالية لجميع الأصناف
     *
     * @return float
     */
    public function getTotalValue(): float
    {
        return $this->items()->sum('total_amount');
    }
}

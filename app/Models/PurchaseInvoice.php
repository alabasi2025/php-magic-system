<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Purchase Invoice Model
 * فواتير الموردين
 * 
 * @property int $id
 * @property string $invoice_number رقم الفاتورة (من المورد)
 * @property string $internal_number رقم داخلي (تلقائي فريد)
 * @property int|null $purchase_order_id أمر الشراء (اختياري)
 * @property int|null $purchase_receipt_id استلام البضاعة (اختياري)
 * @property int $supplier_id المورد
 * @property string $invoice_date تاريخ الفاتورة
 * @property string|null $due_date تاريخ الاستحقاق
 * @property float $subtotal المجموع الفرعي
 * @property float $tax_amount قيمة الضريبة
 * @property float $discount_amount قيمة الخصم
 * @property float $total_amount المجموع الكلي
 * @property float $paid_amount المبلغ المدفوع
 * @property float $remaining_amount المبلغ المتبقي
 * @property string $payment_status حالة الدفع (unpaid/partially_paid/paid)
 * @property string $status الحالة (draft/approved/cancelled)
 * @property string|null $notes ملاحظات
 * @property int $created_by المستخدم المنشئ
 * @property int|null $approved_by المستخدم المعتمد
 * @property \Illuminate\Support\Carbon|null $approved_at تاريخ الاعتماد
 * @property int|null $journal_entry_id القيد المحاسبي
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_number',
        'internal_number',
        'purchase_order_id',
        'purchase_receipt_id',
        'supplier_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_status',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'journal_entry_id',
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
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
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
     * Get the purchase order associated with the invoice.
     * أمر الشراء المرتبط بالفاتورة
     *
     * @return BelongsTo
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Get the purchase receipt associated with the invoice.
     * استلام البضاعة المرتبط بالفاتورة
     *
     * @return BelongsTo
     */
    public function purchaseReceipt(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceipt::class, 'purchase_receipt_id');
    }

    /**
     * Get all receipts for this invoice.
     * جميع استلامات البضاعة لهذه الفاتورة
     *
     * @return HasMany
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(PurchaseReceipt::class, 'purchase_invoice_id');
    }

    /**
     * Get the supplier associated with the invoice.
     * المورد المرتبط بالفاتورة
     *
     * @return BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get all items for this invoice.
     * جميع الأصناف في الفاتورة
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class, 'purchase_invoice_id');
    }

    /**
     * Get all payments for this invoice.
     * جميع الدفعات للفاتورة
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'purchase_invoice_id');
    }

    /**
     * Get the user who created the invoice.
     * المستخدم الذي أنشأ الفاتورة
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the invoice.
     * المستخدم الذي اعتمد الفاتورة
     *
     * @return BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the journal entry associated with the invoice.
     * القيد المحاسبي المرتبط بالفاتورة
     *
     * @return BelongsTo
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
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
     * Scope a query to filter unpaid invoices.
     * تصفية الفواتير غير المدفوعة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    /**
     * Scope a query to filter overdue invoices.
     * تصفية الفواتير المتأخرة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('payment_status', ['unpaid', 'partially_paid']);
    }

    /**
     * Get the attributes that should be searchable (للتوافق مع النظام القديم).
     *
     * @return array<int, string>
     */
    public function getSearchableAttributes(): array
    {
        return ['invoice_number', 'internal_number', 'notes', 'name', 'description'];
    }

    /**
     * Check if invoice is approved.
     * التحقق من اعتماد الفاتورة
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if invoice is draft.
     * التحقق من أن الفاتورة مسودة
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if invoice is fully paid.
     * التحقق من سداد الفاتورة بالكامل
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if invoice is unpaid.
     * التحقق من أن الفاتورة غير مدفوعة
     *
     * @return bool
     */
    public function isUnpaid(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    /**
     * Check if invoice is overdue.
     * التحقق من تأخر الفاتورة
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now() && !$this->isPaid();
    }

    /**
     * Check if invoice has journal entry.
     * التحقق من وجود قيد محاسبي
     *
     * @return bool
     */
    public function hasJournalEntry(): bool
    {
        return !is_null($this->journal_entry_id);
    }

    /**
     * Calculate remaining amount.
     * حساب المبلغ المتبقي
     *
     * @return float
     */
    public function calculateRemainingAmount(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Update payment status based on paid amount.
     * تحديث حالة الدفع بناءً على المبلغ المدفوع
     *
     * @return void
     */
    public function updatePaymentStatus(): void
    {
        if ($this->paid_amount <= 0) {
            $this->payment_status = 'unpaid';
        } elseif ($this->paid_amount >= $this->total_amount) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partially_paid';
        }
        $this->save();
    }
}

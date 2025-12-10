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
        'warehouse_id',
        'payment_method',
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
     * Get the invoice type.
     * نوع الفاتورة
     *
     * @return BelongsTo
     */
    public function invoiceType(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoiceType::class, 'invoice_type_id');
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
     * Get the warehouse associated with the invoice.
     * المخزن المرتبط بالفاتورة
     *
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
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

    /**
     * Create a new purchase invoice with items.
     * إنشاء فاتورة شراء جديدة مع الأصناف
     *
     * @param \Illuminate\Http\Request $request
     * @return PurchaseInvoice
     */
    public static function createPurchaseInvoice($request)
    {
        \DB::beginTransaction();
        
        try {
            // محاولة إنشاء الفاتورة مع إعادة المحاولة في حالة التكرار
            $maxRetries = 5;
            $invoice = null;
            
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    // الحصول على نوع الفاتورة
                    $invoiceType = null;
                    if ($request->has('invoice_type_id') && $request->invoice_type_id) {
                        $invoiceType = PurchaseInvoiceType::find($request->invoice_type_id);
                    }
                    
                    $invoice = self::create([
                        'invoice_type_id' => $invoiceType ? $invoiceType->id : null,
                        'invoice_number' => $invoiceType ? $invoiceType->getNextInvoiceNumber() : self::generateInvoiceNumber(),
                        'internal_number' => self::generateInternalNumber(),
                        'supplier_id' => $request->supplier_id,
                        'warehouse_id' => $request->warehouse_id,
                        'invoice_date' => $request->invoice_date,
                        'due_date' => $request->due_date,
                        'payment_method' => $request->payment_method,
                        'status' => $request->status ?? 'draft',
                        'payment_status' => 'unpaid',
                        'notes' => $request->notes,
                        'subtotal' => 0,
                        'tax_amount' => 0,
                        'discount_amount' => $request->discount_amount ?? 0,
                        'total_amount' => 0,
                        'paid_amount' => 0,
                        'remaining_amount' => 0,
                        'created_by' => auth()->id() ?? \App\Models\User::first()->id,
                    ]);
                    
                    // نجحت العملية
                    break;
                    
                } catch (\Illuminate\Database\QueryException $e) {
                    // في حالة خطأ التكرار
                    if ($e->getCode() == 23000 && $attempt < $maxRetries) {
                        // إعادة المحاولة
                        usleep(100000); // انتظار 100ms
                        continue;
                    }
                    
                    // إذا فشلت جميع المحاولات
                    throw $e;
                }
            }
            
            if (!$invoice) {
                throw new \Exception('فشل إنشاء الفاتورة بعد ' . $maxRetries . ' محاولات');
            }

            // إضافة العناصر
            if ($request->has('items') && !empty($request->items)) {
                self::createInvoiceItems($invoice, $request->items);
            }

            // حساب الإجمالي
            $invoice->calculateTotals();

            \DB::commit();
            
            return $invoice->load(['supplier', 'items']);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing purchase invoice.
     * تحديث فاتورة شراء موجودة
     *
     * @param \Illuminate\Http\Request $request
     * @return PurchaseInvoice|string
     */
    public function updatePurchaseInvoice($request)
    {
        // التحقق من أن الفاتورة غير معتمدة
        if ($this->status === 'approved') {
            return 'cannot_edit_approved_invoice';
        }
        
        \DB::beginTransaction();
        
        try {
            // تحديث بيانات الفاتورة
            $this->update([
                'invoice_number' => $request->invoice_number,
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'payment_method' => $request->payment_method,
                'status' => $request->status,
                'notes' => $request->notes,
                'discount_amount' => $request->discount_amount ?? 0,
            ]);

            // حذف العناصر القديمة
            $this->items()->delete();

            // إضافة العناصر الجديدة
            if ($request->has('items') && !empty($request->items)) {
                self::createInvoiceItems($this, $request->items);
            }

            // إعادة حساب الإجمالي
            $this->calculateTotals();

            \DB::commit();
            
            return $this->load(['supplier', 'items']);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create invoice items.
     * إنشاء أصناف الفاتورة
     *
     * @param PurchaseInvoice $invoice
     * @param array $items
     * @return void
     */
    protected static function createInvoiceItems($invoice, $items)
    {
        foreach ($items as $item) {
            $invoice->items()->create([
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'] ?? $item['price'] ?? 0,
                'discount_rate' => $item['discount'] ?? 0,
                'tax_rate' => $item['tax_rate'] ?? 0,
                'total_amount' => ($item['quantity'] * ($item['unit_price'] ?? $item['price'] ?? 0)) - ($item['discount'] ?? 0),
            ]);
        }
    }

    /**
     * Calculate and update invoice totals.
     * حساب وتحديث إجماليات الفاتورة
     *
     * @return void
     */
    public function calculateTotals()
    {
        $subtotal = $this->items()->sum(\DB::raw('quantity * unit_price'));
        $tax_amount = 0; // يمكن إضافة حساب الضريبة لاحقاً
        $total = $subtotal + $tax_amount - $this->discount_amount;

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $tax_amount,
            'total_amount' => $total,
            'remaining_amount' => $total - $this->paid_amount,
        ]);
    }

    /**
     * Generate internal number for invoice.
     * توليد رقم داخلي للفاتورة
     *
     * @return string
     */
    protected static function generateInternalNumber()
    {
        $year = date('Y');
        $prefix = 'PI-' . $year . '-';
        
        // البحث عن آخر رقم داخلي في نفس السنة
        $lastInvoice = self::where('internal_number', 'LIKE', $prefix . '%')
            ->orderBy('internal_number', 'desc')
            ->lockForUpdate()
            ->first();
        
        if ($lastInvoice) {
            // استخراج الرقم من آخر internal_number
            $lastNumber = (int) substr($lastInvoice->internal_number, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // التحقق من عدم وجود الرقم (في حالة وجود فجوات)
        $maxAttempts = 100; // حد أقصى للمحاولات لتجنب حلقة لا نهائية
        $attempts = 0;
        
        while ($attempts < $maxAttempts) {
            $internalNumber = $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            
            // التحقق من عدم وجود هذا الرقم
            $exists = self::where('internal_number', $internalNumber)->exists();
            
            if (!$exists) {
                return $internalNumber;
            }
            
            // إذا كان موجوداً، جرب الرقم التالي
            $nextNumber++;
            $attempts++;
        }
        
        // في حالة فشل جميع المحاولات (نادر جداً)
        throw new \Exception('فشل في توليد رقم داخلي فريد بعد ' . $maxAttempts . ' محاولة');
    }
    
    /**
     * Generate invoice number.
     * توليد رقم الفاتورة
     *
     * @return string
     */
    protected static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = 'INV-' . $year . $month . '-';
        
        // البحث عن آخر رقم فاتورة في نفس الشهر
        $lastInvoice = self::where('invoice_number', 'LIKE', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->lockForUpdate()
            ->first();
        
        if ($lastInvoice) {
            // استخراج الرقم من آخر invoice_number
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // التحقق من عدم وجود الرقم (في حالة وجود فجوات)
        $maxAttempts = 100;
        $attempts = 0;
        
        while ($attempts < $maxAttempts) {
            $invoiceNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
            // التحقق من عدم وجود هذا الرقم
            $exists = self::where('invoice_number', $invoiceNumber)->exists();
            
            if (!$exists) {
                return $invoiceNumber;
            }
            
            // إذا كان موجوداً، جرب الرقم التالي
            $nextNumber++;
            $attempts++;
        }
        
        // في حالة فشل جميع المحاولات
        throw new \Exception('فشل في توليد رقم فاتورة فريد بعد ' . $maxAttempts . ' محاولة');
    }
}

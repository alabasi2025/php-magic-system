<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Supplier Model
 * إدارة بيانات الموردين
 * 
 * @property int $id
 * @property string $code رمز المورد (فريد)
 * @property string $name اسم المورد
 * @property string|null $name_en الاسم بالإنجليزية
 * @property string|null $contact_person الشخص المسؤول
 * @property string $phone رقم الهاتف
 * @property string|null $email البريد الإلكتروني
 * @property string|null $address العنوان
 * @property string|null $tax_number الرقم الضريبي
 * @property string $payment_terms شروط الدفع (cash/credit)
 * @property float $credit_limit حد الائتمان
 * @property int $credit_days مدة الائتمان بالأيام
 * @property int|null $account_id ربط بحساب المورد في النظام المحاسبي
 * @property string $status الحالة (active/inactive)
 * @property string|null $notes ملاحظات
 * @property float $initial_balance الرصيد الافتتاحي (للتوافق مع النظام القديم)
 * @property float $balance الرصيد الحالي (للتوافق مع النظام القديم)
 * @property bool $is_active حالة النشاط (للتوافق مع النظام القديم)
 * @property int|null $user_id المستخدم المنشئ (للتوافق مع النظام القديم)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'suppliers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_number',
        'payment_terms',
        'credit_limit',
        'credit_days',
        'account_id',
        'status',
        'notes',
        // للتوافق مع النظام القديم
        'initial_balance',
        'balance',
        'is_active',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'credit_limit' => 'decimal:2',
        'credit_days' => 'integer',
        'initial_balance' => 'decimal:2',
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the account associated with the supplier.
     * ربط بالحساب المحاسبي
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'account_id');
    }

    /**
     * Get all purchase orders for this supplier.
     * جميع أوامر الشراء للمورد
     *
     * @return HasMany
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id');
    }

    /**
     * Get all purchase invoices for this supplier.
     * جميع فواتير المشتريات للمورد
     *
     * @return HasMany
     */
    public function purchaseInvoices(): HasMany
    {
        return $this->hasMany(PurchaseInvoice::class, 'supplier_id');
    }

    /**
     * Get all purchase receipts for this supplier.
     * جميع استلامات البضاعة للمورد
     *
     * @return HasMany
     */
    public function purchaseReceipts(): HasMany
    {
        return $this->hasMany(PurchaseReceipt::class, 'supplier_id');
    }

    /**
     * علاقة المورد بالتعاملات المالية (للتوافق مع النظام القديم).
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'supplier_id');
    }

    /**
     * علاقة المورد بالمستخدم الذي أنشأه (للتوافق مع النظام القديم).
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope a query to only include active suppliers.
     * تصفية الموردين النشطين فقط
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'active')
              ->orWhere('is_active', true);
        });
    }

    /**
     * Scope a query to search suppliers by name or code.
     * البحث في الموردين بالاسم أو الرمز
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('name_en', 'like', "%{$search}%");
        });
    }

    /**
     * Get the supplier's full display name.
     * الحصول على الاسم الكامل للمورد
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        if (!empty($this->code)) {
            return $this->code . ' - ' . $this->name;
        }
        return $this->name;
    }

    /**
     * Check if supplier has credit payment terms.
     * التحقق من أن المورد يعمل بنظام الآجل
     *
     * @return bool
     */
    public function hasCreditTerms(): bool
    {
        return $this->payment_terms === 'credit';
    }

    /**
     * Check if supplier is active.
     * التحقق من أن المورد نشط
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active' || $this->is_active === true;
    }

    /**
     * حساب الرصيد الحالي للمورد (للتوافق مع النظام القديم).
     * يتم استدعاؤها من الخدمة عند الحاجة.
     *
     * @return float
     */
    public function calculateCurrentBalance(): float
    {
        $transactionsSum = $this->transactions()->sum('amount');
        return $this->initial_balance + $transactionsSum;
    }
}

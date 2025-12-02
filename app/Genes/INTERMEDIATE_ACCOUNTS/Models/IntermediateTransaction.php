<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Model: IntermediateTransaction
 * 
 * موديل معاملات الحسابات الوسيطة
 * 
 * @property int $id
 * @property int $intermediate_account_id
 * @property string $type (receipt|payment)
 * @property float $amount
 * @property float $available_amount
 * @property string $reference_number
 * @property string $transaction_date
 * @property string $status
 * @property bool $is_transferred
 */
class IntermediateTransaction extends Model
{
    use SoftDeletes;

    protected $table = 'alabasi_intermediate_transactions';

    protected $fillable = [
        'intermediate_account_id',
        'type',
        'amount',
        'available_amount',
        'reference_number',
        'transaction_date',
        'description',
        'status',
        'is_transferred',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'available_amount' => 'decimal:2',
        'transaction_date' => 'date',
        'is_transferred' => 'boolean',
    ];

    /**
     * العلاقة مع الحساب الوسيط
     */
    public function intermediateAccount(): BelongsTo
    {
        return $this->belongsTo(IntermediateAccount::class);
    }

    /**
     * العلاقة مع روابط القبض (إذا كانت قبض)
     */
    public function receiptLinks(): HasMany
    {
        return $this->hasMany(TransactionLink::class, 'receipt_transaction_id');
    }

    /**
     * العلاقة مع روابط الدفع (إذا كانت دفع)
     */
    public function paymentLinks(): HasMany
    {
        return $this->hasMany(TransactionLink::class, 'payment_transaction_id');
    }

    /**
     * Scope: القبوضات فقط
     */
    public function scopeReceipts(Builder $query): Builder
    {
        return $query->where('type', 'receipt');
    }

    /**
     * Scope: المدفوعات فقط
     */
    public function scopePayments(Builder $query): Builder
    {
        return $query->where('type', 'payment');
    }

    /**
     * Scope: المعاملات المكتملة
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: المعاملات قيد الانتظار
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: المعاملات التي لم يتم ترحيلها
     */
    public function scopeNotTransferred(Builder $query): Builder
    {
        return $query->where('is_transferred', false);
    }

    /**
     * حساب المبلغ المربوط
     */
    public function getLinkedAmountAttribute(): float
    {
        if ($this->type === 'receipt') {
            return $this->receiptLinks()->sum('linked_amount');
        }
        
        return $this->paymentLinks()->sum('linked_amount');
    }

    /**
     * تحديث المبلغ المتاح
     */
    public function updateAvailableAmount(): void
    {
        $this->available_amount = $this->amount - $this->linked_amount;
        $this->save();
    }

    /**
     * هل المعاملة مربوطة بالكامل؟
     */
    public function isFullyLinked(): bool
    {
        return $this->available_amount <= 0;
    }
}

<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Model: TransactionLink
 * 
 * موديل ربط المعاملات (قبض ← → دفع)
 * 
 * @property int $id
 * @property int $receipt_transaction_id
 * @property int $payment_transaction_id
 * @property float $linked_amount
 * @property string $link_date
 */
class TransactionLink extends Model
{
    use SoftDeletes;

    protected $table = 'alabasi_transaction_links';

    protected $fillable = [
        'receipt_transaction_id',
        'payment_transaction_id',
        'linked_amount',
        'link_date',
    ];

    protected $casts = [
        'linked_amount' => 'decimal:2',
        'link_date' => 'date',
    ];

    /**
     * العلاقة مع معاملة القبض
     */
    public function receiptTransaction(): BelongsTo
    {
        return $this->belongsTo(IntermediateTransaction::class, 'receipt_transaction_id');
    }

    /**
     * العلاقة مع معاملة الدفع
     */
    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(IntermediateTransaction::class, 'payment_transaction_id');
    }

    /**
     * Scope: الروابط لحساب وسيط معين
     */
    public function scopeForAccount(Builder $query, int $accountId): Builder
    {
        return $query->whereHas('receiptTransaction', function ($q) use ($accountId) {
            $q->where('intermediate_account_id', $accountId);
        });
    }

    /**
     * Scope: الروابط في فترة معينة
     */
    public function scopeBetweenDates(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('link_date', [$from, $to]);
    }

    /**
     * حذف الربط وتحديث المبالغ المتاحة
     */
    public function deleteAndUpdateAmounts(): void
    {
        // تحديث المبلغ المتاح للقبض
        $this->receiptTransaction->available_amount += $this->linked_amount;
        $this->receiptTransaction->save();

        // تحديث المبلغ المتاح للدفع
        $this->paymentTransaction->available_amount += $this->linked_amount;
        $this->paymentTransaction->save();

        // حذف الربط
        $this->delete();
    }
}

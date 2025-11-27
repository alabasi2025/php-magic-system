<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Account;
use App\Models\User;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Model: IntermediateTransaction
 * 
 * 💡 الفكرة:
 * نموذج يمثل عملية (قبض أو صرف) في الحساب الوسيط.
 * كل عملية يمكن أن تُربط بعمليات معاكسة لإتمامها.
 * 
 * 🎯 الغرض:
 * - تسجيل جميع العمليات التي تدخل الحساب الوسيط
 * - تتبع حالة العملية (معلقة، مرتبطة، مرحّلة)
 * - ربط العمليات ببعضها البعض
 * 
 * 📊 الحالات:
 * - pending: معلقة (لم تُربط بعد)
 * - linked: مرتبطة (تم ربطها بعمليات معاكسة)
 * - transferred: مرحّلة (تم ترحيلها إلى الحساب الرئيسي)
 * 
 * @property int $id
 * @property int $intermediate_account_id
 * @property string $type (receipt|payment)
 * @property string $voucher_number
 * @property string $from_to
 * @property int|null $source_target_account_id
 * @property float $amount
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property string $description
 * @property string $status
 * @property bool $is_transferred
 * @property \Illuminate\Support\Carbon|null $transferred_at
 * @property int|null $transferred_by
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @version 1.0.0
 * @since 2025-11-27
 */
class IntermediateTransaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'intermediate_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'intermediate_account_id',
        'type',
        'voucher_number',
        'from_to',
        'source_target_account_id',
        'amount',
        'transaction_date',
        'description',
        'status',
        'is_transferred',
        'transferred_at',
        'transferred_by',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'is_transferred' => 'boolean',
        'transferred_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the intermediate account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function intermediateAccount(): BelongsTo
    {
        return $this->belongsTo(IntermediateAccount::class, 'intermediate_account_id');
    }

    /**
     * Get the source/target account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sourceTargetAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'source_target_account_id');
    }

    /**
     * Get the user who transferred this transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transferrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    /**
     * Get the user who created this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all links where this transaction is the source.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sourceLinks(): HasMany
    {
        return $this->hasMany(TransactionLink::class, 'source_transaction_id');
    }

    /**
     * Get all links where this transaction is the target.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function targetLinks(): HasMany
    {
        return $this->hasMany(TransactionLink::class, 'target_transaction_id');
    }

    /**
     * Get the total linked amount for this transaction.
     *
     * @return float
     */
    public function getLinkedAmount(): float
    {
        $sourceLinked = $this->sourceLinks()->sum('linked_amount');
        $targetLinked = $this->targetLinks()->sum('linked_amount');
        
        return $sourceLinked + $targetLinked;
    }

    /**
     * Get the remaining unlinked amount.
     *
     * @return float
     */
    public function getRemainingAmount(): float
    {
        return $this->amount - $this->getLinkedAmount();
    }

    /**
     * Check if this transaction is fully linked.
     *
     * @return bool
     */
    public function isFullyLinked(): bool
    {
        return $this->getRemainingAmount() == 0;
    }

    /**
     * Check if this transaction is partially linked.
     *
     * @return bool
     */
    public function isPartiallyLinked(): bool
    {
        $remaining = $this->getRemainingAmount();
        return $remaining > 0 && $remaining < $this->amount;
    }

    /**
     * Check if this transaction is pending (not linked).
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if this transaction is linked.
     *
     * @return bool
     */
    public function isLinked(): bool
    {
        return $this->status === 'linked';
    }

    /**
     * Check if this transaction is transferred.
     *
     * @return bool
     */
    public function isTransferred(): bool
    {
        return $this->status === 'transferred' || $this->is_transferred;
    }

    /**
     * Check if this is a receipt transaction.
     *
     * @return bool
     */
    public function isReceipt(): bool
    {
        return $this->type === 'receipt';
    }

    /**
     * Check if this is a payment transaction.
     *
     * @return bool
     */
    public function isPayment(): bool
    {
        return $this->type === 'payment';
    }

    /**
     * Mark this transaction as linked.
     *
     * @return bool
     */
    public function markAsLinked(): bool
    {
        return $this->update(['status' => 'linked']);
    }

    /**
     * Mark this transaction as transferred.
     *
     * @param int $userId
     * @return bool
     */
    public function markAsTransferred(int $userId): bool
    {
        return $this->update([
            'status' => 'transferred',
            'is_transferred' => true,
            'transferred_at' => now(),
            'transferred_by' => $userId,
        ]);
    }
}

<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Model: TransactionLink
 * 
 * 💡 الفكرة:
 * نموذج يمثل الربط بين عمليتين معاكستين في الحساب الوسيط.
 * يمكن ربط عملية واحدة بعدة عمليات، والعكس.
 * 
 * 🎯 القاعدة الذهبية:
 * مجموع المبالغ المرتبطة يجب أن يكون متساوياً تماماً
 * 
 * 📊 أنواع الربط:
 * - 1:1 - عملية واحدة ← عملية واحدة
 * - 1:N - عملية واحدة ← عدة عمليات
 * - N:1 - عدة عمليات ← عملية واحدة
 * - N:M - عدة عمليات ← عدة عمليات
 * 
 * @property int $id
 * @property int $source_transaction_id
 * @property int $target_transaction_id
 * @property float $linked_amount
 * @property \Illuminate\Support\Carbon $linked_at
 * @property int|null $linked_by
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @version 1.0.0
 * @since 2025-11-27
 */
class TransactionLink extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_links';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'source_transaction_id',
        'target_transaction_id',
        'linked_amount',
        'linked_at',
        'linked_by',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'linked_amount' => 'decimal:2',
        'linked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the source transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sourceTransaction(): BelongsTo
    {
        return $this->belongsTo(IntermediateTransaction::class, 'source_transaction_id');
    }

    /**
     * Get the target transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function targetTransaction(): BelongsTo
    {
        return $this->belongsTo(IntermediateTransaction::class, 'target_transaction_id');
    }

    /**
     * Get the user who linked these transactions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function linker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'linked_by');
    }

    /**
     * Check if this link is valid (source and target are opposite types).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $source = $this->sourceTransaction;
        $target = $this->targetTransaction;

        if (!$source || !$target) {
            return false;
        }

        // Source and target must be opposite types
        return ($source->isReceipt() && $target->isPayment()) ||
               ($source->isPayment() && $target->isReceipt());
    }

    /**
     * Get the link direction description.
     *
     * @return string
     */
    public function getDirectionDescription(): string
    {
        $source = $this->sourceTransaction;
        $target = $this->targetTransaction;

        if (!$source || !$target) {
            return 'غير معروف';
        }

        if ($source->isReceipt() && $target->isPayment()) {
            return 'قبض → صرف';
        }

        if ($source->isPayment() && $target->isReceipt()) {
            return 'صرف → قبض';
        }

        return 'غير صحيح';
    }
}

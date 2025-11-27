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
 * Model: IntermediateAccount
 * 
 * 💡 الفكرة:
 * نموذج يمثل الحساب الوسيط المرتبط بحساب رئيسي.
 * كل حساب رئيسي يمكن أن يكون له حتى 3 حسابات وسيطة.
 * 
 * 🎯 الغرض:
 * - ربط الحساب الرئيسي بحساباته الوسيطة
 * - إدارة حالة الحساب الوسيط (نشط/غير نشط)
 * - تتبع من أنشأ وعدّل الإعدادات
 * 
 * 📊 العلاقات:
 * - mainAccount: الحساب الرئيسي
 * - intermediateAccount1: الحساب الوسيط الأول (إلزامي)
 * - intermediateAccount2: الحساب الوسيط الثاني (اختياري)
 * - intermediateAccount3: الحساب الوسيط الثالث (اختياري)
 * - transactions: جميع العمليات في هذا الحساب الوسيط
 * 
 * @property int $id
 * @property int $main_account_id
 * @property int $intermediate_account_1_id
 * @property int|null $intermediate_account_2_id
 * @property int|null $intermediate_account_3_id
 * @property string $status
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
class IntermediateAccount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'intermediate_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'main_account_id',
        'intermediate_account_1_id',
        'intermediate_account_2_id',
        'intermediate_account_3_id',
        'status',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the main account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'main_account_id');
    }

    /**
     * Get the first intermediate account (required).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function intermediateAccount1(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'intermediate_account_1_id');
    }

    /**
     * Get the second intermediate account (optional).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function intermediateAccount2(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'intermediate_account_2_id');
    }

    /**
     * Get the third intermediate account (optional).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function intermediateAccount3(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'intermediate_account_3_id');
    }

    /**
     * Get all transactions for this intermediate account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(IntermediateTransaction::class, 'intermediate_account_id');
    }

    /**
     * Get pending transactions (not linked or transferred).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pendingTransactions(): HasMany
    {
        return $this->hasMany(IntermediateTransaction::class, 'intermediate_account_id')
            ->where('status', 'pending');
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
     * Get the current balance of the intermediate account.
     * 
     * 🎯 القاعدة الذهبية:
     * الرصيد يجب أن يكون = 0 عندما تكون جميع العمليات مرتبطة ومرحّلة
     *
     * @return float
     */
    public function getCurrentBalance(): float
    {
        $receipts = $this->transactions()
            ->where('type', 'receipt')
            ->where('is_transferred', false)
            ->sum('amount');

        $payments = $this->transactions()
            ->where('type', 'payment')
            ->where('is_transferred', false)
            ->sum('amount');

        return $receipts - $payments;
    }

    /**
     * Check if the intermediate account is balanced (balance = 0).
     *
     * @return bool
     */
    public function isBalanced(): bool
    {
        return $this->getCurrentBalance() == 0;
    }

    /**
     * Get count of unlinked transactions.
     *
     * @return int
     */
    public function getUnlinkedTransactionsCount(): int
    {
        return $this->transactions()
            ->where('status', 'pending')
            ->count();
    }

    /**
     * Check if this intermediate account is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Activate this intermediate account.
     *
     * @return bool
     */
    public function activate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    /**
     * Deactivate this intermediate account.
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => 'inactive']);
    }
}

<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Account;
use Illuminate\Database\Eloquent\Builder;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Model: IntermediateAccount
 * 
 * موديل الحساب الوسيط
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $main_account_id
 * @property bool $is_active
 * @property string|null $description
 */
class IntermediateAccount extends Model
{
    use SoftDeletes;

    protected $table = 'alabasi_intermediate_accounts';

    protected $fillable = [
        'name',
        'code',
        'main_account_id',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع الحساب الرئيسي
     */
    public function mainAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'main_account_id');
    }

    /**
     * العلاقة مع المعاملات
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(IntermediateTransaction::class);
    }

    /**
     * Scope: الحسابات النشطة فقط
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: البحث بالكود
     */
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    /**
     * حساب الرصيد من المعاملات
     */
    public function getBalanceAttribute(): float
    {
        $receipts = $this->transactions()->where('type', 'receipt')->sum('amount');
        $payments = $this->transactions()->where('type', 'payment')->sum('amount');
        
        return $receipts - $payments;
    }

    /**
     * إجمالي القبوضات
     */
    public function getTotalReceipts(): float
    {
        return $this->transactions()->where('type', 'receipt')->sum('amount');
    }

    /**
     * إجمالي المدفوعات
     */
    public function getTotalPayments(): float
    {
        return $this->transactions()->where('type', 'payment')->sum('amount');
    }
}

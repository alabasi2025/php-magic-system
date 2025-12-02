<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Model: GeneralIntermediateAccount
 * 
 * موديل الحسابات الوسيطة العامة
 * (حسابات وسيطة غير مرتبطة بحساب رئيسي محدد)
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * @property float $balance
 * @property string|null $description
 */
class GeneralIntermediateAccount extends Model
{
    use SoftDeletes;

    protected $table = 'alabasi_general_intermediate_accounts';

    protected $fillable = [
        'name',
        'code',
        'balance',
        'description',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Scope: البحث بالكود
     */
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    /**
     * Scope: الحسابات ذات الرصيد الموجب
     */
    public function scopeWithPositiveBalance(Builder $query): Builder
    {
        return $query->where('balance', '>', 0);
    }

    /**
     * Scope: الحسابات ذات الرصيد السالب
     */
    public function scopeWithNegativeBalance(Builder $query): Builder
    {
        return $query->where('balance', '<', 0);
    }

    /**
     * إضافة مبلغ للرصيد
     */
    public function addToBalance(float $amount): void
    {
        $this->balance += $amount;
        $this->save();
    }

    /**
     * خصم مبلغ من الرصيد
     */
    public function subtractFromBalance(float $amount): void
    {
        $this->balance -= $amount;
        $this->save();
    }

    /**
     * إعادة تعيين الرصيد
     */
    public function resetBalance(): void
    {
        $this->balance = 0;
        $this->save();
    }
}

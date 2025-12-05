<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string|null $contact_person
 * @property string $phone
 * @property string|null $email
 * @property string|null $address
 * @property float $initial_balance
 * @property float $balance
 * @property bool $is_active
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read \App\Models\User $creator
 */
class Supplier extends Model
{
    use HasFactory;

    // الحقول المسموح بتعبئتها جماعياً
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'initial_balance',
        'balance', // يمكن تحديثه من خلال الخدمة فقط
        'is_active',
        'user_id',
    ];

    // تحويل الحقول إلى أنواع بيانات محددة
    protected $casts = [
        'initial_balance' => 'decimal:2',
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * علاقة المورد بالتعاملات المالية (المشتريات، المدفوعات).
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        // نفترض وجود نموذج Transaction يربط بالكيانات المالية
        return $this->hasMany(Transaction::class, 'supplier_id');
    }

    /**
     * علاقة المورد بالمستخدم الذي أنشأه.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * حساب الرصيد الحالي للمورد.
     * يتم استدعاؤها من الخدمة عند الحاجة.
     *
     * @return float
     */
    public function calculateCurrentBalance(): float
    {
        // الرصيد الافتتاحي + مجموع التعاملات (يجب أن يكون منطق التعاملات في الخدمة)
        // نفترض أن قيمة التعاملات موجبة تعني دين على الشركة (مستحق للمورد) وسالبة تعني دفع للمورد.
        $transactionsSum = $this->transactions()->sum('amount');
        return $this->initial_balance + $transactionsSum;
    }
}

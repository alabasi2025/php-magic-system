<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $number رقم إذن الإخراج
 * @property int $warehouse_id معرف المخزن
 * @property int $customer_id معرف العميل
 * @property string $date تاريخ الإخراج
 * @property string|null $reference مرجع خارجي (مثل رقم فاتورة)
 * @property string|null $notes ملاحظات
 * @property float $total_amount إجمالي المبلغ
 * @property string $status حالة الإذن (مثل: معلق، مكتمل، ملغي)
 * @property int $created_by معرف المستخدم الذي أنشأ الإذن
 */
class StockOut extends Model
{
    use HasFactory;

    // تحديد الحقول التي يمكن تعبئتها جماعياً
    protected $fillable = [
        'number',
        'warehouse_id',
        'customer_id',
        'date',
        'reference',
        'notes',
        'total_amount',
        'status',
        'created_by',
    ];

    // تحويل حقل التاريخ إلى كائن تاريخ عند القراءة
    protected $casts = [
        'date' => 'date',
        'total_amount' => 'float',
    ];

    /**
     * علاقة: تفاصيل إذن الإخراج.
     * @return HasMany
     */
    public function details(): HasMany
    {
        // إذن الإخراج يحتوي على عدة تفاصيل (بنود)
        return $this->hasMany(StockOutDetail::class);
    }

    /**
     * علاقة: المخزن الذي تم منه الإخراج.
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        // إذن الإخراج ينتمي إلى مخزن واحد
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * علاقة: العميل الذي تم الإخراج له.
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        // إذن الإخراج مرتبط بعميل واحد
        return $this->belongsTo(Customer::class);
    }

    /**
     * علاقة: المستخدم الذي أنشأ إذن الإخراج.
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        // إذن الإخراج تم إنشاؤه بواسطة مستخدم واحد
        return $this->belongsTo(User::class, 'created_by');
    }
}

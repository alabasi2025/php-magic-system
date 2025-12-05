<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $number رقم عملية الجرد
 * @property int $warehouse_id المخزن الذي تم جرده
 * @property string $date تاريخ الجرد
 * @property string $status حالة الجرد
 * @property string|null $notes ملاحظات عامة
 * @property int $created_by المستخدم الذي أنشأ الجرد
 * @property int|null $approved_by المستخدم الذي وافق على الجرد
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Warehouse $warehouse
 * @property-read \App\Models\User $creator
 * @property-read \App\Models\User|null $approver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockCountDetail> $details
 */
class StockCount extends Model
{
    use HasFactory;

    // تحديد الحقول التي يمكن تعبئتها جماعياً
    protected $fillable = [
        'number',
        'warehouse_id',
        'date',
        'status',
        'notes',
        'created_by',
        'approved_by',
    ];

    // تحويل الحقول
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * علاقة: الجرد ينتمي إلى مخزن واحد.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * علاقة: الجرد أنشئ بواسطة مستخدم واحد.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * علاقة: الجرد تمت الموافقة عليه بواسطة مستخدم واحد (قد يكون فارغاً).
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * علاقة: الجرد يحتوي على عدة تفاصيل (بنود).
     */
    public function details(): HasMany
    {
        return $this->hasMany(StockCountDetail::class);
    }

    /**
     * نطاق للاستعلام عن الجرود المكتملة.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }
}

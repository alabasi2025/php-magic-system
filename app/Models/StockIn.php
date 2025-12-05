<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockIn extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بتعبئتها جماعياً.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'warehouse_id',
        'supplier_id',
        'date',
        'reference',
        'notes',
        'total_amount',
        'status',
        'created_by',
    ];

    /**
     * تحويل بعض الحقول إلى أنواع بيانات محددة.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * علاقة: إذن الإدخال ينتمي إلى مخزن واحد.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * علاقة: إذن الإدخال ينتمي إلى مورد واحد.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * علاقة: إذن الإدخال أنشئ بواسطة مستخدم واحد.
     */
    public function createdBy(): BelongsTo
    {
        // نفترض وجود نموذج User
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * علاقة: إذن الإدخال يحتوي على عدة تفاصيل (أصناف).
     */
    public function details(): HasMany
    {
        return $this->hasMany(StockInDetail::class);
    }
}

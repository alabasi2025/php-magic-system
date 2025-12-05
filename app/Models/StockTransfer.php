<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;
use App\Models\User;

/**
 * نموذج تحويل المخزون
 * يمثل طلب نقل البضائع من مخزن إلى آخر.
 */
class StockTransfer extends Model
{
    use HasFactory;

    // الحقول المسموح بتعبئتها
    protected $fillable = [
        'number',
        'from_warehouse_id',
        'to_warehouse_id',
        'date',
        'reference',
        'notes',
        'status', // مثال: 'pending', 'approved', 'rejected', 'completed'
        'created_by',
        'approved_by',
    ];

    // تحويلات الحقول
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * علاقة تفاصيل التحويل.
     * التحويل يحتوي على عدة تفاصيل (مواد).
     */
    public function details()
    {
        return $this->hasMany(StockTransferDetail::class);
    }

    /**
     * علاقة المخزن المصدر (من).
     */
    public function fromWarehouse()
    {
        // نفترض وجود نموذج Warehouse
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    /**
     * علاقة المخزن المستقبل (إلى).
     */
    public function toWarehouse()
    {
        // نفترض وجود نموذج Warehouse
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    /**
     * علاقة المستخدم الذي أنشأ التحويل.
     */
    public function creator()
    {
        // نفترض وجود نموذج User
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * علاقة المستخدم الذي وافق على التحويل.
     */
    public function approver()
    {
        // نفترض وجود نموذج User
        return $this->belongsTo(User::class, 'approved_by');
    }
}

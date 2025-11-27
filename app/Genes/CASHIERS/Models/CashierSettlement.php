<?php

namespace App\Genes\CASHIERS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * نموذج تسوية الصراف (Cashier Settlement)
 * 
 * يمثل التسوية اليومية أو الدورية للصراف
 */
class CashierSettlement extends Model
{
    use SoftDeletes;

    protected $table = 'cashier_settlements';

    protected $fillable = [
        'code',
        'cashier_id',
        'entity_id',
        'settlement_date',
        'opening_balance',
        'total_deposits',
        'total_withdrawals',
        'expected_balance',
        'actual_balance',
        'difference',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'status', // pending, approved, rejected
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'total_deposits' => 'decimal:2',
        'total_withdrawals' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'actual_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'settlement_date' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * العلاقة مع الصراف
     */
    public function cashier()
    {
        return $this->belongsTo(Cashier::class);
    }

    /**
     * العلاقة مع الكيان
     */
    public function entity()
    {
        return $this->belongsTo(\App\Models\Entity::class);
    }

    /**
     * العلاقة مع المنشئ
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * العلاقة مع المعتمد
     */
    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    /**
     * Scope: حسب الحالة
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: حسب الصراف
     */
    public function scopeByCashier($query, $cashierId)
    {
        return $query->where('cashier_id', $cashierId);
    }

    /**
     * Scope: حسب الفترة الزمنية
     */
    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('settlement_date', [$from, $to]);
    }

    /**
     * حساب الفرق تلقائياً
     */
    public function calculateDifference()
    {
        $this->difference = $this->actual_balance - $this->expected_balance;
        return $this;
    }
}

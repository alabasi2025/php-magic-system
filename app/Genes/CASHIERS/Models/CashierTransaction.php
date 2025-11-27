<?php

namespace App\Genes\CASHIERS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * نموذج معاملة الصراف (Cashier Transaction)
 * 
 * يمثل معاملة مالية تتم عبر الصراف (سحب، إيداع، تحويل)
 */
class CashierTransaction extends Model
{
    use SoftDeletes;

    protected $table = 'cashier_transactions';

    protected $fillable = [
        'code',
        'cashier_id',
        'entity_id',
        'transaction_type', // deposit, withdrawal, transfer
        'amount',
        'currency_id',
        'exchange_rate',
        'amount_in_base_currency',
        'reference_type', // invoice, payment, receipt, etc.
        'reference_id',
        'customer_id',
        'supplier_id',
        'description',
        'notes',
        'transaction_date',
        'created_by',
        'approved_by',
        'approved_at',
        'status', // pending, approved, rejected, cancelled
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_in_base_currency' => 'decimal:2',
        'transaction_date' => 'datetime',
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
     * العلاقة مع العملة
     */
    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }

    /**
     * العلاقة مع العميل
     */
    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    /**
     * العلاقة مع المورد
     */
    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class);
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
     * Scope: حسب نوع المعاملة
     */
    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
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
        return $query->whereBetween('transaction_date', [$from, $to]);
    }
}

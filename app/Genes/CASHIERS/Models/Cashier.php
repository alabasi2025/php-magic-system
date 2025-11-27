<?php

namespace App\Genes\CASHIERS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * نموذج الصراف (Cashier)
 * 
 * يمثل الصراف في النظام مع معلوماته الأساسية والخزينة المرتبطة به
 */
class Cashier extends Model
{
    use SoftDeletes;

    protected $table = 'cashiers';

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'entity_id',
        'branch_id',
        'user_id',
        'safe_id',
        'opening_balance',
        'current_balance',
        'status',
        'max_transaction_limit',
        'daily_limit',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'max_transaction_limit' => 'decimal:2',
        'daily_limit' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * العلاقة مع الكيان (Entity)
     */
    public function entity()
    {
        return $this->belongsTo(\App\Models\Entity::class);
    }

    /**
     * العلاقة مع الفرع (Branch)
     */
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    /**
     * العلاقة مع المستخدم (User)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * العلاقة مع الخزينة (Safe)
     */
    public function safe()
    {
        return $this->belongsTo(\App\Models\Safe::class);
    }

    /**
     * العلاقة مع المعاملات (Transactions)
     */
    public function transactions()
    {
        return $this->hasMany(CashierTransaction::class);
    }

    /**
     * العلاقة مع التسويات (Settlements)
     */
    public function settlements()
    {
        return $this->hasMany(CashierSettlement::class);
    }

    /**
     * Scope: الصرافين النشطين فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: حسب الكيان
     */
    public function scopeByEntity($query, $entityId)
    {
        return $query->where('entity_id', $entityId);
    }

    /**
     * Scope: حسب الفرع
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}

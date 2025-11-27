<?php

namespace App\Genes\WALLETS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * نموذج المحفظة (Wallet)
 * 
 * يمثل المحفظة الإلكترونية للعميل أو المورد
 */
class Wallet extends Model
{
    use SoftDeletes;

    protected $table = 'wallets';

    protected $fillable = [
        'code',
        'owner_type', // customer, supplier, employee, user
        'owner_id',
        'entity_id',
        'wallet_type', // personal, business, savings
        'currency_id',
        'balance',
        'available_balance',
        'reserved_balance',
        'credit_limit',
        'status', // active, inactive, suspended, blocked
        'is_active',
        'notes',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'reserved_balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * العلاقة مع المالك (Polymorphic)
     */
    public function owner()
    {
        return $this->morphTo();
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
     * العلاقة مع المعاملات
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Scope: المحافظ النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope: حسب نوع المالك
     */
    public function scopeByOwnerType($query, $type)
    {
        return $query->where('owner_type', $type);
    }

    /**
     * Scope: حسب الكيان
     */
    public function scopeByEntity($query, $entityId)
    {
        return $query->where('entity_id', $entityId);
    }

    /**
     * تحديث الرصيد المتاح
     */
    public function updateAvailableBalance()
    {
        $this->available_balance = $this->balance - $this->reserved_balance;
        $this->save();
        return $this;
    }

    /**
     * التحقق من كفاية الرصيد
     */
    public function hasSufficientBalance($amount)
    {
        return $this->available_balance >= $amount;
    }
}

<?php

namespace App\Genes\WALLETS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * نموذج تحويل بين المحافظ (Wallet Transfer)
 * 
 * يمثل عملية تحويل أموال بين محفظتين
 */
class WalletTransfer extends Model
{
    use SoftDeletes;

    protected $table = 'wallet_transfers';

    protected $fillable = [
        'code',
        'from_wallet_id',
        'to_wallet_id',
        'entity_id',
        'amount',
        'exchange_rate',
        'amount_received',
        'fees',
        'description',
        'notes',
        'transfer_date',
        'created_by',
        'approved_by',
        'approved_at',
        'status', // pending, completed, failed, cancelled
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_received' => 'decimal:2',
        'fees' => 'decimal:2',
        'transfer_date' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * العلاقة مع المحفظة المرسلة
     */
    public function fromWallet()
    {
        return $this->belongsTo(Wallet::class, 'from_wallet_id');
    }

    /**
     * العلاقة مع المحفظة المستقبلة
     */
    public function toWallet()
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id');
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
     * Scope: حسب المحفظة (مرسلة أو مستقبلة)
     */
    public function scopeByWallet($query, $walletId)
    {
        return $query->where(function ($q) use ($walletId) {
            $q->where('from_wallet_id', $walletId)
              ->orWhere('to_wallet_id', $walletId);
        });
    }

    /**
     * Scope: حسب الفترة الزمنية
     */
    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('transfer_date', [$from, $to]);
    }

    /**
     * حساب المبلغ المستلم
     */
    public function calculateAmountReceived()
    {
        $this->amount_received = ($this->amount * $this->exchange_rate) - $this->fees;
        return $this;
    }
}

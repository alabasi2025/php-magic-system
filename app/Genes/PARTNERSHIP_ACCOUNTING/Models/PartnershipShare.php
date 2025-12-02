<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
// افتراض وجود هذه الموديلات في نفس الجين أو مسار يمكن الوصول إليه
// بما أننا لا نستطيع فحص المستودع، سنفترض وجودها لتعريف العلاقات
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\Partner;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\PartnershipAccount;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\Transaction;

class PartnershipShare extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'alabasi_partnership_shares';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'partner_id',
        'partnership_account_id',
        'share_percentage',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'share_percentage' => 'decimal:4',
    ];

    // --- Relationships (العلاقات) ---

    /**
     * Get the partner that owns the share. (BelongsTo)
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the partnership account associated with the share. (BelongsTo)
     */
    public function partnershipAccount(): BelongsTo
    {
        return $this->belongsTo(PartnershipAccount::class);
    }

    /**
     * Get the transactions related to this share. (HasMany)
     */
    public function transactions(): HasMany
    {
        // افتراض أن جدول المعاملات (transactions) يحتوي على foreign key يشير إلى partnership_share_id
        return $this->hasMany(Transaction::class, 'partnership_share_id');
    }

    // --- Scopes (النطاقات) ---

    /**
     * Scope a query to include only active shares (e.g., share percentage > 0).
     */
    public function scopeActive($query)
    {
        return $query->where('share_percentage', '>', 0);
    }

    /**
     * Scope a query to only include shares for a given partner.
     */
    public function scopeOfPartner($query, int $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    // --- Accessors (الوصول) ---

    /**
     * Get the share percentage formatted as a string (e.g., "15.50%").
     *
     * @return string
     */
    protected function getFormattedSharePercentageAttribute(): string
    {
        return number_format($this->share_percentage, 2) . '%';
    }
}

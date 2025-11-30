<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User; // Assuming User model is in App\Models

class ProfitDistribution extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'profit_distributions';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'distribution_date' => 'date',
        'amount' => 'decimal:4',
        'is_completed' => 'boolean',
    ];

    // ========================================================================
    //  RELATIONSHIPS (4 Relations)
    // ========================================================================

    /**
     * Get the partner who received the profit distribution.
     */
    public function partner(): BelongsTo
    {
        // Assuming Partner model is in the same Gene for simplicity
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    /**
     * Get the partnership account from which the profit was distributed.
     */
    public function partnershipAccount(): BelongsTo
    {
        // Assuming PartnershipAccount model is in the same Gene for simplicity
        return $this->belongsTo(PartnershipAccount::class, 'partnership_account_id');
    }

    /**
     * Get the user who recorded the distribution.
     */
    public function distributedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'distributed_by_user_id');
    }

    /**
     * Get the related accounting transactions (e.g., journal entries).
     */
    public function transactions(): HasMany
    {
        // Assuming Transaction model is in a common location
        return $this->hasMany(Transaction::class, 'source_id')->where('source_type', self::class);
    }

    // ========================================================================
    //  SCOPES
    // ========================================================================

    /**
     * Scope a query to only include profit distributions for a specific partner.
     */
    public function scopeForPartner($query, int $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    /**
     * Scope a query to only include profit distributions from a specific partnership account.
     */
    public function scopeForAccount($query, int $accountId)
    {
        return $query->where('partnership_account_id', $accountId);
    }

    /**
     * Scope a query to only include completed profit distributions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    // ========================================================================
    //  ACCESSORS
    // ========================================================================

    /**
     * Get the formatted distribution amount.
     */
    protected function getFormattedAmountAttribute(): string
    {
        // Assuming a currency format function exists globally or via a helper
        return number_format($this->amount, 2) . ' SAR';
    }

    /**
     * Get the formatted distribution date.
     */
    protected function getDistributionDateFormattedAttribute(): string
    {
        return $this->distribution_date?->format('Y-m-d') ?? 'N/A';
    }
}

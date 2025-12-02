<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Model: ProfitCalculation
 * Table: profit_calculations
 * الوصف: حساب الأرباح
 */
class ProfitCalculation extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'alabasi_profit_calculations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'partner_id',
        'calculation_date',
        'total_profit',
        'calculation_details',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'calculation_date' => 'date',
        'total_profit' => 'decimal:2',
        'calculation_details' => 'array',
        'deleted_at' => 'datetime',
    ];

    // --- Relations (العلاقات) ---

    /**
     * Get the partner that owns the profit calculation.
     * (BelongsTo)
     */
    public function partner(): BelongsTo
    {
        // افتراض وجود نموذج Partner في نفس المسار
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the distributions for the profit calculation.
     * (HasMany)
     */
    public function distributions(): HasMany
    {
        // افتراض وجود نموذج ProfitDistribution في نفس المسار
        return $this->hasMany(ProfitDistribution::class);
    }

    // --- Scopes (النطاقات) ---

    /**
     * Scope a query to include only recent calculations (e.g., last 30 days).
     */
    public function scopeRecent($query)
    {
        return $query->where('calculation_date', '>=', now()->subDays(30));
    }

    // --- Accessors (للوصول إلى البيانات المنسقة) ---

    /**
     * Get the formatted total profit.
     */
    protected function formattedProfit(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => number_format($attributes['total_profit'], 2) . ' SAR',
        );
    }
}

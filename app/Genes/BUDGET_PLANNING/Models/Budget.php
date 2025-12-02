<?php

namespace App\Genes\BUDGET_PLANNING\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

/**
 * الميزانية
 * 
 * نموذج لتمثيل الميزانيات السنوية والتخطيط المالي
 */
class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'fiscal_year',
        'start_date',
        'end_date',
        'total_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقة مع بنود الميزانية
     */
    public function items(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    /**
     * العلاقة مع المستخدم الذي أنشأ الميزانية
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الحصول على الميزانيات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * الحصول على الميزانيات حسب السنة المالية
     */
    public function scopeForFiscalYear($query, string $year)
    {
        return $query->where('fiscal_year', $year);
    }

    /**
     * حساب إجمالي المبلغ المخطط
     */
    public function getPlannedTotalAttribute(): float
    {
        return $this->items()->sum('planned_amount');
    }

    /**
     * حساب إجمالي المبلغ الفعلي
     */
    public function getActualTotalAttribute(): float
    {
        return $this->items()->sum('actual_amount');
    }

    /**
     * حساب إجمالي الفرق
     */
    public function getVarianceTotalAttribute(): float
    {
        return $this->items()->sum('variance');
    }

    /**
     * نسبة التنفيذ
     */
    public function getExecutionRateAttribute(): float
    {
        if ($this->planned_total == 0) {
            return 0;
        }

        return ($this->actual_total / $this->planned_total) * 100;
    }

    /**
     * التحقق من صلاحية الميزانية
     */
    public function isValid(): bool
    {
        return $this->status === 'active' && 
               $this->end_date >= now();
    }
}

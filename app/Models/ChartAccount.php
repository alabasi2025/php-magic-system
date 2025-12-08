<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\OptimizedQueries;

/**
 * ChartAccount Model
 * الحسابات المحاسبية
 * 
 * @package App\Models
 * @property int $id
 * @property int $chart_group_id
 * @property int|null $parent_id
 * @property int $level
 * @property bool $is_parent
 * @property string $code
 * @property string $name
 * @property string|null $name_en
 * @property string $account_type
 * @property string|null $description
 * @property float $balance
 * @property float $debit_balance
 * @property float $credit_balance
 * @property bool $is_active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class ChartAccount extends Model
{
    use HasFactory, SoftDeletes, OptimizedQueries;

    /**
     * Default relations to eager load
     * 
     * @var array
     */
    protected $defaultRelations = ['chartGroup', 'parent'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chart_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chart_group_id',
        'account_group_id',
        'parent_id',
        'level',
        'is_parent',
        'code',
        'name',
        'name_en',
        'account_type',
        'intermediate_for',
        'is_linked',
        'linked_entity_id',
        'linked_entity_type',
        'description',
        'balance',
        'debit_balance',
        'credit_balance',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level' => 'integer',
        'is_parent' => 'boolean',
        'is_linked' => 'boolean',
        'balance' => 'decimal:2',
        'debit_balance' => 'decimal:2',
        'credit_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the chart group that owns the account.
     */
    public function chartGroup(): BelongsTo
    {
        return $this->belongsTo(ChartGroup::class);
    }

    /**
     * Get the parent account.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'parent_id');
    }

    /**
     * Get the children accounts.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ChartAccount::class, 'parent_id')->orderBy('code');
    }

    /**
     * Get all descendants recursively.
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Scope a query to only include active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للحسابات حسب المجموعة
     */
    public function scopeByChartGroup($query, $chartGroupId)
    {
        return $query->where('chart_group_id', $chartGroupId);
    }

    /**
     * Scope للحسابات حسب النوع
     */
    public function scopeByType($query, $accountType)
    {
        return $query->where('account_type', $accountType);
    }

    /**
     * Scope للحسابات النشطة حسب المجموعة
     */
    public function scopeActiveByChartGroup($query, $chartGroupId)
    {
        return $query->where('chart_group_id', $chartGroupId)
                     ->where('is_active', true);
    }

    /**
     * Scope للحسابات النشطة حسب النوع
     */
    public function scopeActiveByType($query, $accountType)
    {
        return $query->where('account_type', $accountType)
                     ->where('is_active', true);
    }

    /**
     * Scope a query to only include root accounts.
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to only include parent accounts.
     */
    public function scopeParents($query)
    {
        return $query->where('is_parent', true);
    }

    /**
     * Scope a query to only include leaf accounts (no children).
     */
    public function scopeLeaves($query)
    {
        return $query->where('is_parent', false);
    }

    /**
     * Get the account type label in Arabic.
     */
    public function getAccountTypeLabelAttribute(): string
    {
        return match($this->account_type) {
            'general' => 'عام',
            'cash_box' => 'صندوق',
            'bank' => 'بنك',
            'wallet' => 'محفظة',
            'atm' => 'صراف',
            'intermediate' => 'حساب وسيط',
            'customer' => 'عميل',
            'supplier' => 'مورد',
            'employee' => 'موظف',
            'expense' => 'مصروف',
            'revenue' => 'إيراد',
            'asset' => 'أصل',
            'liability' => 'التزام',
            'equity' => 'حقوق ملكية',
            default => $this->account_type,
        };
    }

    /**
     * Get the intermediate for label in Arabic.
     */
    public function getIntermediateForLabelAttribute(): ?string
    {
        if (!$this->intermediate_for) {
            return null;
        }

        return match($this->intermediate_for) {
            'cash_boxes' => 'للصناديق',
            'banks' => 'للبنوك',
            'wallets' => 'للمحافظ',
            'atms' => 'للصرافات',
            'customers' => 'للعملاء',
            'suppliers' => 'للموردين',
            'employees' => 'للموظفين',
            default => $this->intermediate_for,
        };
    }

    /**
     * Scope للحسابات الوسيطة المتاحة (غير المرتبطة)
     */
    public function scopeAvailableIntermediates($query, $forType = null)
    {
        $query = $query->where('account_type', 'intermediate')
                       ->where('is_linked', false)
                       ->where('is_active', true);
        
        if ($forType) {
            $query->where('intermediate_for', $forType);
        }
        
        return $query;
    }

    /**
     * ربط الحساب الوسيط بكيان
     */
    public function linkToEntity($entityId, $entityType): void
    {
        $this->update([
            'is_linked' => true,
            'linked_entity_id' => $entityId,
            'linked_entity_type' => $entityType,
        ]);
    }

    /**
     * فك ربط الحساب الوسيط
     */
    public function unlinkFromEntity(): void
    {
        $this->update([
            'is_linked' => false,
            'linked_entity_id' => null,
            'linked_entity_type' => null,
        ]);
    }

    /**
     * Get the full account code (including parent codes).
     */
    public function getFullCodeAttribute(): string
    {
        $codes = [$this->code];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($codes, $parent->code);
            $parent = $parent->parent;
        }
        
        return implode('.', $codes);
    }

    /**
     * Get the indented name based on level.
     */
    public function getIndentedNameAttribute(): string
    {
        return str_repeat('— ', $this->level - 1) . $this->name;
    }

    /**
     * Check if the account has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Update the is_parent flag based on children existence.
     */
    public function updateIsParent(): void
    {
        $this->is_parent = $this->hasChildren();
        $this->save();
    }

    /**
     * Get the balance formatted.
     */
    public function getBalanceFormattedAttribute(): string
    {
        return number_format($this->balance, 2);
    }

    /**
     * Get the linked cash box (if intermediate_for = 'cash_boxes')
     */
    public function cashBox()
    {
        return $this->hasOne(CashBox::class, 'intermediate_account_id');
    }

    /**
     * Get the linked bank (if intermediate_for = 'banks')
     */
    public function bank()
    {
        // Will be implemented when Bank model is created
        return null;
    }

    /**
     * Get the linked wallet (if intermediate_for = 'wallets')
     */
    public function wallet()
    {
        // Will be implemented when Wallet model is created
        return null;
    }

    /**
     * Get the linked ATM (if intermediate_for = 'atms')
     */
    public function atm()
    {
        // Will be implemented when ATM model is created
        return null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ChartOfAccount Model - دليل الحسابات
 * 
 * نظام دليل الحسابات الهرمي المرتبط بالوحدة
 * يدعم الحسابات الرئيسية والفرعية مع العملات المتعددة
 * 
 * @package App\Models
 */
class ChartOfAccount extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chart_of_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unit_id',
        'parent_id',
        'code',
        'name',
        'name_en',
        'description',
        'account_level',
        'account_type',
        'analytical_type',
        'preferred_currencies',
        'is_active',
        'is_root',
        'level',
        'full_code',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    /**
     * تكوين Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'name', 'name_en', 'account_type', 'account_level', 'analytical_type', 'preferred_currencies', 'unit_id', 'parent_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "تم {$eventName} الحساب");
    }

    protected $casts = [
        'preferred_currencies' => 'array',
        'is_active' => 'boolean',
        'is_root' => 'boolean',
        'level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * الوحدة التابع لها الدليل
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * الحساب الأب (الرئيسي)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    /**
     * الحسابات الفرعية
     */
    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    /**
     * جميع الحسابات الفرعية (بشكل متداخل)
     */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    /**
     * من أنشأ
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * من عدّل
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope للحسابات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للحسابات الرئيسية
     */
    public function scopeParentAccounts($query)
    {
        return $query->where('account_level', 'parent');
    }

    /**
     * Scope للحسابات الفرعية
     */
    public function scopeSubAccounts($query)
    {
        return $query->where('account_level', 'sub');
    }

    /**
     * Scope للحسابات حسب النوع المحاسبي
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    /**
     * Scope للحسابات حسب النوع التحليلي
     */
    public function scopeOfAnalyticalType($query, $type)
    {
        return $query->where('analytical_type', $type);
    }

    /**
     * Scope للحسابات الجذرية
     */
    public function scopeRootAccounts($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope للحسابات حسب الوحدة
     */
    public function scopeForUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * الاسم الكامل مع الكود
     */
    public function getFullNameAttribute(): string
    {
        return $this->code . ' - ' . $this->name;
    }

    /**
     * هل الحساب رئيسي؟
     */
    public function getIsParentAttribute(): bool
    {
        return $this->account_level === 'parent';
    }

    /**
     * هل الحساب فرعي؟
     */
    public function getIsSubAttribute(): bool
    {
        return $this->account_level === 'sub';
    }

    /**
     * هل الحساب له حسابات فرعية؟
     */
    public function getHasChildrenAttribute(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * عدد الحسابات الفرعية
     */
    public function getChildrenCountAttribute(): int
    {
        return $this->children()->count();
    }

    /**
     * الترجمة العربية لنوع الحساب المحاسبي
     */
    public function getAccountTypeNameAttribute(): ?string
    {
        $types = [
            'asset' => 'أصول',
            'liability' => 'خصوم',
            'equity' => 'حقوق ملكية',
            'revenue' => 'إيرادات',
            'expense' => 'مصروفات',
        ];

        return $types[$this->account_type] ?? null;
    }

    /**
     * الترجمة العربية لنوع الحساب التحليلي
     */
    public function getAnalyticalTypeNameAttribute(): ?string
    {
        $types = [
            'cash_box' => 'صندوق',
            'bank' => 'بنك',
            'cashier' => 'صراف',
            'wallet' => 'محفظة',
            'customer' => 'عميل',
            'supplier' => 'مورد',
            'warehouse' => 'مخزن',
            'employee' => 'موظف',
            'partner' => 'شريك',
            'other' => 'أخرى',
        ];

        return $types[$this->analytical_type] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */

    /**
     * بناء الكود الكامل للحساب
     */
    public function buildFullCode(): string
    {
        if (!$this->parent_id) {
            return $this->code;
        }

        $parent = $this->parent;
        $codes = [$this->code];

        while ($parent) {
            array_unshift($codes, $parent->code);
            $parent = $parent->parent;
        }

        return implode('.', $codes);
    }

    /**
     * حساب مستوى الحساب في الشجرة
     */
    public function calculateLevel(): int
    {
        if (!$this->parent_id) {
            return 1;
        }

        $level = 1;
        $parent = $this->parent;

        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }

        return $level;
    }

    /**
     * التحقق من إمكانية حذف الحساب
     */
    public function canBeDeleted(): bool
    {
        // لا يمكن حذف الحساب إذا كان له حسابات فرعية
        if ($this->has_children) {
            return false;
        }

        // لا يمكن حذف الحساب إذا كان حساب جذر
        if ($this->is_root) {
            return false;
        }

        // TODO: التحقق من عدم وجود حسابات تحليلية مرتبطة
        // TODO: التحقق من عدم وجود حركات مالية

        return true;
    }

    /**
     * التحقق من إمكانية تغيير نوع الحساب
     */
    public function canChangeLevel(): bool
    {
        // لا يمكن تغيير نوع الحساب إذا كان له حسابات فرعية
        if ($this->has_children) {
            return false;
        }

        // TODO: التحقق من عدم وجود حسابات تحليلية مرتبطة

        return true;
    }

    /**
     * Get the attributes that should be searchable.
     *
     * @return array<int, string>
     */
    public function getSearchableAttributes(): array
    {
        return ['code', 'name', 'name_en', 'description'];
    }
}

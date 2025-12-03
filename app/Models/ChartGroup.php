<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ChartGroup Model
 * مجموعات الأدلة المحاسبية
 * 
 * @package App\Models
 * @property int $id
 * @property int $unit_id
 * @property string $code
 * @property string $name
 * @property string|null $name_en
 * @property string $type
 * @property string|null $description
 * @property string|null $icon
 * @property string|null $color
 * @property bool $is_active
 * @property int $sort_order
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class ChartGroup extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chart_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unit_id',
        'code',
        'name',
        'name_en',
        'type',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the unit that owns the chart group.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the accounts for the chart group.
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(ChartAccount::class);
    }

    /**
     * Get the root accounts (level 1) for the chart group.
     */
    public function rootAccounts(): HasMany
    {
        return $this->hasMany(ChartAccount::class)->whereNull('parent_id')->orderBy('code');
    }

    /**
     * Get active accounts only.
     */
    public function activeAccounts(): HasMany
    {
        return $this->hasMany(ChartAccount::class)->where('is_active', true);
    }

    /**
     * Scope a query to only include active chart groups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the type label in Arabic.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'payroll' => 'أعمال الموظفين',
            'final_accounts' => 'الحسابات النهائية',
            'assets' => 'الأصول',
            'budget' => 'الميزانية',
            'projects' => 'المشاريع',
            'inventory' => 'المخزون',
            'sales' => 'المبيعات',
            'purchases' => 'المشتريات',
            'custom' => 'مخصص',
            default => $this->type,
        };
    }

    /**
     * Get the default icon for the type.
     */
    public function getDefaultIconAttribute(): string
    {
        return match($this->type) {
            'payroll' => 'fas fa-users',
            'final_accounts' => 'fas fa-file-invoice-dollar',
            'assets' => 'fas fa-building',
            'budget' => 'fas fa-chart-pie',
            'projects' => 'fas fa-project-diagram',
            'inventory' => 'fas fa-boxes',
            'sales' => 'fas fa-shopping-cart',
            'purchases' => 'fas fa-shopping-bag',
            'custom' => 'fas fa-folder',
            default => 'fas fa-folder',
        };
    }

    /**
     * Get the default color for the type.
     */
    public function getDefaultColorAttribute(): string
    {
        return match($this->type) {
            'payroll' => 'blue',
            'final_accounts' => 'green',
            'assets' => 'purple',
            'budget' => 'orange',
            'projects' => 'indigo',
            'inventory' => 'yellow',
            'sales' => 'pink',
            'purchases' => 'red',
            'custom' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get the total number of accounts.
     */
    public function getAccountsCountAttribute(): int
    {
        return $this->accounts()->count();
    }
}

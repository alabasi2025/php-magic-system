<?php

namespace App\Genes\ORGANIZATIONAL_STRUCTURE\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Project Model - المشاريع
 * 
 * المشروع هو عمل محدد بفترة زمنية وميزانية
 * يمكن أن يكون تابعاً لوحدة أو قسم
 * 
 * @package App\Models
 */
class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unit_id',
        'department_id',
        'code',
        'name',
        'name_en',
        'description',
        'type',
        'manager_id',
        'client_id',
        'client_name',
        'start_date',
        'end_date',
        'actual_end_date',
        'budget',
        'actual_cost',
        'revenue',
        'progress',
        'status',
        'priority',
        'location',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_end_date' => 'date',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'revenue' => 'decimal:2',
        'progress' => 'decimal:2',
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
     * الوحدة التابع لها
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * القسم التابع له
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * مدير المشروع
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * العميل
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * المهام التابعة
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
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
     * Scope للمشاريع النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope للمشاريع المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope للمشاريع حسب النوع
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope للمشاريع حسب الأولوية
     */
    public function scopeOfPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * الاسم الكامل
     */
    public function getFullNameAttribute(): string
    {
        return $this->code . ' - ' . $this->name;
    }

    /**
     * الربح
     */
    public function getProfitAttribute(): float
    {
        return $this->revenue - $this->actual_cost;
    }

    /**
     * نسبة الربح
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->revenue == 0) {
            return 0;
        }
        return ($this->profit / $this->revenue) * 100;
    }

    /**
     * الانحراف عن الميزانية
     */
    public function getBudgetVarianceAttribute(): float
    {
        return $this->budget - $this->actual_cost;
    }

    /**
     * هل تأخر المشروع؟
     */
    public function getIsDelayedAttribute(): bool
    {
        if (!$this->end_date || $this->status == 'completed') {
            return false;
        }
        return now()->gt($this->end_date);
    }

    /**
     * Get the attributes that should be searchable.
     *
     * @return array<int, string>
     */
    public function getSearchableAttributes(): array
    {
        return ['code', 'name', 'name_en', 'description', 'client_name'];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Unit Model - الوحدات
 * 
 * الوحدة هي كيان تنظيمي تابع للشركة القابضة
 * يمكن أن تكون شركة، فرع، أو مؤسسة
 * 
 * @package App\Models
 */
class Unit extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'units';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'holding_id',
        'parent_id',
        'code',
        'name',
        'name_en',
        'type',
        'email',
        'phone',
        'fax',
        'address',
        'city',
        'country',
        'postal_code',
        'tax_number',
        'commercial_register',
        'manager_id',
        'is_active',
        'start_date',
        'end_date',
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
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
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
     * الشركة القابضة
     */
    public function holding(): BelongsTo
    {
        return $this->belongsTo(Holding::class, 'holding_id');
    }

    /**
     * الوحدة الأم
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }

    /**
     * الوحدات الفرعية
     */
    public function children(): HasMany
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }

    /**
     * المدير
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * الأقسام التابعة
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'unit_id');
    }

    /**
     * المشاريع التابعة
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'unit_id');
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
     * Scope للوحدات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للوحدات حسب النوع
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
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
     * Get the attributes that should be searchable.
     *
     * @return array<int, string>
     */
    public function getSearchableAttributes(): array
    {
        return ['code', 'name', 'name_en', 'email', 'phone'];
    }
}

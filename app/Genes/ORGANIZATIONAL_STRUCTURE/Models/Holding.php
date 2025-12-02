<?php

namespace App\Genes\ORGANIZATIONAL_STRUCTURE\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Holding Model - الشركات القابضة
 * 
 * الشركة القابضة هي المستوى الأعلى في الهيكل التنظيمي
 * تحتوي على عدة وحدات ومشاريع وأقسام
 * 
 * @package App\Models
 */
class Holding extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'holdings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'email',
        'phone',
        'fax',
        'website',
        'address',
        'city',
        'country',
        'postal_code',
        'tax_number',
        'commercial_register',
        'legal_form',
        'currency',
        'fiscal_year_start',
        'is_active',
        'logo',
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
     * الوحدات التابعة
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'holding_id');
    }

    /**
     * الأقسام التابعة (عبر الوحدات)
     */
    public function departments()
    {
        return $this->hasManyThrough(
            Department::class,
            Unit::class,
            'holding_id',
            'unit_id',
            'id',
            'id'
        );
    }

    /**
     * المشاريع التابعة (عبر الوحدات)
     */
    public function projects()
    {
        return $this->hasManyThrough(
            Project::class,
            Unit::class,
            'holding_id',
            'unit_id',
            'id',
            'id'
        );
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
     * Scope للشركات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
        return ['code', 'name', 'name_en', 'email', 'phone', 'tax_number'];
    }
}

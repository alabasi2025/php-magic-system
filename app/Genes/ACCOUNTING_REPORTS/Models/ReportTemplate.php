<?php

namespace App\Genes\ACCOUNTING_REPORTS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

/**
 * قالب التقرير
 * 
 * نموذج لتمثيل قوالب التقارير المحاسبية المخصصة
 */
class ReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'template_type',
        'structure',
        'parameters',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'structure' => 'array',
        'parameters' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم الذي أنشأ القالب
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع التقارير المولدة من هذا القالب
     */
    public function generatedReports(): HasMany
    {
        return $this->hasMany(GeneratedReport::class, 'template_id');
    }

    /**
     * الحصول على القوالب النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * الحصول على القوالب حسب النوع
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('template_type', $type);
    }

    /**
     * الحصول على عدد التقارير المولدة
     */
    public function getGeneratedCountAttribute(): int
    {
        return $this->generatedReports()->count();
    }

    /**
     * التحقق من صلاحية القالب
     */
    public function isValid(): bool
    {
        return $this->is_active && 
               !empty($this->structure) && 
               !empty($this->parameters);
    }

    /**
     * تعطيل القالب
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * تفعيل القالب
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }
}

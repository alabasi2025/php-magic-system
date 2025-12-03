<?php

/**
 * 🧬 Gene: SeederTemplate Model
 * 
 * Model لتخزين وإدارة قوالب الـ Seeders الجاهزة
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Models
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeederTemplate extends Model
{
    use HasFactory;

    /**
     * اسم الجدول
     */
    protected $table = 'seeder_templates';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'table_name',
        'model_name',
        'default_count',
        'schema',
        'is_active',
        'usage_count',
    ];

    /**
     * الحقول المخفية
     */
    protected $hidden = [];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'schema' => 'array',
        'is_active' => 'boolean',
        'default_count' => 'integer',
        'usage_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * الفئات الممكنة
     */
    const CATEGORY_USER_MANAGEMENT = 'user_management';
    const CATEGORY_ECOMMERCE = 'ecommerce';
    const CATEGORY_BLOG = 'blog';
    const CATEGORY_CRM = 'crm';
    const CATEGORY_ACCOUNTING = 'accounting';
    const CATEGORY_INVENTORY = 'inventory';
    const CATEGORY_HR = 'hr';
    const CATEGORY_OTHER = 'other';

    /**
     * العلاقة: الـ Seeders المولدة من هذا القالب
     */
    public function generations(): HasMany
    {
        return $this->hasMany(SeederGeneration::class, 'template_id');
    }

    /**
     * Scope: النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: حسب الفئة
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: الأكثر استخداماً
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    /**
     * Scope: الأحدث
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * زيادة عداد الاستخدام
     */
    public function incrementUsage(): bool
    {
        return $this->increment('usage_count');
    }

    /**
     * تفعيل القالب
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * تعطيل القالب
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * التحقق: هل القالب نشط؟
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * الحصول على قائمة الفئات
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_USER_MANAGEMENT => 'إدارة المستخدمين',
            self::CATEGORY_ECOMMERCE => 'التجارة الإلكترونية',
            self::CATEGORY_BLOG => 'المدونات',
            self::CATEGORY_CRM => 'إدارة العملاء',
            self::CATEGORY_ACCOUNTING => 'المحاسبة',
            self::CATEGORY_INVENTORY => 'المخزون',
            self::CATEGORY_HR => 'الموارد البشرية',
            self::CATEGORY_OTHER => 'أخرى',
        ];
    }

    /**
     * الحصول على اسم الفئة بالعربية
     */
    public function getCategoryNameAttribute(): string
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? $this->category;
    }
}

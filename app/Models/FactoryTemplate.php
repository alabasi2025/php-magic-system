<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 🧬 Model: FactoryTemplate
 * 
 * نموذج قوالب الـ Factories
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Models
 * @package App\Models
 */
class FactoryTemplate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * اسم الجدول
     */
    protected $table = 'factory_templates';

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'model_name',
        'table_name',
        'schema',
        'is_public',
        'usage_count',
        'rating',
        'created_by',
        'updated_by',
    ];

    /**
     * الحقول المخفية
     */
    protected $hidden = [];

    /**
     * تحويل الحقول
     */
    protected $casts = [
        'schema' => 'array',
        'is_public' => 'boolean',
        'usage_count' => 'integer',
        'rating' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * الفئات المتاحة
     */
    const CATEGORY_ECOMMERCE = 'ecommerce';
    const CATEGORY_BLOG = 'blog';
    const CATEGORY_CRM = 'crm';
    const CATEGORY_ERP = 'erp';
    const CATEGORY_GENERAL = 'general';
    const CATEGORY_CUSTOM = 'custom';

    /**
     * العلاقة مع المستخدم الذي أنشأ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع المستخدم الذي عدّل
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: القوالب العامة
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
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
    public function scopeMostUsed($query, int $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    /**
     * Scope: الأعلى تقييماً
     */
    public function scopeTopRated($query, int $limit = 10)
    {
        return $query->orderBy('rating', 'desc')->limit($limit);
    }

    /**
     * زيادة عداد الاستخدام
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * تحديث التقييم
     */
    public function updateRating(float $rating): void
    {
        $this->update(['rating' => $rating]);
    }

    /**
     * عرض القالب
     */
    public function render(array $variables = []): string
    {
        $content = $this->schema['template'] ?? '';
        
        foreach ($variables as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }
        
        return $content;
    }

    /**
     * الحصول على جميع الفئات
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_ECOMMERCE => 'التجارة الإلكترونية',
            self::CATEGORY_BLOG => 'المدونات',
            self::CATEGORY_CRM => 'إدارة العملاء',
            self::CATEGORY_ERP => 'تخطيط موارد المؤسسة',
            self::CATEGORY_GENERAL => 'عام',
            self::CATEGORY_CUSTOM => 'مخصص',
        ];
    }
}

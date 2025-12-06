<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * نموذج قالب القيد اليومي
 * يمثل قالباً جاهزاً لإنشاء قيود يومية متكررة
 */
class JournalTemplate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * الحقول القابلة للتعبئة الجماعية
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'template_data',
        'is_active',
        'created_by',
    ];

    /**
     * الحقول التي يجب تحويلها إلى أنواع أصلية
     */
    protected $casts = [
        'template_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * علاقة: المستخدم الذي أنشأ القالب
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

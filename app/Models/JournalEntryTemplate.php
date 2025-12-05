<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property array $template_data
 * @property int $user_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class JournalEntryTemplate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'template_data',
        'user_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'template_data' سيحتوي على بيانات القيد (مثل الحسابات والمبالغ) بصيغة JSON
        'template_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع المستخدم الذي أنشأ القالب.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        // نفترض وجود نموذج User في المسار الافتراضي
        return $this->belongsTo(\App\Models\User::class);
    }

    // يمكن إضافة نطاقات (Scopes) أو دوال مساعدة هنا
}

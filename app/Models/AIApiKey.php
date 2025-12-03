<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $key
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class AIApiKey extends Model
{
    use HasFactory;

    /**
     * @var string اسم الجدول المرتبط بالنموذج
     */
    protected $table = 'ai_api_keys';

    /**
     * @var array الحقول التي يمكن تعبئتها جماعياً (Mass Assignable)
     */
    protected $fillable = [
        'user_id',      // معرف المستخدم الذي يملك المفتاح
        'name',         // اسم وصفي للمفتاح (مثل: مفتاح تطبيق الويب)
        'key',          // مفتاح API الفعلي
        'is_active',    // حالة المفتاح (نشط/غير نشط)
        'last_used_at', // تاريخ آخر استخدام للمفتاح
        'expires_at',   // تاريخ انتهاء صلاحية المفتاح
    ];

    /**
     * @var array تحويل أنواع البيانات للحقول
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // العلاقات (Relationships)
    // -------------------------------------------------------------------------

    /**
     * علاقة: المفتاح ينتمي إلى مستخدم واحد.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        // يربط هذا المفتاح بنموذج المستخدم (User Model) باستخدام user_id
        return $this->belongsTo(User::class, 'user_id');
    }

    // -------------------------------------------------------------------------
    // النطاقات (Scopes)
    // -------------------------------------------------------------------------

    /**
     * نطاق: استرجاع المفاتيح النشطة فقط.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق: استرجاع المفاتيح التي لم تنتهِ صلاحيتها بعد.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotExpired($query)
    {
        return $query->whereNull('expires_at')
                     ->orWhere('expires_at', '>', now());
    }
}

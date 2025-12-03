<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @class AIConversation
 * @brief نموذج Eloquent لإدارة محادثات الذكاء الاصطناعي.
 *
 * يمثل هذا النموذج محادثة واحدة بين المستخدم ونظام الذكاء الاصطناعي.
 * يتميز بوجود علاقة مع المستخدم الذي أنشأ المحادثة وعلاقة مع الرسائل التابعة لهذه المحادثة.
 */
class AIConversation extends Model
{
    use HasFactory;

    /**
     * @var string $table اسم الجدول المرتبط بالنموذج.
     */
    protected $table = 'ai_conversations';

    /**
     * @var array<int, string> $fillable السمات التي يمكن تعبئتها بشكل جماعي (Mass Assignable).
     */
    protected $fillable = [
        'user_id', // مفتاح خارجي للمستخدم الذي يمتلك المحادثة
        'title',   // عنوان المحادثة (يمكن أن يكون ملخصًا)
        'context', // سياق المحادثة الأولي أو إعداداتها (JSON)
        'status',  // حالة المحادثة (مثل: active, archived, completed)
    ];

    /**
     * @var array<string, string> $casts تحويل أنواع البيانات للسمات.
     */
    protected $casts = [
        'context' => 'array', // تحويل حقل السياق إلى مصفوفة PHP (JSON في قاعدة البيانات)
    ];

    // -------------------------------------------------------------------------
    // العلاقات (Relationships)
    // -------------------------------------------------------------------------

    /**
     * @brief العلاقة: المحادثة تنتمي إلى مستخدم واحد.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        // يفترض وجود نموذج App\Models\User
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @brief العلاقة: المحادثة تحتوي على العديد من الرسائل.
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        // يفترض وجود نموذج App\Models\AIMessage لإدارة رسائل المحادثة
        return $this->hasMany(AIMessage::class, 'conversation_id');
    }

    // -------------------------------------------------------------------------
    // نطاقات الاستعلام (Query Scopes) - مثال على أفضل الممارسات
    // -------------------------------------------------------------------------

    /**
     * @brief نطاق استعلام لجلب المحادثات النشطة فقط.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

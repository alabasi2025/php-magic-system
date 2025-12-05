<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $journal_entry_id
 * @property string $file_name
 * @property string $file_path
 * @property string $file_type
 * @property int $file_size
 * @property int $uploaded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\JournalEntry $journalEntry
 * @property-read \App\Models\User $uploader
 * @property-read string $readable_size
 * @property-read bool $is_image
 */
class JournalEntryAttachment extends Model
{
    use HasFactory;

    // تحديد الحقول التي يمكن تعبئتها جماعياً
    protected $fillable = [
        'journal_entry_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    // الحقول التي يجب تحويلها تلقائياً
    protected $casts = [
        'file_size' => 'integer',
    ];

    // إضافة مُعدّلات (Accessors) للخصائص المحسوبة
    protected $appends = ['readable_size', 'is_image'];

    /**
     * العلاقة: المرفق ينتمي إلى قيد يومية واحد.
     *
     * @return BelongsTo
     */
    public function journalEntry(): BelongsTo
    {
        // نفترض وجود نموذج JournalEntry
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * العلاقة: المرفق تم رفعه بواسطة مستخدم واحد.
     *
     * @return BelongsTo
     */
    public function uploader(): BelongsTo
    {
        // نفترض وجود نموذج User
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * مُعدّل (Accessor) للحصول على حجم الملف بصيغة مقروءة.
     *
     * @return string
     */
    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * مُعدّل (Accessor) للتحقق مما إذا كان الملف صورة.
     *
     * @return bool
     */
    public function getIsImageAttribute(): bool
    {
        // أنواع MIME الشائعة للصور التي قد يتم رفعها
        return in_array($this->file_type, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ]);
    }
}

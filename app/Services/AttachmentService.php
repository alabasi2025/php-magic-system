<?php

namespace App\Services;

use App\Models\JournalEntryAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AttachmentService
{
    // تحديد مسار التخزين الافتراضي
    protected string $disk = 'public';
    protected string $basePath = 'journal_attachments';

    // أنواع الملفات المسموحة (MIME Types)
    protected array $allowedMimeTypes = [
        'application/pdf', // PDF
        'image/jpeg',      // JPG
        'image/png',       // PNG
        'application/vnd.ms-excel', // XLS
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // XLSX
    ];

    // الحد الأقصى لحجم الملف (5MB = 5 * 1024 * 1024 بايت)
    protected int $maxFileSize = 5242880;

    /**
     * التحقق من صحة الملف قبل الرفع.
     *
     * @param UploadedFile $file
     * @throws ValidationException
     */
    protected function validateFile(UploadedFile $file): void
    {
        // التحقق من نوع الملف
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw ValidationException::withMessages([
                'attachment' => 'نوع الملف غير مسموح به. الأنواع المسموحة: PDF, JPG, PNG, Excel.',
            ]);
        }

        // التحقق من حجم الملف
        if ($file->getSize() > $this->maxFileSize) {
            throw ValidationException::withMessages([
                'attachment' => 'حجم الملف يتجاوز الحد الأقصى المسموح به (5MB).',
            ]);
        }
    }

    /**
     * رفع ملف واحد أو مجموعة ملفات وإرفاقها بقيد يومية.
     *
     * @param int $journalEntryId
     * @param array|UploadedFile $files
     * @param int $uploadedBy
     * @return Collection<JournalEntryAttachment>
     * @throws ValidationException
     */
    public function upload(int $journalEntryId, array|UploadedFile $files, int $uploadedBy): Collection
    {
        $uploadedFiles = collect($files);
        $attachments = collect();

        foreach ($uploadedFiles as $file) {
            // 1. التحقق من صحة الملف
            $this->validateFile($file);

            // 2. تخزين الملف
            // يتم إنشاء مسار فرعي بناءً على معرف القيد
            $path = Storage::disk($this->disk)->putFile(
                "{$this->basePath}/{$journalEntryId}",
                $file
            );

            if (!$path) {
                throw new \Exception('فشل في تخزين الملف.');
            }

            // 3. حفظ بيانات المرفق في قاعدة البيانات
            $attachment = JournalEntryAttachment::create([
                'journal_entry_id' => $journalEntryId,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $uploadedBy,
            ]);

            $attachments->push($attachment);
        }

        return $attachments;
    }

    /**
     * تحميل ملف مرفق.
     *
     * @param JournalEntryAttachment $attachment
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(JournalEntryAttachment $attachment)
    {
        // التحقق من وجود الملف في نظام التخزين
        if (!Storage::disk($this->disk)->exists($attachment->file_path)) {
            abort(404, 'الملف المطلوب غير موجود.');
        }

        // إرجاع الملف كاستجابة تحميل
        return Storage::disk($this->disk)->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }

    /**
     * حذف ملف مرفق من التخزين وقاعدة البيانات.
     *
     * @param JournalEntryAttachment $attachment
     * @return bool
     */
    public function delete(JournalEntryAttachment $attachment): bool
    {
        // 1. حذف الملف من نظام التخزين
        $deletedFromStorage = Storage::disk($this->disk)->delete($attachment->file_path);

        // 2. حذف السجل من قاعدة البيانات
        $deletedFromDb = $attachment->delete();

        // نعتبر العملية ناجحة إذا تم الحذف من قاعدة البيانات، حتى لو فشل حذف الملف (لأن السجل هو الأهم)
        return $deletedFromDb;
    }

    /**
     * الحصول على جميع المرفقات لقيد يومية معين.
     *
     * @param int $journalEntryId
     * @return Collection<JournalEntryAttachment>
     */
    public function getAttachments(int $journalEntryId): Collection
    {
        return JournalEntryAttachment::where('journal_entry_id', $journalEntryId)
            ->with('uploader')
            ->get();
    }
}

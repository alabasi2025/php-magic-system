<?php

namespace App\Http\Controllers;

use App\Models\JournalEntryAttachment;
use App\Services\AttachmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class JournalEntryAttachmentController extends Controller
{
    protected AttachmentService $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
        // تطبيق سياسة الصلاحيات (Authorization)
        // نفترض وجود سياسة JournalEntryAttachmentPolicy
        $this->middleware('auth');
    }

    /**
     * رفع ملفات جديدة لقيد يومية.
     *
     * @param Request $request
     * @param int $journalEntryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, int $journalEntryId)
    {
        // التحقق من أن القيد موجود وأن المستخدم لديه صلاحية الإضافة
        // (يجب أن يتم التحقق من وجود JournalEntry في مشروعك الفعلي)

        try {
            // التحقق من صحة المدخلات (يجب أن يكون ملف واحد على الأقل)
            $request->validate([
                'attachments' => 'required|array',
                'attachments.*' => 'file|max:5120', // 5120KB = 5MB
            ]);

            $attachments = $this->attachmentService->upload(
                $journalEntryId,
                $request->file('attachments'),
                Auth::id()
            );

            return response()->json([
                'message' => 'تم رفع المرفقات بنجاح.',
                'attachments' => $attachments,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'فشل التحقق من صحة الملفات.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // تسجيل الخطأ
            \Log::error("Attachment upload failed: " . $e->getMessage());
            return response()->json([
                'message' => 'حدث خطأ أثناء رفع الملفات.',
            ], 500);
        }
    }

    /**
     * تحميل ملف مرفق.
     *
     * @param JournalEntryAttachment $attachment
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(JournalEntryAttachment $attachment)
    {
        // التحقق من صلاحية المستخدم لتحميل المرفق (يجب أن يكون لديه صلاحية الوصول للقيد)
        // $this->authorize('view', $attachment);

        return $this->attachmentService->download($attachment);
    }

    /**
     * حذف ملف مرفق.
     *
     * @param JournalEntryAttachment $attachment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(JournalEntryAttachment $attachment)
    {
        // التحقق من صلاحية المستخدم لحذف المرفق
        // $this->authorize('delete', $attachment);

        try {
            $this->attachmentService->delete($attachment);

            return response()->json([
                'message' => 'تم حذف المرفق بنجاح.',
            ]);
        } catch (\Exception $e) {
            \Log::error("Attachment deletion failed: " . $e->getMessage());
            return response()->json([
                'message' => 'حدث خطأ أثناء حذف المرفق.',
            ], 500);
        }
    }

    /**
     * جلب قائمة المرفقات لقيد يومية معين (للاستخدام عبر AJAX).
     *
     * @param int $journalEntryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $journalEntryId)
    {
        // التحقق من صلاحية المستخدم لعرض القيد
        // (يجب أن يتم التحقق من وجود JournalEntry في مشروعك الفعلي)

        $attachments = $this->attachmentService->getAttachments($journalEntryId);

        return response()->json($attachments);
    }
}

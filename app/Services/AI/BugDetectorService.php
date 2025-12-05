<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Exception;

/**
 * BugDetectorService
 *
 * خدمة كشف الأخطاء باستخدام الذكاء الاصطناعي.
 */
class BugDetectorService
{
    /**
     * كشف الأخطاء في الكود المقدم.
     *
     * @param string $code الكود المراد تحليله.
     * @return array نتائج التحليل.
     */
    public function detectBugs(string $code): array
    {
        // محاكاة لعملية تحليل الكود بواسطة الذكاء الاصطناعي
        // في بيئة الإنتاج، سيتم استدعاء API خارجي (مثل OpenAI أو Gemini)
        Log::info('Simulating AI bug detection for code: ' . substr($code, 0, 50) . '...');

        // مثال على نتائج تحليل الأخطاء
        $mockBugs = [];

        // تحليل بسيط: البحث عن علامات PHP غير مغلقة
        if (substr(trim($code), -2) !== '?>' && substr(trim($code), 0, 5) === '<?php') {
            $mockBugs[] = [
                'type' => 'Warning',
                'line' => 99, // رقم سطر وهمي
                'message' => 'قد يكون هناك علامة PHP غير مغلقة في نهاية الملف.',
                'suggestion' => 'تأكد من إغلاق علامة PHP (?>) إذا لم يكن الملف يحتوي على HTML بعدها.'
            ];
        }

        // تحليل بسيط: البحث عن استخدام دالة غير معرفة (افتراضياً)
        if (str_contains($code, 'undefined_function(')) {
            $mockBugs[] = [
                'type' => 'Error',
                'line' => 10,
                'message' => 'استخدام دالة غير معرفة: undefined_function().',
                'suggestion' => 'تأكد من تعريف الدالة أو استيراد المكتبة الصحيحة.'
            ];
        }

        // تحليل بسيط: البحث عن محاولة جمع نص ورقم (Type Error)
        if (preg_match('/\$[a-zA-Z0-9_]+\s*\+\s*["\'][^"\']*["\']/', $code)) {
            $mockBugs[] = [
                'type' => 'Error',
                'line' => 5,
                'message' => 'محاولة جمع متغير رقمي مع سلسلة نصية.',
                'suggestion' => 'قم بتحويل السلسلة النصية إلى رقم باستخدام (int) أو (float) قبل إجراء العملية الحسابية.'
            ];
        }

        // إذا لم يتم العثور على أخطاء، نرجع نتائج فارغة
        if (empty($mockBugs)) {
            return [
                'status' => 'success',
                'message' => 'لم يتم العثور على أخطاء أو تحذيرات في الكود.',
                'bugs' => []
            ];
        }

        return [
            'status' => 'success',
            'message' => 'تم العثور على أخطاء وتحذيرات في الكود.',
            'bugs' => $mockBugs
        ];
    }
}

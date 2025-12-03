<?php

namespace App\Services\AI;

use Illuminate\Support\Collection;

/**
 * Class BugDetectorService
 *
 * خدمة كاشف الأخطاء الذكي (AI Bug Detector Service).
 * هذه الخدمة مسؤولة عن تحليل الكود البرمجي المكتوب
 * واكتشاف الأخطاء المحتملة من أنواع مختلفة (Syntax, Logic, Runtime, Type)
 * وتقديم اقتراحات إصلاح فورية.
 *
 * @package App\Services\AI
 * @author Manus AI
 * @version 1.0.0
 * @since 2025-12-03
 */
class BugDetectorService
{
    /**
     * يحلل الكود البرمجي ويكتشف الأخطاء المحتملة.
     *
     * في بيئة إنتاج حقيقية، سيتم هنا استدعاء نموذج ذكاء اصطناعي (LLM)
     * أو استخدام أدوات تحليل الكود الثابت (Static Analysis Tools)
     * مثل PHPStan أو Psalm لتحليل الكود.
     *
     * @param string $code الكود البرمجي المراد تحليله.
     * @return Collection مجموعة من الأخطاء المكتشفة، كل خطأ يحتوي على:
     *                  - 'type': نوع الخطأ (Syntax, Logic, Runtime, Type).
     *                  - 'severity': شدة الخطأ (error, warning).
     *                  - 'line': رقم السطر الذي حدث فيه الخطأ.
     *                  - 'message': وصف موجز للخطأ.
     *                  - 'suggestion': اقتراح إصلاح فوري.
     */
    public function detectBugs(string $code): Collection
    {
        // تقسيم الكود إلى أسطر لتسهيل محاكاة اكتشاف الأخطاء برقم السطر
        $lines = explode("\n", $code);
        $bugs = [];

        // محاكاة اكتشاف الأخطاء باستخدام قواعد بسيطة
        foreach ($lines as $lineNumber => $lineContent) {
            $lineNumber = $lineNumber + 1; // الأسطر تبدأ من 1

            // 1. محاكاة Syntax Error (خطأ نحوي)
            if (str_contains($lineContent, 'function(') && !str_contains($lineContent, '{')) {
                $bugs[] = $this->createBug(
                    'Syntax',
                    'error',
                    $lineNumber,
                    'قوس مفقود أو فاصلة منقوطة مفقودة في تعريف الدالة.',
                    'تأكد من إغلاق الأقواس واستخدام الفاصلة المنقوطة (;) بشكل صحيح.'
                );
            }

            // 2. محاكاة Type Error (خطأ في النوع)
            if (preg_match('/\$(\w+)\s*=\s*"\d+";\s*\$(\w+)\s*=\s*\$(\w+)\s*\+\s*1;/', $lineContent)) {
                $bugs[] = $this->createBug(
                    'Type',
                    'warning',
                    $lineNumber,
                    'محاولة إجراء عملية حسابية على متغير من نوع سلسلة نصية (string).',
                    'استخدم (int) أو (float) لتحويل المتغير إلى نوع رقمي قبل إجراء العملية الحسابية.'
                );
            }

            // 3. محاكاة Logic Error (خطأ منطقي) - مثال: حلقة لا نهائية محتملة
            if (str_contains($lineContent, 'while (true)')) {
                $bugs[] = $this->createBug(
                    'Logic',
                    'error',
                    $lineNumber,
                    'حلقة لا نهائية محتملة (while (true)).',
                    'تأكد من وجود شرط خروج واضح للحلقة لتجنب استهلاك موارد النظام.'
                );
            }

            // 4. محاكاة Runtime Error (خطأ وقت التشغيل) - مثال: استخدام دالة غير معرفة
            if (str_contains($lineContent, 'undefined_function(')) {
                $bugs[] = $this->createBug(
                    'Runtime',
                    'error',
                    $lineNumber,
                    'استدعاء دالة غير معرفة (undefined_function).',
                    'تحقق من اسم الدالة وتأكد من تعريفها أو استيرادها بشكل صحيح.'
                );
            }
        }

        // في حال عدم وجود أخطاء، يمكن إضافة رسالة تحذيرية بسيطة (اختياري)
        if (empty($bugs)) {
            $bugs[] = $this->createBug(
                'Analysis',
                'success',
                0, // 0 لعدم الارتباط بسطر معين
                'تم تحليل الكود بنجاح.',
                'لم يتم العثور على أخطاء حرجة. الكود يبدو سليمًا.'
            );
        }

        return collect($bugs);
    }

    /**
     * دالة مساعدة لإنشاء هيكل الخطأ الموحد.
     *
     * @param string $type نوع الخطأ (Syntax, Logic, Runtime, Type).
     * @param string $severity شدة الخطأ (error, warning, success).
     * @param int $line رقم السطر.
     * @param string $message وصف الخطأ.
     * @param string $suggestion اقتراح الإصلاح.
     * @return array هيكل الخطأ.
     */
    protected function createBug(string $type, string $severity, int $line, string $message, string $suggestion): array
    {
        return [
            'type' => $type,
            'severity' => $severity, // يستخدم لتحديد اللون (أحمر للخطأ، أصفر للتحذير) في الواجهة
            'line' => $line,
            'message' => $message,
            'suggestion' => $suggestion,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}

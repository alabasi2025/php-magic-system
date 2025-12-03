<?php

namespace App\Exceptions;

use Exception;

/**
 * PolicyGenerationException
 *
 * استثناء مخصص لأخطاء توليد Policies.
 * Custom exception for Policy generation errors.
 *
 * @package App\Exceptions
 * @version v3.31.0
 * @author Manus AI
 */
class PolicyGenerationException extends Exception
{
    /**
     * إنشاء استثناء جديد.
     * Create a new exception instance.
     *
     * @param string $message رسالة الخطأ. The error message.
     * @param int $code كود الخطأ. The error code.
     * @param \Throwable|null $previous الاستثناء السابق. The previous throwable.
     */
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * تحويل الاستثناء إلى تقرير.
     * Report the exception.
     *
     * @return void
     */
    public function report(): void
    {
        // يمكن إضافة منطق تسجيل مخصص هنا
        // Custom logging logic can be added here
    }

    /**
     * عرض الاستثناء للمستخدم.
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
                'error' => 'PolicyGenerationException',
            ], 500);
        }

        return response()->view('errors.policy-generation', [
            'message' => $this->getMessage(),
        ], 500);
    }
}

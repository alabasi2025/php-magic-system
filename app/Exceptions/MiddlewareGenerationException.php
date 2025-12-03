<?php

namespace App\Exceptions;

use Exception;

/**
 * MiddlewareGenerationException
 *
 * استثناء مخصص لأخطاء توليد Middleware.
 * Custom exception for Middleware generation errors.
 *
 * @package App\Exceptions
 * @version v3.28.0
 * @author Manus AI
 */
class MiddlewareGenerationException extends Exception
{
    /**
     * إنشاء استثناء جديد.
     * Create a new exception instance.
     *
     * @param string $message رسالة الخطأ. The error message.
     * @param int $code كود الخطأ. The error code.
     * @param \Throwable|null $previous الاستثناء السابق. The previous exception.
     */
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * تحويل الاستثناء إلى استجابة HTTP.
     * Convert the exception to an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'trace' => config('app.debug') ? $this->getTraceAsString() : null,
        ], 500);
    }

    /**
     * تسجيل الاستثناء.
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        \Log::error('Middleware Generation Error: ' . $this->getMessage(), [
            'exception' => get_class($this),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString(),
        ]);
    }
}

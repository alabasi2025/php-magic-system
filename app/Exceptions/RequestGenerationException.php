<?php

namespace App\Exceptions;

use Exception;

/**
 * @class RequestGenerationException
 * @package App\Exceptions
 *
 * @brief استثناء خاص بتوليد Form Requests.
 *
 * يستخدم هذا الاستثناء للإبلاغ عن الأخطاء التي تحدث أثناء
 * عملية توليد Form Requests.
 *
 * Exception for Form Request generation errors.
 *
 * This exception is used to report errors that occur during
 * the Form Request generation process.
 *
 * @version 3.29.0
 * @author Manus AI
 */
class RequestGenerationException extends Exception
{
    /**
     * @var array $context سياق الخطأ.
     * Error context.
     */
    protected array $context;

    /**
     * RequestGenerationException constructor.
     *
     * @param string $message رسالة الخطأ. The error message.
     * @param array $context سياق الخطأ. The error context.
     * @param int $code كود الخطأ. The error code.
     * @param \Throwable|null $previous الاستثناء السابق. The previous exception.
     */
    public function __construct(
        string $message = "Failed to generate Form Request",
        array $context = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * @brief الحصول على سياق الخطأ.
     *
     * Get error context.
     *
     * @return array سياق الخطأ. The error context.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @brief تحويل الاستثناء إلى مصفوفة.
     *
     * Convert exception to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'context' => $this->context,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
}

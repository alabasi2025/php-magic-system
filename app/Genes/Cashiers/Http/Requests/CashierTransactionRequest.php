<?php

namespace App\Genes\Cashiers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @author Manus AI
 * @description Form Request for validating Cashier Transaction data (Deposit/Withdrawal).
 * @package App\Genes\Cashiers\Http\Requests
 */
class CashierTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // يجب أن يتم تحديد منطق التفويض بناءً على دور المستخدم أو الصلاحيات
        // في سياق نظام الصرافين، قد يكون التفويض هو التحقق من أن المستخدم الحالي هو صراف نشط
        return true; // يتم تعيينها مؤقتاً إلى true للسماح بالمرور
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // التحقق من وجود معرف الصراف (Cashier ID) وأنه رقم صحيح
            'cashier_id' => [
                'required',
                'integer',
                'exists:cashiers,id', // افتراض وجود جدول 'cashiers'
            ],

            // التحقق من نوع المعاملة (يجب أن يكون إيداع أو سحب)
            'transaction_type' => [
                'required',
                'string',
                Rule::in(['deposit', 'withdrawal']),
            ],

            // التحقق من مبلغ المعاملة (يجب أن يكون رقمًا، أكبر من صفر، وبدقة معينة)
            'amount' => [
                'required',
                'numeric',
                'gt:0',
                'regex:/^\d+(\.\d{1,2})?$/', // للتحقق من رقم عشري بدقة تصل إلى منزلتين
            ],

            // التحقق من العملة (افتراض أن النظام يدعم عملات محددة)
            'currency' => [
                'required',
                'string',
                Rule::in(['SAR', 'USD', 'EUR']), // مثال على العملات المدعومة
            ],

            // وصف اختياري للمعاملة
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cashier_id.required' => 'معرف الصراف مطلوب.',
            'cashier_id.exists' => 'معرف الصراف غير موجود في النظام.',
            'transaction_type.in' => 'نوع المعاملة يجب أن يكون إيداع أو سحب.',
            'amount.required' => 'مبلغ المعاملة مطلوب.',
            'amount.numeric' => 'مبلغ المعاملة يجب أن يكون رقماً.',
            'amount.gt' => 'مبلغ المعاملة يجب أن يكون أكبر من صفر.',
            'amount.regex' => 'مبلغ المعاملة يجب أن يكون رقماً عشرياً بدقة تصل إلى منزلتين.',
            'currency.in' => 'العملة المدخلة غير مدعومة.',
        ];
    }
}
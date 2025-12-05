<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WarehouseStoreRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً بتقديم هذا الطلب.
     */
    public function authorize(): bool
        {
        // يتم التحقق من الصلاحيات في Controller باستخدام Policy
        return true;
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // رمز المخزن: مطلوب، سلسلة نصية، فريد في جدول المخازن، بحد أقصى 50 حرف
            'code' => ['required', 'string', 'max:50', Rule::unique('warehouses', 'code')],
            // اسم المخزن: مطلوب، سلسلة نصية، بحد أقصى 255 حرف
            'name' => ['required', 'string', 'max:255'],
            // الموقع: اختياري، سلسلة نصية، بحد أقصى 255 حرف
            'location' => ['nullable', 'string', 'max:255'],
            // العنوان: اختياري، سلسلة نصية، بحد أقصى 500 حرف
            'address' => ['nullable', 'string', 'max:500'],
            // الهاتف: اختياري، سلسلة نصية، بحد أقصى 20 حرف
            'phone' => ['nullable', 'string', 'max:20'],
            // البريد الإلكتروني: اختياري، يجب أن يكون بريداً إلكترونياً صالحاً، فريد
            'email' => ['nullable', 'email', Rule::unique('warehouses', 'email')],
            // معرف المدير: اختياري، يجب أن يكون موجوداً في جدول المستخدمين
            'manager_id' => ['nullable', 'exists:users,id'],
            // حالة التفعيل: اختياري، يجب أن يكون قيمة منطقية
            'is_active' => ['nullable', 'boolean'],
            // السعة: اختياري، يجب أن يكون عدداً صحيحاً موجباً
            'capacity' => ['nullable', 'integer', 'min:0'],
            // قيمة المخزون الحالية: اختياري، يجب أن يكون عدداً رقمياً موجباً
            'current_stock_value' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * تخصيص رسائل الأخطاء.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'code.required' => 'رمز المخزن مطلوب.',
            'code.unique' => 'رمز المخزن هذا مستخدم بالفعل.',
            'name.required' => 'اسم المخزن مطلوب.',
            'manager_id.exists' => 'مدير المخزن المحدد غير موجود.',
            // يمكن إضافة المزيد من الرسائل المخصصة هنا
        ];
    }
}

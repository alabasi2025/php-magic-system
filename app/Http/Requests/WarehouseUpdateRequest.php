<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WarehouseUpdateRequest extends FormRequest
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
        // الحصول على معرف المخزن من مسار الطلب (Route)
        $warehouseId = $this->route('warehouse');

        return [
            // رمز المخزن: مطلوب، فريد باستثناء المخزن الحالي
            'code' => ['required', 'string', 'max:50', Rule::unique('warehouses', 'code')->ignore($warehouseId)],
            // اسم المخزن: مطلوب
            'name' => ['required', 'string', 'max:255'],
            // الموقع: اختياري
            'location' => ['nullable', 'string', 'max:255'],
            // العنوان: اختياري
            'address' => ['nullable', 'string', 'max:500'],
            // الهاتف: اختياري
            'phone' => ['nullable', 'string', 'max:20'],
            // البريد الإلكتروني: اختياري، فريد باستثناء المخزن الحالي
            'email' => ['nullable', 'email', Rule::unique('warehouses', 'email')->ignore($warehouseId)],
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
        ];
    }
}

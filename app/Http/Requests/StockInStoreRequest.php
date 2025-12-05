<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StockInStoreRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً لإجراء هذا الطلب.
     */
    public function authorize(): bool
    {
        // يجب تطبيق منطق التحقق من الصلاحيات هنا
        // نفترض أن أي مستخدم مسجل يمكنه إنشاء إذن إدخال مبدئياً
        return Auth::check() && Auth::user()->can('create', StockIn::class);
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            
            // قواعد التحقق لتفاصيل الأصناف
            'details' => ['required', 'array', 'min:1'],
            'details.*.item_id' => ['required', 'exists:items,id'],
            'details.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'details.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * تخصيص رسائل التحقق.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'warehouse_id.required' => 'يجب تحديد المخزن.',
            'supplier_id.required' => 'يجب تحديد المورد.',
            'date.required' => 'يجب تحديد تاريخ الإدخال.',
            'details.required' => 'يجب إضافة صنف واحد على الأقل.',
            'details.*.item_id.required' => 'يجب تحديد الصنف.',
            'details.*.quantity.min' => 'يجب أن تكون الكمية أكبر من صفر.',
        ];
    }
}

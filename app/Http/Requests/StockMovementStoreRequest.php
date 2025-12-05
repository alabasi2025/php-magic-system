<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StockMovementStoreRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً لإجراء هذا الطلب.
     */
    public function authorize(): bool
    {
        // يجب أن يكون المستخدم مصرحاً له بتسجيل حركات المخزون
        // نفترض وجود بوابة (Gate) أو سياسة (Policy) للتحقق من الصلاحية
        return Auth::check() && Auth::user()->can('create', StockMovement::class);
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     */
    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'movement_type' => ['required', 'string', Rule::in(['in', 'out', 'adjustment', 'transfer'])],
            'reference_type' => ['required', 'string', 'max:255'],
            'reference_id' => ['required', 'integer'],
            // الكمية يجب أن تكون موجبة، وسيتم التعامل مع نوع الحركة (دخول/خروج) في الخدمة
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            // 'date' يمكن إرسالها يدوياً، وإلا سيتم تعيينها في الخدمة
            'date' => ['nullable', 'date'],
        ];
    }

    /**
     * تخصيص رسائل التحقق.
     */
    public function messages(): array
    {
        return [
            'warehouse_id.required' => 'يجب تحديد المخزن.',
            'item_id.required' => 'يجب تحديد الصنف.',
            'movement_type.required' => 'يجب تحديد نوع الحركة.',
            'movement_type.in' => 'نوع الحركة غير صالح.',
            'reference_type.required' => 'يجب تحديد نوع المستند المرجعي.',
            'reference_id.required' => 'يجب تحديد معرف المستند المرجعي.',
            'quantity.required' => 'يجب تحديد الكمية.',
            'quantity.numeric' => 'يجب أن تكون الكمية رقماً.',
            'quantity.gt' => 'يجب أن تكون الكمية أكبر من الصفر.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * طلب التحقق من صحة بيانات تحويل المخزون.
 */
class StockTransferRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً بتقديم هذا الطلب.
     */
    public function authorize(): bool
    {
        // يتم التعامل مع التخويل في Policy، هنا نكتفي بالسماح
        return true;
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     */
    public function rules(): array
    {
        return [
            'from_warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
                // يجب أن يكون المخزن المصدر مختلفاً عن المخزن المستقبل
                Rule::notIn([$this->input('to_warehouse_id')]),
            ],
            'to_warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
            ],
            'date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            
            // قواعد التحقق لتفاصيل التحويل
            'details' => ['required', 'array', 'min:1'],
            'details.*.item_id' => ['required', 'integer', 'exists:items,id'],
            'details.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    /**
     * رسائل الخطأ المخصصة.
     */
    public function messages(): array
    {
        return [
            'from_warehouse_id.not_in' => 'يجب أن يكون المخزن المصدر مختلفاً عن المخزن المستقبل.',
            'details.min' => 'يجب إضافة مادة واحدة على الأقل لطلب التحويل.',
        ];
    }
}

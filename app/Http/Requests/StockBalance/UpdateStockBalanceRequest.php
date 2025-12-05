<?php

namespace App\Http\Requests\StockBalance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateStockBalanceRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مصرحاً له بتقديم هذا الطلب. (الأمان)
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // نفترض وجود سياسة (Policy) لـ StockBalance
        return Gate::allows('update', $this->route('stock_balance'));
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // تجاهل القيد الفريد لنفس السجل عند التحديث
        $stockBalanceId = $this->route('stock_balance')->id;

        return [
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            // يجب أن يكون فريداً مع warehouse_id باستثناء السجل الحالي
            'item_id' => ['required', 'integer', 'exists:items,id', 'unique:stock_balances,item_id,' . $stockBalanceId . ',id,warehouse_id,' . $this->input('warehouse_id')],
            'quantity' => ['required', 'numeric', 'min:0'],
            'last_cost' => ['required', 'numeric', 'min:0'],
            'average_cost' => ['nullable', 'numeric', 'min:0'],
            'total_value' => ['nullable', 'numeric', 'min:0'],
            'last_updated' => ['nullable', 'date'],
        ];
    }

    /**
     * تخصيص رسائل الخطأ.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'item_id.unique' => 'هذا الصنف موجود بالفعل في هذا المخزن.',
            'warehouse_id.required' => 'يجب تحديد المخزن.',
            'item_id.required' => 'يجب تحديد الصنف.',
            'quantity.min' => 'يجب أن تكون الكمية موجبة.',
        ];
    }
}

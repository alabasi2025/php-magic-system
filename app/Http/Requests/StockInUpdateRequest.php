<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\StockIn;

class StockInUpdateRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً لإجراء هذا الطلب.
     */
    public function authorize(): bool
    {
        // يجب تطبيق منطق التحقق من الصلاحيات هنا
        // نفترض أن المستخدم يمكنه تحديث إذن الإدخال إذا كان مخولاً
        $stockIn = $this->route('stock_in'); // الحصول على نموذج StockIn من المسار
        return Auth::check() && Auth::user()->can('update', $stockIn);
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // نفس قواعد الإنشاء باستثناء رقم الإذن الذي لا يتم تحديثه عادةً
        return [
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            
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
        return (new StockInStoreRequest())->messages();
    }
}

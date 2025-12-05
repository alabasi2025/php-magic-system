<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * طلب التحقق لإنشاء عملية جرد جديدة.
 */
class StockCountStoreRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً بتقديم هذا الطلب.
     */
    public function authorize(): bool
    {
        // يجب أن يكون المستخدم مسجلاً للدخول ومخولاً بإنشاء جرد
        return Auth::check();
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
            // قائمة الأصناف المراد جردها
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer', 'exists:items,id'],
            'items.*.system_quantity' => ['required', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * تخصيص أسماء الحقول للرسائل.
     */
    public function attributes(): array
    {
        return [
            'warehouse_id' => 'المخزن',
            'date' => 'التاريخ',
            'notes' => 'الملاحظات',
            'items' => 'الأصناف',
            'items.*.item_id' => 'معرف الصنف',
            'items.*.system_quantity' => 'الكمية في النظام',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * طلب التحقق لتحديث الكميات الفعلية في عملية الجرد.
 */
class StockCountUpdateQuantitiesRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً بتقديم هذا الطلب.
     */
    public function authorize(): bool
    {
        // يجب أن يكون المستخدم مسجلاً للدخول ومخولاً بتعديل الجرد
        // سيتم التحقق من الصلاحية بشكل أعمق في Policy
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
            // بيانات التفاصيل التي تم جردها
            'details' => ['required', 'array', 'min:1'],
            'details.*.id' => ['required', 'integer', 'exists:stock_count_details,id'],
            'details.*.actual_quantity' => ['required', 'numeric', 'min:0'],
            'details.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * تخصيص أسماء الحقول للرسائل.
     */
    public function attributes(): array
    {
        return [
            'details' => 'تفاصيل الجرد',
            'details.*.id' => 'معرف التفصيل',
            'details.*.actual_quantity' => 'الكمية الفعلية',
        ];
    }
}

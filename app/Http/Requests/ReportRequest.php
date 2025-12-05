<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ReportRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً لتقديم هذا الطلب.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // التحقق من الأمان: يجب أن يكون المستخدم مخولاً لعرض التقارير
        return Gate::allows('view-reports');
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'report_type' => ['required', 'string', 'in:balance,movement,valuation,slow_moving,min_stock,active,purchases,sales'],
            'start_date' => ['nullable', 'date', 'before_or_equal:end_date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date', 'before_or_equal:today'],
            'item_id' => ['nullable', 'integer', 'exists:items,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'period_days' => ['nullable', 'integer', 'min:1'], // للأصناف الراكدة
        ];

        // قواعد خاصة لتقرير حركة الأصناف والأصناف الأكثر حركة والمشتريات والمبيعات
        if (in_array($this->report_type, ['movement', 'active', 'purchases', 'sales'])) {
            $rules['start_date'][] = 'required';
            $rules['end_date'][] = 'required';
        }

        return $rules;
    }

    /**
     * الحصول على أسماء السمات المخصصة لأخطاء التحقق.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'report_type' => 'نوع التقرير',
            'start_date' => 'تاريخ البدء',
            'end_date' => 'تاريخ الانتهاء',
            'item_id' => 'معرف الصنف',
            'limit' => 'الحد الأقصى للنتائج',
            'period_days' => 'فترة الركود بالأيام',
        ];
    }

    /**
     * الحصول على رسائل الخطأ المخصصة لمجموعة قواعد التحقق المحددة.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'report_type.in' => 'نوع التقرير المحدد غير صالح.',
            'start_date.required' => 'تاريخ البدء مطلوب لهذا النوع من التقارير.',
            'end_date.required' => 'تاريخ الانتهاء مطلوب لهذا النوع من التقارير.',
            'start_date.before_or_equal' => 'تاريخ البدء يجب أن يكون قبل أو يساوي تاريخ الانتهاء.',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد أو يساوي تاريخ البدء.',
            'end_date.before_or_equal' => 'تاريخ الانتهاء يجب أن لا يتجاوز تاريخ اليوم.',
            'item_id.exists' => 'الصنف المحدد غير موجود.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFiscalPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fiscal_year_id' => ['required', 'exists:fiscal_years,id'],
            'period_name' => ['required', 'string', 'max:100'],
            'period_number' => ['required', 'integer', 'min:1', 'max:12'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'fiscal_year_id.required' => 'السنة المالية مطلوبة.',
            'fiscal_year_id.exists' => 'السنة المالية غير موجودة.',
            'period_name.required' => 'اسم الفترة مطلوب.',
            'period_number.required' => 'رقم الفترة مطلوب.',
            'period_number.min' => 'رقم الفترة يجب أن يكون على الأقل 1.',
            'period_number.max' => 'رقم الفترة يجب ألا يتجاوز 12.',
            'start_date.required' => 'تاريخ البداية مطلوب.',
            'end_date.required' => 'تاريخ النهاية مطلوب.',
            'end_date.after' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',
        ];
    }
}

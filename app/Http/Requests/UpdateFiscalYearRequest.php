<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFiscalYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fiscalYearId = $this->route('fiscal_year')->id ?? $this->route('fiscal-year');
        
        return [
            'year_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('fiscal_years', 'year_name')->ignore($fiscalYearId)
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'year_name.required' => 'اسم السنة المالية مطلوب.',
            'year_name.unique' => 'اسم السنة المالية مستخدم من قبل.',
            'start_date.required' => 'تاريخ البداية مطلوب.',
            'end_date.required' => 'تاريخ النهاية مطلوب.',
            'end_date.after' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChartAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_code' => ['required', 'string', 'max:50', 'unique:chart_accounts,account_code'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_name_en' => ['nullable', 'string', 'max:255'],
            'account_type' => ['required', 'in:asset,liability,equity,revenue,expense,intermediate'],
            'parent_id' => ['nullable', 'exists:chart_accounts,id'],
            'level' => ['required', 'integer', 'min:1', 'max:10'],
            'is_detail' => ['required', 'boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_code.required' => 'رمز الحساب مطلوب.',
            'account_code.unique' => 'رمز الحساب مستخدم من قبل.',
            'account_name.required' => 'اسم الحساب مطلوب.',
            'account_type.required' => 'نوع الحساب مطلوب.',
            'account_type.in' => 'نوع الحساب غير صحيح.',
            'parent_id.exists' => 'الحساب الأب غير موجود.',
            'level.required' => 'مستوى الحساب مطلوب.',
            'is_detail.required' => 'يجب تحديد ما إذا كان الحساب تفصيلياً أم إجمالياً.',
        ];
    }
}

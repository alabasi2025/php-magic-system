<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_number' => ['required', 'string', 'max:50', 'unique:bank_accounts,account_number'],
            'account_name' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:3'],
            'iban' => ['nullable', 'string', 'max:34'],
            'swift_code' => ['nullable', 'string', 'max:11'],
            'chart_account_id' => ['required', 'exists:chart_accounts,id'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'current_balance' => ['nullable', 'numeric'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_number.required' => 'رقم الحساب مطلوب.',
            'account_number.unique' => 'رقم الحساب مستخدم من قبل.',
            'account_name.required' => 'اسم الحساب مطلوب.',
            'bank_name.required' => 'اسم البنك مطلوب.',
            'currency.required' => 'العملة مطلوبة.',
            'chart_account_id.required' => 'الحساب المحاسبي مطلوب.',
            'chart_account_id.exists' => 'الحساب المحاسبي غير موجود.',
            'opening_balance.min' => 'الرصيد الافتتاحي يجب أن يكون صفر أو أكثر.',
        ];
    }
}

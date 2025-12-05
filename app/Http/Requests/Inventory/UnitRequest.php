<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً لإجراء هذا الطلب.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // يجب أن يكون المستخدم مصرحاً له بإدارة الوحدات
        // نفترض وجود بوابة (Gate) أو سياسة (Policy) باسم 'manage-units'
        return Auth::check() && Auth::user()->can('manage-units');
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $unitId = $this->route('unit') ? $this->route('unit')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'name')->ignore($unitId),
            ],
            'symbol' => [
                'required',
                'string',
                'max:10',
                Rule::unique('units', 'symbol')->ignore($unitId),
            ],
            'is_base_unit' => ['boolean'],
            'base_unit_id' => [
                'nullable',
                'integer',
                'exists:units,id',
                // منع الوحدة من أن تكون هي نفسها الوحدة الأساسية
                Rule::notIn([$unitId]),
                // يجب أن تكون الوحدة الأساسية المختارة هي وحدة أساسية بالفعل
                Rule::exists('units', 'id')->where(function ($query) {
                    $query->where('is_base_unit', true);
                }),
                // إذا كانت الوحدة هي وحدة أساسية، يجب أن يكون base_unit_id فارغاً
                Rule::requiredIf(!$this->input('is_base_unit')),
                Rule::prohibitedIf($this->input('is_base_unit')),
            ],
            'conversion_factor' => [
                'required',
                'numeric',
                'min:0.0001',
                // إذا كانت الوحدة هي وحدة أساسية، يجب أن يكون معامل التحويل 1.0
                Rule::in($this->input('is_base_unit') ? [1.0] : null),
            ],
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
            'base_unit_id.required_if' => 'يجب تحديد وحدة أساسية إذا لم تكن هذه الوحدة هي الوحدة الأساسية.',
            'base_unit_id.prohibited_if' => 'لا يمكن تحديد وحدة أساسية إذا كانت هذه الوحدة هي الوحدة الأساسية.',
            'base_unit_id.exists' => 'الوحدة الأساسية المختارة غير صالحة أو ليست وحدة أساسية.',
            'conversion_factor.in' => 'يجب أن يكون معامل التحويل 1.0 للوحدات الأساسية.',
            'conversion_factor.min' => 'يجب أن يكون معامل التحويل أكبر من صفر.',
        ];
    }
}

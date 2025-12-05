<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً لإجراء هذا الطلب.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // يجب أن يكون المستخدم مصرحاً له بإدارة الفئات
        // نفترض وجود بوابة (Gate) أو سياسة (Policy) باسم 'manage-categories'
        return Auth::check() && Auth::user()->can('manage-categories');
    }

    /**
     * قواعد التحقق التي تنطبق على الطلب.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('category') ? $this->route('category')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // يجب أن يكون الاسم فريداً في جدول الفئات، باستثناء الفئة الحالية عند التحديث
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
                // منع الفئة من أن تكون هي نفسها الفئة الأب
                Rule::notIn([$categoryId]),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
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
            'name.required' => 'اسم الفئة مطلوب.',
            'name.unique' => 'اسم الفئة هذا موجود بالفعل.',
            'parent_id.exists' => 'الفئة الأب المختارة غير صالحة.',
            'parent_id.not_in' => 'لا يمكن أن تكون الفئة هي نفسها الفئة الأب.',
        ];
    }
}

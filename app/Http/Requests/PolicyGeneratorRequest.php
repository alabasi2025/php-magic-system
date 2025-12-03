<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PolicyGeneratorRequest
 *
 * طلب التحقق من صحة بيانات توليد Policy.
 * Form request for validating Policy generation data.
 *
 * @package App\Http\Requests
 * @version v3.31.0
 * @author Manus AI
 */
class PolicyGeneratorRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مصرحاً له بتقديم هذا الطلب.
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // يمكن إضافة منطق التفويض هنا
        // Authorization logic can be added here
        return true;
    }

    /**
     * الحصول على قواعد التحقق التي تنطبق على الطلب.
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // الحقول الأساسية - Basic fields
            'name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z0-9]*$/',
            'model' => 'required|string|max:255',
            'type' => 'required|in:resource,custom,role_based,ownership',

            // الأساليب المخصصة - Custom methods
            'methods' => 'nullable|array',
            'methods.*' => 'in:viewAny,view,create,update,delete,restore,forceDelete',

            // الأدوار والصلاحيات - Roles and permissions
            'roles' => 'nullable|array',
            'roles.*' => 'string|max:100',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:100',

            // خيارات إضافية - Additional options
            'ownership_field' => 'nullable|string|max:100',
            'use_responses' => 'nullable|boolean',
            'include_filters' => 'nullable|boolean',
            'guest_support' => 'nullable|boolean',
            'soft_deletes' => 'nullable|boolean',

            // خيارات الذكاء الاصطناعي - AI options
            'ai_description' => 'nullable|string|max:1000',
            'ai_enabled' => 'nullable|boolean',
        ];
    }

    /**
     * الحصول على رسائل الخطأ المخصصة.
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم Policy مطلوب. Policy name is required.',
            'name.regex' => 'يجب أن يبدأ اسم Policy بحرف كبير ويحتوي على أحرف وأرقام فقط. Policy name must start with uppercase letter and contain only letters and numbers.',
            'model.required' => 'اسم النموذج مطلوب. Model name is required.',
            'type.required' => 'نوع Policy مطلوب. Policy type is required.',
            'type.in' => 'نوع Policy غير صالح. Invalid policy type.',
            'methods.array' => 'يجب أن تكون الأساليب مصفوفة. Methods must be an array.',
            'methods.*.in' => 'أسلوب غير صالح. Invalid method.',
        ];
    }

    /**
     * الحصول على أسماء الحقول المخصصة.
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'اسم Policy / Policy Name',
            'model' => 'النموذج / Model',
            'type' => 'النوع / Type',
            'methods' => 'الأساليب / Methods',
            'roles' => 'الأدوار / Roles',
            'permissions' => 'الصلاحيات / Permissions',
            'ownership_field' => 'حقل الملكية / Ownership Field',
        ];
    }

    /**
     * تحضير البيانات للتحقق.
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // إضافة "Policy" إلى الاسم إذا لم يكن موجوداً
        // Add "Policy" suffix to name if not present
        if ($this->has('name') && !str_ends_with($this->name, 'Policy')) {
            $this->merge([
                'name' => $this->name . 'Policy',
            ]);
        }

        // تحويل القيم المنطقية
        // Convert boolean values
        $this->merge([
            'use_responses' => $this->boolean('use_responses'),
            'include_filters' => $this->boolean('include_filters'),
            'guest_support' => $this->boolean('guest_support'),
            'soft_deletes' => $this->boolean('soft_deletes'),
            'ai_enabled' => $this->boolean('ai_enabled'),
        ]);
    }
}

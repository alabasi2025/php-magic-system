<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateRoleRequest
 *
 * This Form Request handles the validation for updating an existing Role.
 * It ensures that the 'name' field is unique across all roles, except for the role
 * currently being updated.
 *
 * @package App\Http\Requests\Role
 */
class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * In a real-world application, this method would contain authorization logic,
     * such as checking if the authenticated user has the 'update-roles' permission.
     * For this task, we assume authorization is handled by middleware or policy.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Assuming authorization is handled by a policy or middleware.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * The 'name' field uses the Rule::unique helper to ensure uniqueness in the 'roles' table,
     * while ignoring the ID of the role currently being updated. The role ID is retrieved
     * from the route parameters.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Assuming the route parameter for the role is named 'role' and contains the Role model instance or its ID.
        // If the route is defined as Route::put('roles/{role}', ...), Laravel automatically resolves the model.
        $roleId = $this->route('role');

        // If the route parameter is a model instance, we need to get its primary key.
        if (is_object($roleId) && method_exists($roleId, 'getKey')) {
            $roleId = $roleId->getKey();
        }

        return [
            // The 'name' must be a string, required, max 50 characters, and unique in the 'roles' table,
            // ignoring the current role's ID.
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],

            // The 'display_name' must be a string, required, and max 100 characters.
            'display_name' => [
                'required',
                'string',
                'max:100',
            ],

            // The 'description' is optional, must be a string, and max 255 characters.
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * This provides custom, user-friendly messages for specific validation failures.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'The role name has already been taken. Please choose a different name.',
            'name.required' => 'A unique role name is required.',
            'display_name.required' => 'A display name for the role is required.',
        ];
    }
}
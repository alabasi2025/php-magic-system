<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * In a production environment, this method should contain logic to check
     * if the authenticated user has the necessary permissions (e.g., 'create-role').
     * For this task, we return true as authorization is assumed to be handled elsewhere.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Example: return $this->user()->can('create-role');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // The 'name' is the system-level, unique identifier for the role (e.g., 'admin', 'editor').
            'name' => [
                'required',
                'string',
                'max:255',
                // Ensures the name is unique in the 'roles' table.
                Rule::unique('roles', 'name'),
            ],

            // The 'display_name' is the human-readable title for the role (e.g., 'Administrator').
            'display_name' => [
                'required',
                'string',
                'max:255',
            ],

            // The 'description' provides a detailed explanation of the role's purpose and permissions.
            'description' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'The role name is already taken. Please choose a different name.',
        ];
    }
}
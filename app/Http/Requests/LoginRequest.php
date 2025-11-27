<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // A login request should generally be authorized for all guests.
        // In a typical Laravel application, this should return true.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Define the validation rules as per the task requirements.
        return [
            // The 'email' field is required and must be a valid email format.
            'email' => ['required', 'email'],
            
            // The 'password' field is required and must have a minimum length of 6 characters.
            'password' => ['required', 'min:6'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        // Optional: Custom error messages for better user experience.
        return [
            'email.required' => 'The email address is required for login.',
            'email.email' => 'The email address must be a valid format.',
            'password.required' => 'The password is required for login.',
            'password.min' => 'The password must be at least 6 characters long.',
        ];
    }
}
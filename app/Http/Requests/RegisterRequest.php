<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Class RegisterRequest
 *
 * Handles the validation logic for user registration requests.
 * Ensures all required fields (name, email, password, phone) meet the specified criteria
 * before processing the registration.
 *
 * @package App\Http\Requests
 */
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * For registration, we typically allow all guests to make this request,
     * so we return true. Authorization logic is usually handled by gates/policies
     * for protected resources, but not for a public registration endpoint.
     *
     * @return bool
     */
    public function authorize(): bool
    {
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
            // Validate the user's full name. It must be present and a string.
            'name' => ['required', 'string', 'max:255'],

            // Validate the email address.
            // It must be present, a valid email format, and unique in the 'users' table.
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

            // Validate the password.
            // It must be present, confirmed (requires a 'password_confirmation' field),
            // and meet the minimum length requirement of 8 characters.
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    // Optional: Add more security rules for production readiness
                    // ->mixedCase()
                    // ->numbers()
                    // ->symbols()
            ],

            // Validate the phone number.
            // It must be present and a string, with a reasonable maximum length.
            'phone' => ['required', 'string', 'max:15'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * This is optional but good practice for production-ready code.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
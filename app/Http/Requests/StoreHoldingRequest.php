<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreHoldingRequest",
 *     title="Store Holding Request",
 *     required={"name", "code"},
 *     @OA\Property(property="name", type="string", example="Acme Corp Holding", description="The name of the holding company."),
 *     @OA\Property(property="code", type="string", example="ACME", description="A unique code for the holding company."),
 *     @OA\Property(property="logo", type="string", format="binary", description="The logo image file for the holding company.")
 * )
 */
class StoreHoldingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * In a production environment, this should check for the user's permission
     * to create a new Holding record.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Assuming authorization logic is handled elsewhere (e.g., in a middleware or policy)
        // For this task, we return true to allow the request to proceed.
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
            // 1. 'name' is required.
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            // 2. 'code' is required and must be unique in the 'holdings' table.
            'code' => [
                'required',
                'string',
                'max:50',
                // Ensure the code is unique in the 'holdings' table.
                Rule::unique('holdings', 'code'),
            ],

            // 3. 'logo' is optional but must be a valid image file.
            'logo' => [
                'nullable',
                'image',        // Must be an image file (jpeg, png, bmp, gif, svg, webp).
                'mimes:jpeg,png,jpg,gif,svg,webp', // Specific allowed mime types.
                'max:2048',     // Maximum file size of 2MB (2048 kilobytes).
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
            'name.required' => 'The holding name is required.',
            'code.unique' => 'This holding code is already in use.',
            'logo.image' => 'The logo must be a valid image file.',
            'logo.max' => 'The logo file size must not exceed 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     * This can be used to clean, normalize, or add data before validation runs.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Example: Normalize the 'code' to uppercase before validation.
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper($this->input('code')),
            ]);
        }
    }
}
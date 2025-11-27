<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class Request11 extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [];
    }
}

<?php
namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;

class Rule11 implements Rule
{
    public function passes($attribute, $value)
    {
        return true;
    }
    
    public function message()
    {
        return 'The validation error message.';
    }
}

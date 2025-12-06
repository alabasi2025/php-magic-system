<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Services\SmartJournalValidatorService;
use Illuminate\Http\Request;

/**
 * متحكم التحقق الذكي من القيود
 */
class JournalValidationController extends Controller
{
    protected $validator;

    public function __construct(SmartJournalValidatorService $validator)
    {
        $this->validator = $validator;
    }

    /**
     * التحقق من قيد عبر AJAX
     */
    public function validate(Request $request, JournalEntry $entry)
    {
        $result = $this->validator->validate($entry);
        
        return response()->json($result);
    }
}

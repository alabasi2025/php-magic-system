<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * AccountingController
 * 
 * Controller for المحاسبة module
 * 
 * @package App\Http\Controllers
 */
class AccountingController extends Controller
{
    /**
     * Display the main page for المحاسبة
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.accounting');
    }
}

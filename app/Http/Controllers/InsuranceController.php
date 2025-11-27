<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * InsuranceController
 * 
 * Controller for التأمين الطبي module
 * 
 * @package App\Http\Controllers
 */
class InsuranceController extends Controller
{
    /**
     * Display the main page for التأمين الطبي
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.insurance');
    }
}

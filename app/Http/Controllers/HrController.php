<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * HrController
 * 
 * Controller for الموارد البشرية module
 * 
 * @package App\Http\Controllers
 */
class HrController extends Controller
{
    /**
     * Display the main page for الموارد البشرية
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.hr');
    }
}

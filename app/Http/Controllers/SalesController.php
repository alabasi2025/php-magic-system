<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * SalesController
 * 
 * Controller for المبيعات module
 * 
 * @package App\Http\Controllers
 */
class SalesController extends Controller
{
    /**
     * Display the main page for المبيعات
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.sales');
    }
}

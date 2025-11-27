<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * LoyaltyController
 * 
 * Controller for نقاط الولاء module
 * 
 * @package App\Http\Controllers
 */
class LoyaltyController extends Controller
{
    /**
     * Display the main page for نقاط الولاء
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.loyalty');
    }
}

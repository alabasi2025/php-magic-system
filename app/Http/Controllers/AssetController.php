<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * AssetController
 * 
 * Controller for الأصول module
 * 
 * @package App\Http\Controllers
 */
class AssetController extends Controller
{
    /**
     * Display the main page for الأصول
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.asset');
    }
}

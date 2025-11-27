<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * ManufacturingController
 * 
 * Controller for التصنيع module
 * 
 * @package App\Http\Controllers
 */
class ManufacturingController extends Controller
{
    /**
     * Display the main page for التصنيع
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.manufacturing');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * GeneController
 * 
 * Controller for الجينات module
 * 
 * @package App\Http\Controllers
 */
class GeneController extends Controller
{
    /**
     * Display the main page for الجينات
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.gene');
    }
}

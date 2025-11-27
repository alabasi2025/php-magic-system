<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * PurchaseController
 * 
 * Controller for المشتريات module
 * 
 * @package App\Http\Controllers
 */
class PurchaseController extends Controller
{
    /**
     * Display the main page for المشتريات
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.purchase');
    }
}

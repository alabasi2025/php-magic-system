<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * InventoryController
 * 
 * Controller for المخزون module
 * 
 * @package App\Http\Controllers
 */
class InventoryController extends Controller
{
    /**
     * Display the main page for المخزون
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.inventory');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * ProjectController
 * 
 * Controller for المشاريع module
 * 
 * @package App\Http\Controllers
 */
class ProjectController extends Controller
{
    /**
     * Display the main page for المشاريع
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.project');
    }
}

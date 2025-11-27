<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * UsersController
 * 
 * Controller for المستخدمين
 * 
 * @package App\Http\Controllers
 */
class UsersController extends Controller
{
    /**
     * Display the main page for المستخدمين
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('modules.users');
    }
    
    /**
     * Store a new resource
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // TODO: Implement store logic
        return response()->json([
            'success' => true,
            'message' => 'تم الإنشاء بنجاح'
        ]);
    }
    
    /**
     * Update an existing resource
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // TODO: Implement update logic
        return response()->json([
            'success' => true,
            'message' => 'تم التحديث بنجاح'
        ]);
    }
    
    /**
     * Delete a resource
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // TODO: Implement delete logic
        return response()->json([
            'success' => true,
            'message' => 'تم الحذف بنجاح'
        ]);
    }
}

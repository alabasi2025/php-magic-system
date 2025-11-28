<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * نظام سندات الصرف (Payment Vouchers) Controller - Task 2400
 * Category: Integration
 */
class نظامسنداتالصرفPaymentVouchersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Created successfully'
        ], 201);
    }
}

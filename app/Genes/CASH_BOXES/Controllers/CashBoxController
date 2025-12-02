<?php

namespace App\Genes\CASH_BOXES\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Genes\CASH_BOXES\Services\CashBoxService;

class CashBoxController extends Controller
{
    protected $cashBoxService;

    /**
     * CashBoxController constructor.
     *
     * @param CashBoxService $cashBoxService
     */
    public function __construct(CashBoxService $cashBoxService)
    {
        $this->cashBoxService = $cashBoxService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Placeholder: Fetch all cash boxes
        $data = $this->cashBoxService->getAllCashBoxes($request->all());
        return response()->json(['status' => 'success', 'message' => 'Cash boxes list retrieved successfully', 'data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        // Placeholder: Return necessary data for creation form
        return response()->json(['status' => 'success', 'message' => 'Cash box creation form data']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Placeholder: Create a new cash box
        $cashBox = $this->cashBoxService->createCashBox($request->all());
        return response()->json(['status' => 'success', 'message' => 'Cash box created successfully', 'data' => $cashBox], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        // Placeholder: Fetch cash box for editing
        $cashBox = $this->cashBoxService->findCashBox($id);
        if (!$cashBox) {
            return response()->json(['status' => 'error', 'message' => 'Cash box not found'], 404);
        }
        return response()->json(['status' => 'success', 'message' => 'Cash box edit form data', 'data' => $cashBox]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Placeholder: Update the cash box
        $cashBox = $this->cashBoxService->updateCashBox($id, $request->all());
        if (!$cashBox) {
            return response()->json(['status' => 'error', 'message' => 'Cash box not found or update failed'], 404);
        }
        return response()->json(['status' => 'success', 'message' => 'Cash box updated successfully', 'data' => $cashBox]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Placeholder: Delete the cash box
        $deleted = $this->cashBoxService->deleteCashBox($id);
        if (!$deleted) {
            return response()->json(['status' => 'error', 'message' => 'Cash box not found or deletion failed'], 404);
        }
        return response()->json(['status' => 'success', 'message' => 'Cash box deleted successfully'], 204);
    }

    /**
     * Add a transaction to the specified cash box.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTransaction(Request $request, $id)
    {
        // Placeholder: Add a transaction
        $transaction = $this->cashBoxService->addTransaction($id, $request->all());
        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Failed to add transaction'], 400);
        }
        return response()->json(['status' => 'success', 'message' => 'Transaction added successfully', 'data' => $transaction], 201);
    }

    /**
     * Get transactions for the specified cash box.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactions($id)
    {
        // Placeholder: Get transactions
        $transactions = $this->cashBoxService->getCashBoxTransactions($id);
        if (is_null($transactions)) {
            return response()->json(['status' => 'error', 'message' => 'Cash box not found'], 404);
        }
        return response()->json(['status' => 'success', 'message' => 'Transactions retrieved successfully', 'data' => $transactions]);
    }
}

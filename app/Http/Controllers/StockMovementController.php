<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\StockBalance;
use App\Http\Requests\StockMovementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource (Stock Movements).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Basic pagination and eager loading for related data
        // Assuming 'product', 'sourceLocation', and 'destinationLocation' are defined relationships in StockMovement model
        $movements = StockMovement::with(['product', 'sourceLocation', 'destinationLocation'])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($movements);
    }

    /**
     * Store a newly created resource in storage and update stock balances.
     *
     * @param StockMovementRequest $request
     * @return JsonResponse
     */
    public function store(StockMovementRequest $request): JsonResponse
    {
        $data = $request->validated();
        $movementType = $data['movement_type'];
        $quantity = (float) $data['quantity'];
        $productId = $data['product_id'];
        $sourceLocationId = $data['source_location_id'] ?? null;
        $destinationLocationId = $data['destination_location_id'] ?? null;

        // Use a database transaction to ensure atomicity for stock updates
        try {
            DB::beginTransaction();

            // 1. Record the Stock Movement (Transaction History)
            $movement = StockMovement::create($data);

            // 2. Update Stock Balances based on movement type
            if ($movementType === 'in' || ($movementType === 'adjustment' && $destinationLocationId)) {
                // 'in' or positive 'adjustment' affects the destination location
                $locationId = $destinationLocationId;
                if ($locationId) {
                    $this->updateStockBalance($productId, $locationId, $quantity);
                }
            }

            if ($movementType === 'out' || ($movementType === 'adjustment' && $sourceLocationId)) {
                // 'out' or negative 'adjustment' affects the source location
                $locationId = $sourceLocationId;
                if ($locationId) {
                    // Check if sufficient stock exists before decrementing
                    $currentBalance = StockBalance::where('product_id', $productId)
                        ->where('location_id', $locationId)
                        ->value('quantity') ?? 0;

                    if ($currentBalance < $quantity) {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Insufficient stock at location to complete the movement.',
                            'current_stock' => $currentBalance,
                        ], 409); // 409 Conflict
                    }

                    $this->updateStockBalance($productId, $locationId, -$quantity);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Stock movement recorded and balances updated successfully.',
                'movement' => $movement->load(['product', 'sourceLocation', 'destinationLocation']),
            ], 201); // 201 Created

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Log::error('Stock Movement failed: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => 'An error occurred while processing the stock movement.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper function to update or create a StockBalance record.
     *
     * @param int $productId
     * @param int $locationId
     * @param float $quantityChange (positive for increase, negative for decrease)
     * @return StockBalance
     */
    protected function updateStockBalance(int $productId, int $locationId, float $quantityChange): StockBalance
    {
        // Find the balance or initialize a new one
        $balance = StockBalance::firstOrNew([
            'product_id' => $productId,
            'location_id' => $locationId,
        ]);

        // Update the quantity
        $balance->quantity += $quantityChange;

        // Ensure quantity does not go below zero (should be caught by the transaction check, but good for safety)
        $balance->quantity = max(0, $balance->quantity);

        $balance->save();

        return $balance;
    }

    /**
     * Display the specified resource.
     *
     * @param StockMovement $stockMovement
     * @return JsonResponse
     */
    public function show(StockMovement $stockMovement): JsonResponse
    {
        // Eager load relationships for a complete view
        $stockMovement->load(['product', 'sourceLocation', 'destinationLocation']);

        return response()->json($stockMovement);
    }

    /**
     * Update the specified resource in storage.
     *
     * NOTE: Updating a stock movement record after it has been processed is generally discouraged
     * as it can lead to inconsistencies in stock balances. For a production system, this method
     * should ideally be restricted or implement complex reversal/correction logic.
     * For CRUD completeness, a basic update is provided, but it DOES NOT update stock balances.
     *
     * @param StockMovementRequest $request
     * @param StockMovement $stockMovement
     * @return JsonResponse
     */
    public function update(StockMovementRequest $request, StockMovement $stockMovement): JsonResponse
    {
        // Only update the movement record itself, without affecting stock balances.
        // A proper system would require a separate 'Stock Correction' or 'Reversal' movement.
        $stockMovement->update($request->validated());

        return response()->json([
            'message' => 'Stock movement record updated successfully (Note: Stock balances were NOT affected).',
            'movement' => $stockMovement->load(['product', 'sourceLocation', 'destinationLocation']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * NOTE: Deleting a stock movement record is highly discouraged in a production environment
     * as it destroys transaction history and causes stock balance inconsistencies.
     * This method is provided for CRUD completeness but should be heavily restricted.
     * It DOES NOT reverse the stock balance change.
     *
     * @param StockMovement $stockMovement
     * @return JsonResponse
     */
    public function destroy(StockMovement $stockMovement): JsonResponse
    {
        // In a real-world scenario, this would likely be a soft delete or a restricted operation.
        $stockMovement->delete();

        return response()->json([
            'message' => 'Stock movement record deleted successfully (Note: Stock balances were NOT affected).',
        ], 204); // 204 No Content
    }
}
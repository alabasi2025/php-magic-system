<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseLocation; // Assuming a WarehouseLocation model exists for location management
use App\Http\Requests\WarehouseStoreRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class WarehouseController
 * Manages CRUD operations for Warehouses, along with inventory summary and location management.
 * Follows Laravel 12 best practices and uses proper naming conventions.
 */
class WarehouseController extends Controller
{
    /**
     * Display a listing of the warehouses.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Fetch all warehouses with pagination
        $warehouses = Warehouse::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('search') . '%')
                      ->orWhere('code', 'like', '%' . $request->input('search') . '%');
            })
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'message' => 'Warehouses retrieved successfully.',
            'data' => $warehouses,
        ]);
    }

    /**
     * Store a newly created warehouse in storage.
     *
     * @param WarehouseStoreRequest $request
     * @return JsonResponse
     */
    public function store(WarehouseStoreRequest $request): JsonResponse
    {
        try {
            // Validation is handled by WarehouseStoreRequest
            $warehouse = Warehouse::create($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Warehouse created successfully.',
                'data' => $warehouse,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Warehouse creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create warehouse.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified warehouse.
     *
     * @param Warehouse $warehouse
     * @return JsonResponse
     */
    public function show(Warehouse $warehouse): JsonResponse
    {
        // Load related locations for a complete view
        $warehouse->load('locations');

        return response()->json([
            'status' => 'success',
            'message' => 'Warehouse retrieved successfully.',
            'data' => $warehouse,
        ]);
    }

    /**
     * Update the specified warehouse in storage.
     *
     * @param WarehouseUpdateRequest $request
     * @param Warehouse $warehouse
     * @return JsonResponse
     */
    public function update(WarehouseUpdateRequest $request, Warehouse $warehouse): JsonResponse
    {
        try {
            // Validation is handled by WarehouseUpdateRequest
            $warehouse->update($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Warehouse updated successfully.',
                'data' => $warehouse,
            ]);
        } catch (\Exception $e) {
            Log::error('Warehouse update failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update warehouse.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified warehouse from storage.
     *
     * @param Warehouse $warehouse
     * @return JsonResponse
     */
    public function destroy(Warehouse $warehouse): JsonResponse
    {
        try {
            // Implement logic to check for existing inventory or transactions before deletion
            // For production-ready code, consider soft deletes or throwing an exception if dependencies exist.
            if ($warehouse->inventory()->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete warehouse with existing inventory records.',
                ], 409); // Conflict
            }

            $warehouse->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Warehouse deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Warehouse deletion failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete warehouse.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a summary of the inventory for the specified warehouse.
     *
     * @param Warehouse $warehouse
     * @return JsonResponse
     */
    public function inventorySummary(Warehouse $warehouse): JsonResponse
    {
        // This is a placeholder for a complex inventory summary logic.
        // It assumes a relationship 'inventory' exists on the Warehouse model.
        // In a real system, this would involve complex joins and aggregations.

        // Example: Group by item and sum the quantity
        $summary = $warehouse->inventory()
            ->select('item_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('item_id')
            ->with('item') // Assuming an 'item' relationship on the inventory model
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Inventory summary retrieved successfully.',
            'warehouse' => $warehouse->name,
            'data' => $summary,
        ]);
    }

    /**
     * Add a new location (e.g., shelf, aisle) to the specified warehouse.
     *
     * @param Request $request
     * @param Warehouse $warehouse
     * @return JsonResponse
     */
    public function addLocation(Request $request, Warehouse $warehouse): JsonResponse
    {
        // Simple validation for the new location data
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouse_locations,code',
            'description' => 'nullable|string',
        ]);

        try {
            // Create the new location associated with the warehouse
            $location = $warehouse->locations()->create([
                'name' => $request->input('name'),
                'code' => $request->input('code'),
                'description' => $request->input('description'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Location added successfully to warehouse.',
                'data' => $location,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to add location: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add location.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove a location from the specified warehouse.
     *
     * @param Warehouse $warehouse
     * @param WarehouseLocation $location
     * @return JsonResponse
     */
    public function removeLocation(Warehouse $warehouse, WarehouseLocation $location): JsonResponse
    {
        // Ensure the location belongs to the warehouse (Route Model Binding should handle this if configured,
        // but an explicit check is safer for business logic).
        if ($location->warehouse_id !== $warehouse->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'The specified location does not belong to this warehouse.',
            ], 403); // Forbidden
        }

        try {
            // Implement logic to check if the location is empty before deletion
            // Assuming a relationship 'inventoryItems' exists on the WarehouseLocation model.
            if ($location->inventoryItems()->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot remove location with existing inventory items.',
                ], 409); // Conflict
            }

            $location->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Location removed successfully from warehouse.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to remove location: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove location.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
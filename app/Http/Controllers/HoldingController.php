<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Http\Requests\StoreHoldingRequest;
use App\Http\Requests\UpdateHoldingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

/**
 * @class HoldingController
 * @package App\Http\Controllers
 *
 * Controller for managing Holding resources and providing dashboard statistics.
 * Follows Laravel 12 best practices for production-ready code.
 */
class HoldingController extends Controller
{
    /**
     * Display a listing of the Holding resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Apply filtering, sorting, and pagination
            $holdings = Holding::query()
                ->when($request->has('search'), function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->input('search') . '%')
                          ->orWhere('code', 'like', '%' . $request->input('search') . '%');
                })
                ->orderBy($request->input('sort_by', 'created_at'), $request->input('sort_direction', 'desc'))
                ->paginate($request->input('per_page', 15));

            // Return a successful JSON response with the paginated data
            return response()->json([
                'status' => 'success',
                'message' => 'Holdings retrieved successfully.',
                'data' => $holdings,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("HoldingController@index: Failed to retrieve holdings. Error: " . $e->getMessage());

            // Return a JSON response with an error status
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching holdings.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created Holding resource in storage.
     *
     * @param StoreHoldingRequest $request
     * @return JsonResponse
     */
    public function store(StoreHoldingRequest $request): JsonResponse
    {
        try {
            // The request is already validated by StoreHoldingRequest
            $holding = Holding::create($request->validated());

            // Return a successful JSON response with the created resource
            return response()->json([
                'status' => 'success',
                'message' => 'Holding created successfully.',
                'data' => $holding,
            ], 201);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("HoldingController@store: Failed to create holding. Error: " . $e->getMessage());

            // Return a JSON response with an error status
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the holding.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified Holding resource.
     *
     * @param Holding $holding
     * @return JsonResponse
     */
    public function show(Holding $holding): JsonResponse
    {
        // The model is automatically resolved by Laravel's Route Model Binding.
        try {
            // Load any necessary relationships (e.g., 'companies')
            $holding->load('companies');

            // Return a successful JSON response with the resource
            return response()->json([
                'status' => 'success',
                'message' => 'Holding retrieved successfully.',
                'data' => $holding,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("HoldingController@show: Failed to retrieve holding ID: {$holding->id}. Error: " . $e->getMessage());

            // Return a JSON response with an error status
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the holding details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified Holding resource in storage.
     *
     * @param UpdateHoldingRequest $request
     * @param Holding $holding
     * @return JsonResponse
     */
    public function update(UpdateHoldingRequest $request, Holding $holding): JsonResponse
    {
        try {
            // The request is already validated by UpdateHoldingRequest
            $holding->update($request->validated());

            // Return a successful JSON response with the updated resource
            return response()->json([
                'status' => 'success',
                'message' => 'Holding updated successfully.',
                'data' => $holding,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("HoldingController@update: Failed to update holding ID: {$holding->id}. Error: " . $e->getMessage());

            // Return a JSON response with an error status
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the holding.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified Holding resource from storage.
     *
     * @param Holding $holding
     * @return JsonResponse
     */
    public function destroy(Holding $holding): JsonResponse
    {
        try {
            // Check for related records before deletion (e.g., if it has companies)
            if ($holding->companies()->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete holding because it has associated companies.',
                ], 409); // Conflict
            }

            $holding->delete();

            // Return a successful JSON response with no content
            return response()->json([
                'status' => 'success',
                'message' => 'Holding deleted successfully.',
                'data' => null,
            ], 204);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("HoldingController@destroy: Failed to delete holding ID: {$holding->id}. Error: " . $e->getMessage());

            // Return a JSON response with an error status
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the holding.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retrieve key statistics for the dashboard.
     *
     * @return JsonResponse
     */
    public function dashboard(): JsonResponse
    {
        try {
            // 1. Total number of holdings
            $totalHoldings = Holding::count();

            // 2. Total number of associated companies (assuming a 'companies' table with a 'holding_id' foreign key)
            $totalCompanies = DB::table('companies')->count();

            // 3. Holding with the most associated companies
            $topHolding = Holding::withCount('companies')
                ->orderByDesc('companies_count')
                ->first();

            // 4. Average age of holdings (assuming 'created_at' is a good proxy for age)
            $averageAgeInDays = DB::table('holdings')
                ->select(DB::raw('AVG(DATEDIFF(NOW(), created_at)) as avg_days'))
                ->value('avg_days');

            $statistics = [
                'total_holdings' => $totalHoldings,
                'total_companies' => $totalCompanies,
                'top_holding_by_companies' => $topHolding ? [
                    'id' => $topHolding->id,
                    'name' => $topHolding->name,
                    'companies_count' => $topHolding->companies_count,
                ] : null,
                'average_holding_age_days' => round($averageAgeInDays ?? 0, 2),
            ];

            // Return a successful JSON response with the statistics
            return response()->json([
                'status' => 'success',
                'message' => 'Dashboard statistics retrieved successfully.',
                'data' => $statistics,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("HoldingController@dashboard: Failed to retrieve dashboard statistics. Error: " . $e->getMessage());

            // Return a JSON response with an error status
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching dashboard statistics.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
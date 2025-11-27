<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CostCenterStoreRequest;
use App\Http\Requests\CostCenterUpdateRequest;

/**
 * Class CostCenterController
 * Handles CRUD operations, budget tracking, and allocation management for Cost Centers.
 */
class CostCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Retrieve all cost centers with their calculated remaining budget
        $costCenters = CostCenter::select([
            'id',
            'name',
            'description',
            'budget_amount',
            'allocated_amount',
            'is_active',
            // Calculate remaining budget directly in the query for efficiency
            DB::raw('budget_amount - allocated_amount AS remaining_budget')
        ])->latest()->paginate(15);

        return response()->json([
            'status' => 'success',
            'message' => 'Cost centers retrieved successfully.',
            'data' => $costCenters,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CostCenterStoreRequest $request
     * @return JsonResponse
     */
    public function store(CostCenterStoreRequest $request): JsonResponse
    {
        // The request is already validated.
        $data = $request->validated();

        // Ensure allocated_amount is 0 on creation, and remaining_budget is calculated.
        $data['allocated_amount'] = 0.00;
        // The remaining_budget column will be automatically calculated by the model's logic or database trigger if one exists.
        // For simplicity and adherence to Laravel practices, we rely on the model's accessor/mutator or a calculated field.

        $costCenter = CostCenter::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Cost center created successfully.',
            'data' => $costCenter,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param CostCenter $costCenter
     * @return JsonResponse
     */
    public function show(CostCenter $costCenter): JsonResponse
    {
        // Append the calculated remaining_budget attribute
        $costCenter->append('remaining_budget');

        return response()->json([
            'status' => 'success',
            'message' => 'Cost center details retrieved successfully.',
            'data' => $costCenter,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CostCenterUpdateRequest $request
     * @param CostCenter $costCenter
     * @return JsonResponse
     */
    public function update(CostCenterUpdateRequest $request, CostCenter $costCenter): JsonResponse
    {
        $data = $request->validated();

        // Check if the new budget_amount is less than the already allocated_amount
        if (isset($data['budget_amount']) && $data['budget_amount'] < $costCenter->allocated_amount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot reduce budget amount below the already allocated amount (' . $costCenter->allocated_amount . ').',
            ], 422);
        }

        $costCenter->update($data);

        // Append the calculated remaining_budget attribute
        $costCenter->append('remaining_budget');

        return response()->json([
            'status' => 'success',
            'message' => 'Cost center updated successfully.',
            'data' => $costCenter,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CostCenter $costCenter
     * @return JsonResponse
     */
    public function destroy(CostCenter $costCenter): JsonResponse
    {
        // Security check: Prevent deletion if there are any allocations or allocated_amount > 0
        if ($costCenter->allocated_amount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete cost center with existing allocations or allocated amount.',
            ], 409); // 409 Conflict
        }

        $costCenter->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cost center deleted successfully.',
            'data' => null,
        ], 204); // 204 No Content
    }

    /**
     * Get the budget tracking details for the specified cost center.
     *
     * @param CostCenter $costCenter
     * @return JsonResponse
     */
    public function trackBudget(CostCenter $costCenter): JsonResponse
    {
        // Append the calculated remaining_budget attribute
        $costCenter->append('remaining_budget');

        // Calculate usage percentage
        $usagePercentage = ($costCenter->budget_amount > 0)
            ? round(($costCenter->allocated_amount / $costCenter->budget_amount) * 100, 2)
            : 0;

        return response()->json([
            'status' => 'success',
            'message' => 'Budget tracking details retrieved.',
            'data' => [
                'cost_center_id' => $costCenter->id,
                'name' => $costCenter->name,
                'budget_amount' => $costCenter->budget_amount,
                'allocated_amount' => $costCenter->allocated_amount,
                'remaining_budget' => $costCenter->remaining_budget,
                'usage_percentage' => $usagePercentage,
                // In a real application, you would load related allocations here:
                // 'recent_allocations' => $costCenter->allocations()->latest()->take(5)->get(),
            ],
        ]);
    }

    /**
     * Manages the allocation of funds to the cost center.
     * This simulates a transaction that increases the allocated_amount.
     *
     * @param Request $request
     * @param CostCenter $costCenter
     * @return JsonResponse
     */
    public function allocate(Request $request, CostCenter $costCenter): JsonResponse
    {
        // 1. Validation for the allocation amount
        $request->validate([
            'allocation_amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $allocationAmount = $request->input('allocation_amount');

        // 2. Budget check: Ensure the allocation does not exceed the remaining budget
        $remainingBudget = $costCenter->budget_amount - $costCenter->allocated_amount;

        if ($allocationAmount > $remainingBudget) {
            return response()->json([
                'status' => 'error',
                'message' => 'Allocation amount exceeds the remaining budget of ' . $remainingBudget . '.',
            ], 422);
        }

        // 3. Perform the allocation transaction
        DB::beginTransaction();
        try {
            // Update the allocated amount
            $costCenter->allocated_amount += $allocationAmount;
            $costCenter->save();

            // In a real system, you would create an 'Allocation' record here:
            /*
            Allocation::create([
                'cost_center_id' => $costCenter->id,
                'amount' => $allocationAmount,
                'description' => $request->input('description'),
                'user_id' => auth()->id(), // Assuming authentication
            ]);
            */

            DB::commit();

            // Append the calculated remaining_budget attribute
            $costCenter->append('remaining_budget');

            return response()->json([
                'status' => 'success',
                'message' => 'Funds successfully allocated to cost center.',
                'data' => $costCenter,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            \Log::error('Cost Center Allocation Failed: ' . $e->getMessage(), ['cost_center_id' => $costCenter->id]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process allocation due to a server error.',
            ], 500);
        }
    }
}
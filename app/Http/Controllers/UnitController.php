<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Location; // Assuming a Location model exists for location management
use App\Http\Requests\UnitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * UnitController
 * Handles CRUD operations, hierarchy display, and location management for organizational units.
 * Follows Laravel 12 best practices, including proper validation and error handling.
 */
class UnitController extends Controller
{
    /**
     * Display a listing of the resource, including hierarchy.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Fetch all units. Eager load parent and location for display.
            // Order by parent_id to help with hierarchy display on the frontend.
            $units = Unit::with(['parent', 'location'])
                ->orderBy('parent_id')
                ->orderBy('name')
                ->get();

            // Simple transformation to build a hierarchical structure (optional, can be done on frontend)
            $hierarchy = $this->buildUnitHierarchy($units);

            return response()->json([
                'status' => 'success',
                'message' => 'Units retrieved successfully.',
                'data' => $hierarchy,
            ]);
        } catch (\Exception $e) {
            Log::error("Error retrieving units: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve units.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\UnitRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UnitRequest $request)
    {
        // Validation is handled by UnitRequest
        try {
            $unit = Unit::create($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Unit created successfully.',
                'data' => $unit->load(['parent', 'location']),
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error creating unit: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create unit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Unit $unit)
    {
        try {
            // Eager load relationships for a complete view
            return response()->json([
                'status' => 'success',
                'message' => 'Unit retrieved successfully.',
                'data' => $unit->load(['parent', 'children', 'location']),
            ]);
        } catch (\Exception $e) {
            Log::error("Error retrieving unit {$unit->id}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Unit not found or failed to retrieve.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UnitRequest $request
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UnitRequest $request, Unit $unit)
    {
        // Validation is handled by UnitRequest
        try {
            // Prevent a unit from being its own parent
            if ($request->input('parent_id') == $unit->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'A unit cannot be its own parent.',
                ], 422);
            }

            // Prevent setting a child unit as a parent (to avoid circular dependency)
            // NOTE: This requires a `isDescendantOf` method on the Unit model, which is a common pattern for hierarchical models.
            // Assuming Unit model has this method or a similar trait.
            if ($request->filled('parent_id') && method_exists($unit, 'isDescendantOf') && $unit->isDescendantOf($request->input('parent_id'))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot set a descendant unit as the parent.',
                ], 422);
            }

            $unit->update($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Unit updated successfully.',
                'data' => $unit->load(['parent', 'location']),
            ]);
        } catch (\Exception $e) {
            Log::error("Error updating unit {$unit->id}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update unit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Unit $unit)
    {
        try {
            // Check for associated children (hierarchy constraint)
            if ($unit->children()->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete unit. It has associated child units.',
                ], 409); // 409 Conflict
            }

            // In a real-world scenario, you would also check for associated operational data (e.g., employees, transactions)
            // if ($unit->employees()->exists() || $unit->transactions()->exists()) { ... }

            $unit->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Unit deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error deleting unit {$unit->id}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete unit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a list of all available locations for selection.
     * This is part of the "location management" requirement.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocations()
    {
        try {
            // Assuming Location model exists and has 'id' and 'name' fields
            $locations = Location::select('id', 'name')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Locations retrieved successfully.',
                'data' => $locations,
            ]);
        } catch (\Exception $e) {
            Log::error("Error retrieving locations: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve locations.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper function to build a nested hierarchy from a flat collection of units.
     *
     * @param \Illuminate\Support\Collection $units
     * @param int|null $parentId
     * @return array
     */
    protected function buildUnitHierarchy($units, $parentId = null): array
    {
        $branch = [];

        foreach ($units as $unit) {
            if ($unit->parent_id === $parentId) {
                $children = $this->buildUnitHierarchy($units, $unit->id);

                // Convert to array to add 'children' key
                $unitArray = $unit->toArray();
                if ($children) {
                    $unitArray['children'] = $children;
                } else {
                    $unitArray['children'] = [];
                }

                $branch[] = $unitArray;
            }
        }

        return $branch;
    }
}
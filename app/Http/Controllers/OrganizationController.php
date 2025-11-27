<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Class OrganizationController
 * Handles CRUD operations for Organization resources, including filtering by holding_id
 * and managing the organization's status.
 */
class OrganizationController extends Controller
{
    /**
     * Display a listing of the organizations.
     * Supports filtering by holding_id via query parameter.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Start building the query for organizations
        $query = Organization::query();

        // 1. Filtering by holding_id
        if ($request->has('holding_id')) {
            $holdingId = $request->input('holding_id');

            // Validate the holding_id input
            $request->validate([
                // Assuming 'holdings' table exists for validation
                'holding_id' => 'required|integer|exists:holdings,id',
            ]);

            // Apply the filter
            $query->where('holding_id', $holdingId);
        }

        // 2. Pagination and fetching the results
        // Using a reasonable default pagination size
        $organizations = $query->paginate(15);

        // Return a successful JSON response
        return response()->json([
            'status' => 'success',
            'message' => 'Organizations retrieved successfully.',
            'data' => $organizations,
        ]);
    }

    /**
     * Store a newly created organization in storage.
     *
     * @param StoreOrganizationRequest $request
     * @return JsonResponse
     */
    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        try {
            // The request is already validated by StoreOrganizationRequest
            // The validated data is used to create a new Organization record
            $organization = Organization::create($request->validated());

            // Return a successful JSON response with the created resource
            return response()->json([
                'status' => 'success',
                'message' => 'Organization created successfully.',
                'data' => $organization,
            ], 201); // 201 Created status code

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Organization creation failed: ' . $e->getMessage());

            // Return an error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create organization.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified organization.
     * Uses Route Model Binding to automatically inject the Organization instance.
     *
     * @param Organization $organization
     * @return JsonResponse
     */
    public function show(Organization $organization): JsonResponse
    {
        // Return a successful JSON response with the resource
        return response()->json([
            'status' => 'success',
            'message' => 'Organization retrieved successfully.',
            'data' => $organization,
        ]);
    }

    /**
     * Update the specified organization in storage.
     *
     * @param UpdateOrganizationRequest $request
     * @param Organization $organization
     * @return JsonResponse
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization): JsonResponse
    {
        try {
            // The request is already validated by UpdateOrganizationRequest
            // The validated data is used to update the Organization record
            $organization->update($request->validated());

            // Return a successful JSON response with the updated resource
            return response()->json([
                'status' => 'success',
                'message' => 'Organization updated successfully.',
                'data' => $organization,
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Organization update failed: ' . $e->getMessage());

            // Return an error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update organization.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified organization from storage (Soft Delete).
     *
     * @param Organization $organization
     * @return JsonResponse
     */
    public function destroy(Organization $organization): JsonResponse
    {
        try {
            // Assuming the Organization model uses SoftDeletes trait for safe deletion
            $organization->delete();

            // Return a successful JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Organization deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Organization deletion failed: ' . $e->getMessage());

            // Return an error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete organization.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the status of the specified organization.
     * This is a custom method for status management.
     *
     * @param Request $request
     * @param Organization $organization
     * @return JsonResponse
     */
    public function updateStatus(Request $request, Organization $organization): JsonResponse
    {
        // 1. Validate the status input
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                // Ensure the status is one of the allowed values
                Rule::in(['active', 'inactive', 'suspended']),
            ],
        ]);

        try {
            // 2. Update the status
            $organization->status = $validated['status'];
            $organization->save();

            // 3. Return a successful JSON response
            return response()->json([
                'status' => 'success',
                'message' => "Organization status updated to '{$organization->status}' successfully.",
                'data' => $organization,
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Organization status update failed: ' . $e->getMessage());

            // 4. Return an error response
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update organization status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
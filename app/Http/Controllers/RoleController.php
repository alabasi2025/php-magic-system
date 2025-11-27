<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Class RoleController
 * @package App\Http\Controllers
 *
 * Manages the CRUD operations for the Role resource.
 * This controller includes authorization checks using Laravel Gates/Policies.
 */
class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(Request $request): JsonResponse
    {
        // Authorization check: User must have the 'viewAny' ability on the Role model.
        Gate::authorize('viewAny', Role::class);

        // Fetch all roles, or paginate for production environments
        // For simplicity, we fetch all, but pagination is recommended for large datasets.
        $roles = Role::query()
            ->select('id', 'name', 'description', 'created_at')
            ->latest()
            ->get();

        // Return a JSON response with the list of roles
        return response()->json([
            'status' => 'success',
            'message' => 'Roles retrieved successfully.',
            'data' => $roles,
        ], 200);
    }

    /**
     * Store a newly created role in storage.
     *
     * @param StoreRoleRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        // Authorization check: User must have the 'create' ability on the Role model.
        Gate::authorize('create', Role::class);

        // Create the new role using validated data
        $role = Role::create($request->validated());

        // Return a JSON response for the created role
        return response()->json([
            'status' => 'success',
            'message' => 'Role created successfully.',
            'data' => $role,
        ], 201);
    }

    /**
     * Display the specified role.
     *
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Role $role): JsonResponse
    {
        // Authorization check: User must have the 'view' ability on the specific role instance.
        Gate::authorize('view', $role);

        // Load any necessary relationships (e.g., permissions)
        // $role->load('permissions');

        // Return a JSON response with the role details
        return response()->json([
            'status' => 'success',
            'message' => 'Role retrieved successfully.',
            'data' => $role,
        ], 200);
    }

    /**
     * Update the specified role in storage.
     *
     * @param UpdateRoleRequest $request
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        // Authorization check: User must have the 'update' ability on the specific role instance.
        Gate::authorize('update', $role);

        // Update the role with validated data
        $role->update($request->validated());

        // Return a JSON response for the updated role
        return response()->json([
            'status' => 'success',
            'message' => 'Role updated successfully.',
            'data' => $role,
        ], 200);
    }

    /**
     * Remove the specified role from storage.
     *
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Role $role): JsonResponse
    {
        // Authorization check: User must have the 'delete' ability on the specific role instance.
        Gate::authorize('delete', $role);

        // Delete the role
        $role->delete();

        // Return a JSON response indicating successful deletion
        return response()->json([
            'status' => 'success',
            'message' => 'Role deleted successfully.',
            'data' => null,
        ], 204); // 204 No Content is appropriate for successful deletion
    }
}

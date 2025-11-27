<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

/**
 * Class PermissionController
 * Handles CRUD operations for Permissions and managing permission-role assignments.
 * Assumes the use of Spatie's laravel-permission package.
 */
class PermissionController extends Controller
{
    /**
     * Constructor: Apply middleware for authorization.
     */
    public function __construct()
    {
        // Enforce policy or direct permission checks for all methods
        $this->middleware('permission:view permissions', ['only' => ['index', 'show']]);
        $this->middleware('permission:create permissions', ['only' => ['store']]);
        $this->middleware('permission:edit permissions', ['only' => ['update']]);
        $this->middleware('permission:delete permissions', ['only' => ['destroy']]);
        $this->middleware('permission:assign permissions', ['only' => ['assignToRole', 'revokeFromRole']]);
    }

    /**
     * Display a listing of the permissions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Retrieve all permissions, optionally with pagination and search
        $permissions = Permission::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'message' => 'Permissions retrieved successfully.',
            'data' => $permissions,
        ]);
    }

    /**
     * Store a newly created permission in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ]);

        // Create the new permission
        $permission = Permission::create([
            'name' => $request->name,
            // Default to 'web' guard if not provided
            'guard_name' => $request->guard_name ?? config('auth.defaults.guard'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Permission created successfully.',
            'data' => $permission,
        ], 201);
    }

    /**
     * Display the specified permission.
     *
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Permission $permission)
    {
        // Load roles that have this permission
        $permission->load('roles');

        return response()->json([
            'status' => 'success',
            'message' => 'Permission details retrieved successfully.',
            'data' => $permission,
        ]);
    }

    /**
     * Update the specified permission in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Permission $permission)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Ensure the name is unique, ignoring the current permission's ID
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ]);

        // Update the permission attributes
        $permission->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? $permission->guard_name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Permission updated successfully.',
            'data' => $permission,
        ]);
    }

    /**
     * Remove the specified permission from storage.
     *
     * @param \Spatie\Permission\Models\Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Permission $permission)
    {
        // Delete the permission
        $permission->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Permission deleted successfully.',
            'data' => null,
        ], 204);
    }

    /**
     * Assign a permission to a specific role.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignToRole(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'permission_name' => ['required', 'string', 'exists:permissions,name'],
            'role_name' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Find the permission and role
        $permission = Permission::where('name', $request->permission_name)->first();
        $role = Role::where('name', $request->role_name)->first();

        // Check if the role already has the permission
        if ($role->hasPermissionTo($permission)) {
            return response()->json([
                'status' => 'error',
                'message' => "Permission '{$request->permission_name}' is already assigned to role '{$request->role_name}'.",
            ], 409); // Conflict
        }

        // Assign the permission to the role
        $role->givePermissionTo($permission);

        return response()->json([
            'status' => 'success',
            'message' => "Permission '{$request->permission_name}' assigned to role '{$request->role_name}' successfully.",
            'data' => [
                'role' => $role->name,
                'permission' => $permission->name,
            ],
        ]);
    }

    /**
     * Revoke a permission from a specific role.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeFromRole(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'permission_name' => ['required', 'string', 'exists:permissions,name'],
            'role_name' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Find the permission and role
        $permission = Permission::where('name', $request->permission_name)->first();
        $role = Role::where('name', $request->role_name)->first();

        // Check if the role has the permission to revoke
        if (!$role->hasPermissionTo($permission)) {
            return response()->json([
                'status' => 'error',
                'message' => "Role '{$request->role_name}' does not have permission '{$request->permission_name}'.",
            ], 404); // Not Found
        }

        // Revoke the permission from the role
        $role->revokePermissionTo($permission);

        return response()->json([
            'status' => 'success',
            'message' => "Permission '{$request->permission_name}' revoked from role '{$request->role_name}' successfully.",
            'data' => [
                'role' => $role->name,
                'permission' => $permission->name,
            ],
        ]);
    }
}
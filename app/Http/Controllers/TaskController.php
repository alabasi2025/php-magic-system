<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Apply authorization check for viewing tasks
        Gate::authorize('viewAny', Task::class);

        // Start with all tasks
        $tasks = Task::with(['creator', 'assignee']);

        // Filtering logic (optional but good practice)
        if ($request->has('status')) {
            $tasks->where('status', $request->input('status'));
        }

        if ($request->has('assigned_to')) {
            $tasks->where('assigned_to_user_id', $request->input('assigned_to'));
        }

        // Order by creation date descending
        $tasks = $tasks->latest()->paginate(15);

        return response()->json([
            'message' => 'Tasks retrieved successfully.',
            'data' => $tasks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Apply authorization check for creating tasks
        Gate::authorize('create', Task::class);

        // 1. Validation
        $validatedData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
        ]);

        // 2. Create the task
        try {
            $task = Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'] ?? null,
                'due_date' => $validatedData['due_date'] ?? null,
                'priority' => $validatedData['priority'],
                'status' => 'pending', // Default status for a new task
                'created_by_user_id' => Auth::id(),
                'assigned_to_user_id' => $validatedData['assigned_to_user_id'] ?? null,
            ]);

            // Load relationships for response
            $task->load(['creator', 'assignee']);

            return response()->json([
                'message' => 'Task created successfully.',
                'data' => $task
            ], 201);

        } catch (\Exception $e) {
            // Log the error and return a generic server error response
            \Log::error('Task creation failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while creating the task.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Task $task)
    {
        // Apply authorization check for viewing a specific task
        Gate::authorize('view', $task);

        // Load relationships for a complete view
        $task->load(['creator', 'assignee']);

        return response()->json([
            'message' => 'Task retrieved successfully.',
            'data' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Task $task)
    {
        // Apply authorization check for updating the task
        Gate::authorize('update', $task);

        // 1. Validation
        $validatedData = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'priority' => ['sometimes', 'required', 'string', Rule::in(['low', 'medium', 'high'])],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
        ]);

        // 2. Update the task
        try {
            $task->update($validatedData);

            // Load relationships for response
            $task->load(['creator', 'assignee']);

            return response()->json([
                'message' => 'Task updated successfully.',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            \Log::error('Task update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while updating the task.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task)
    {
        // Apply authorization check for deleting the task
        Gate::authorize('delete', $task);

        try {
            $task->delete();

            return response()->json([
                'message' => 'Task deleted successfully.'
            ], 204); // 204 No Content for successful deletion
        } catch (\Exception $e) {
            \Log::error('Task deletion failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while deleting the task.'
            ], 500);
        }
    }

    /**
     * Update the status of the specified task.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Task $task)
    {
        // Apply authorization check for updating task status
        // Assuming a separate 'updateStatus' policy method or using 'update'
        Gate::authorize('update', $task);

        // 1. Validation for status update
        $validatedData = $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'in_progress', 'completed', 'on_hold', 'cancelled'])],
        ]);

        // 2. Update the status
        try {
            $task->status = $validatedData['status'];
            $task->save();

            $task->load(['creator', 'assignee']);

            return response()->json([
                'message' => 'Task status updated successfully.',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            \Log::error('Task status update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while updating the task status.'
            ], 500);
        }
    }

    /**
     * Assign the specified task to a user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignTask(Request $request, Task $task)
    {
        // Apply authorization check for assigning the task
        // Assuming the creator or an admin can assign/reassign
        Gate::authorize('assign', $task);

        // 1. Validation for assignment
        $validatedData = $request->validate([
            'assigned_to_user_id' => ['required', 'exists:users,id'],
        ]);

        // 2. Assign the task
        try {
            $task->assigned_to_user_id = $validatedData['assigned_to_user_id'];
            $task->save();

            $task->load(['creator', 'assignee']);

            return response()->json([
                'message' => 'Task assigned successfully.',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            \Log::error('Task assignment failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while assigning the task.'
            ], 500);
        }
    }
}
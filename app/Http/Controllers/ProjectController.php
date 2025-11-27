<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User; // Assuming User model for relationships
use App\Http\Requests\ProjectRequest; // Assuming a ProjectRequest for validation
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class ProjectController
 * Handles CRUD operations, dashboard view, and budget tracking for Projects.
 * Adheres to Laravel 12 best practices.
 */
class ProjectController extends Controller
{
    /**
     * Constructor to apply middleware for security.
     * Ensures only authenticated users can access project management routes.
     */
    public function __construct()
    {
        // Example: Only authenticated users can access these methods
        $this->middleware('auth');
        // Example: Only users with 'manage-projects' permission can perform CRUD
        // $this->middleware('can:manage-projects')->except(['dashboard', 'index', 'show']);
    }

    /**
     * Display the Project Management Dashboard.
     * Shows key metrics, budget summaries, and recent activity.
     *
     * @return View
     */
    public function dashboard(): View
    {
        // 1. Total number of active projects
        $totalActiveProjects = Project::where('status', 'active')->count();

        // 2. Overall budget summary (assuming 'budget' and 'spent_amount' columns in projects table)
        $totalBudget = Project::sum('budget');
        $totalSpent = Project::sum('spent_amount');
        $remainingBudget = $totalBudget - $totalSpent;

        // 3. Projects nearing budget limit (e.g., spent > 80% of budget)
        $budgetWarningProjects = Project::whereRaw('spent_amount / budget >= 0.8')
            ->where('status', 'active')
            ->orderByDesc('spent_amount')
            ->limit(5)
            ->get();

        // 4. Recently completed projects
        $recentlyCompleted = Project::where('status', 'completed')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        return view('projects.dashboard', compact(
            'totalActiveProjects',
            'totalBudget',
            'totalSpent',
            'remainingBudget',
            'budgetWarningProjects',
            'recentlyCompleted'
        ));
    }

    /**
     * Display a listing of the projects.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Apply filtering and searching logic
        $projects = Project::with('manager') // Eager load the manager relationship
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     *
     * @return View
     */
    public function create(): View
    {
        // Assuming a list of users can be project managers
        $managers = User::where('is_manager', true)->get(['id', 'name']);
        return view('projects.create', compact('managers'));
    }

    /**
     * Store a newly created project in storage.
     * Uses ProjectRequest for secure and validated input.
     *
     * @param ProjectRequest $request
     * @return RedirectResponse
     */
    public function store(ProjectRequest $request): RedirectResponse
    {
        try {
            // The validated() method returns only the validated data
            $project = Project::create($request->validated());

            return redirect()->route('projects.show', $project->id)
                ->with('success', 'Project "' . $project->name . '" created successfully.');

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error("Project creation failed: " . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Failed to create project. Please try again.');
        }
    }

    /**
     * Display the specified project.
     * Includes budget tracking details.
     *
     * @param Project $project
     * @return View
     */
    public function show(Project $project): View
    {
        // Ensure the project's manager is loaded
        $project->load('manager');

        // Budget Tracking Logic
        $budgetStatus = $this->calculateBudgetStatus($project);

        return view('projects.show', compact('project', 'budgetStatus'));
    }

    /**
     * Show the form for editing the specified project.
     *
     * @param Project $project
     * @return View
     */
    public function edit(Project $project): View
    {
        $managers = User::where('is_manager', true)->get(['id', 'name']);
        return view('projects.edit', compact('project', 'managers'));
    }

    /**
     * Update the specified project in storage.
     * Uses ProjectRequest for secure and validated input.
     *
     * @param ProjectRequest $request
     * @param Project $project
     * @return RedirectResponse
     */
    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        try {
            // The validated() method returns only the validated data
            $project->update($request->validated());

            return redirect()->route('projects.show', $project->id)
                ->with('success', 'Project "' . $project->name . '" updated successfully.');

        } catch (\Exception $e) {
            \Log::error("Project update failed for ID {$project->id}: " . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Failed to update project. Please try again.');
        }
    }

    /**
     * Remove the specified project from storage.
     *
     * @param Project $project
     * @return RedirectResponse
     */
    public function destroy(Project $project): RedirectResponse
    {
        try {
            $projectName = $project->name;
            $project->delete();

            return redirect()->route('projects.index')
                ->with('success', 'Project "' . $projectName . '" deleted successfully.');

        } catch (\Exception $e) {
            \Log::error("Project deletion failed for ID {$project->id}: " . $e->getMessage());

            return back()
                ->with('error', 'Failed to delete project. It might have related records.');
        }
    }

    /**
     * Helper method to calculate and return the budget status for a project.
     * This is the core budget tracking logic.
     *
     * @param Project $project
     * @return array
     */
    protected function calculateBudgetStatus(Project $project): array
    {
        $budget = (float) $project->budget;
        $spent = (float) $project->spent_amount; // Assuming this column exists and is updated elsewhere

        if ($budget <= 0) {
            return [
                'percentage' => 0,
                'remaining' => 0,
                'status' => 'No Budget Set',
                'class' => 'info',
            ];
        }

        $remaining = $budget - $spent;
        $percentageSpent = ($spent / $budget) * 100;

        $status = 'On Track';
        $class = 'success';

        if ($percentageSpent >= 100) {
            $status = 'Over Budget';
            $class = 'danger';
        } elseif ($percentageSpent >= 85) {
            $status = 'Nearing Limit';
            $class = 'warning';
        }

        return [
            'percentage' => round($percentageSpent, 2),
            'remaining' => round($remaining, 2),
            'status' => $status,
            'class' => $class, // For front-end styling (e.g., Bootstrap classes)
        ];
    }
}
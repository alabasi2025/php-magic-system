<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Http\Requests\StoreHoldingRequest;
use App\Http\Requests\UpdateHoldingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
     * @return View
     */
    public function index(Request $request): View
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

            // Return the view with the paginated data
            return view('organization.holdings.index', compact('holdings'));
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("HoldingController@index: Failed to retrieve holdings. Error: " . $e->getMessage());

            // Redirect back with error message
            return back()->with('error', 'حدث خطأ أثناء جلب البيانات: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new Holding.
     *
     * @return View
     */
    public function create(): View
    {
        return view('organization.holdings.create');
    }

    /**
     * Store a newly created Holding resource in storage.
     *
     * @param StoreHoldingRequest $request
     * @return RedirectResponse
     */
    public function store(StoreHoldingRequest $request): RedirectResponse
    {
        try {
            // The request is already validated by StoreHoldingRequest
            $holding = Holding::create($request->validated());

            // Redirect to the index page with success message
            return redirect()->route('holdings.index')
                ->with('success', 'تم إنشاء الشركة القابضة بنجاح');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("HoldingController@store: Failed to create holding. Error: " . $e->getMessage());

            // Redirect back with error message
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الشركة القابضة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified Holding resource.
     *
     * @param Holding $holding
     * @return View
     */
    public function show(Holding $holding): View
    {
        try {
            // Load any necessary relationships (e.g., 'companies')
            $holding->load('companies');

            // Return the view with the holding data
            return view('organization.holdings.show', compact('holding'));
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("HoldingController@show: Failed to retrieve holding ID: {$holding->id}. Error: " . $e->getMessage());

            // Redirect back with error message
            return back()->with('error', 'حدث خطأ أثناء جلب بيانات الشركة القابضة: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified Holding.
     *
     * @param Holding $holding
     * @return View
     */
    public function edit(Holding $holding): View
    {
        return view('organization.holdings.edit', compact('holding'));
    }

    /**
     * Update the specified Holding resource in storage.
     *
     * @param UpdateHoldingRequest $request
     * @param Holding $holding
     * @return RedirectResponse
     */
    public function update(UpdateHoldingRequest $request, Holding $holding): RedirectResponse
    {
        try {
            // The request is already validated by UpdateHoldingRequest
            $holding->update($request->validated());

            // Redirect to the show page with success message
            return redirect()->route('holdings.show', $holding)
                ->with('success', 'تم تحديث الشركة القابضة بنجاح');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("HoldingController@update: Failed to update holding ID: {$holding->id}. Error: " . $e->getMessage());

            // Redirect back with error message
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الشركة القابضة: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified Holding resource from storage.
     *
     * @param Holding $holding
     * @return RedirectResponse
     */
    public function destroy(Holding $holding): RedirectResponse
    {
        try {
            // Check for related records before deletion (e.g., if it has companies)
            if ($holding->companies()->exists()) {
                return back()->with('error', 'لا يمكن حذف الشركة القابضة لأنها تحتوي على شركات مرتبطة');
            }

            $holding->delete();

            // Redirect to the index page with success message
            return redirect()->route('holdings.index')
                ->with('success', 'تم حذف الشركة القابضة بنجاح');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("HoldingController@destroy: Failed to delete holding ID: {$holding->id}. Error: " . $e->getMessage());

            // Redirect back with error message
            return back()->with('error', 'حدث خطأ أثناء حذف الشركة القابضة: ' . $e->getMessage());
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

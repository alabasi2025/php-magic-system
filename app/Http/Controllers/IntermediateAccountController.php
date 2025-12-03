<?php

namespace App\Http\Controllers;

use App\Models\ChartAccount;
use App\Models\ChartGroup;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntermediateAccountController extends Controller
{
    /**
     * Display a listing of intermediate accounts
     */
    public function index(Request $request)
    {
        $query = ChartAccount::where('account_type', 'intermediate')
            ->with(['chartGroup', 'chartGroup.unit']);

        // Filter by unit
        if ($request->filled('unit_id')) {
            $query->whereHas('chartGroup', function ($q) use ($request) {
                $q->where('unit_id', $request->unit_id);
            });
        }

        // Filter by chart group
        if ($request->filled('chart_group_id')) {
            $query->where('chart_group_id', $request->chart_group_id);
        }

        // Filter by intermediate type
        if ($request->filled('intermediate_for')) {
            $query->where('intermediate_for', $request->intermediate_for);
        }

        // Filter by linked status
        if ($request->filled('is_linked')) {
            $query->where('is_linked', $request->is_linked);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderBy('created_at', 'desc')->paginate(20);
        $units = Unit::where('is_active', true)->get();
        $chartGroups = ChartGroup::where('is_active', true)->get();

        return view('intermediate-accounts.index', compact('accounts', 'units', 'chartGroups'));
    }

    /**
     * Show the form for creating a new intermediate account
     */
    public function create()
    {
        $units = Unit::where('is_active', true)->get();
        $chartGroups = ChartGroup::where('is_active', true)->get();

        return view('intermediate-accounts.create', compact('units', 'chartGroups'));
    }

    /**
     * Store a newly created intermediate account
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'chart_group_id' => 'required|exists:chart_groups,id',
            'parent_id' => 'nullable|exists:chart_accounts,id',
            'code' => 'required|string|max:50|unique:chart_accounts,code',
            'name' => 'required|string|max:255',
            'intermediate_for' => 'required|in:cash_boxes,banks,wallets,atms',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['account_type'] = 'intermediate';
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['is_linked'] = false;

        $account = ChartAccount::create($validated);

        return redirect()
            ->route('intermediate-accounts.show', $account->id)
            ->with('success', 'تم إنشاء الحساب الوسيط بنجاح');
    }

    /**
     * Display the specified intermediate account
     */
    public function show($id)
    {
        $account = ChartAccount::with(['chartGroup', 'chartGroup.unit', 'parent'])
            ->where('account_type', 'intermediate')
            ->findOrFail($id);

        // Get linked entities (cash boxes, banks, etc.)
        $linkedEntity = null;
        switch ($account->intermediate_for) {
            case 'cash_boxes':
                $linkedEntity = $account->cashBox;
                break;
            case 'banks':
                $linkedEntity = $account->bank;
                break;
            case 'wallets':
                $linkedEntity = $account->wallet;
                break;
            case 'atms':
                $linkedEntity = $account->atm;
                break;
        }

        return view('intermediate-accounts.show', compact('account', 'linkedEntity'));
    }

    /**
     * Show the form for editing the specified intermediate account
     */
    public function edit($id)
    {
        $account = ChartAccount::where('account_type', 'intermediate')->findOrFail($id);
        $units = Unit::where('is_active', true)->get();
        $chartGroups = ChartGroup::where('is_active', true)->get();

        return view('intermediate-accounts.edit', compact('account', 'units', 'chartGroups'));
    }

    /**
     * Update the specified intermediate account
     */
    public function update(Request $request, $id)
    {
        $account = ChartAccount::where('account_type', 'intermediate')->findOrFail($id);

        $validated = $request->validate([
            'chart_group_id' => 'required|exists:chart_groups,id',
            'parent_id' => 'nullable|exists:chart_accounts,id',
            'code' => 'required|string|max:50|unique:chart_accounts,code,' . $id,
            'name' => 'required|string|max:255',
            'intermediate_for' => 'required|in:cash_boxes,banks,wallets,atms',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $account->update($validated);

        return redirect()
            ->route('intermediate-accounts.show', $account->id)
            ->with('success', 'تم تحديث الحساب الوسيط بنجاح');
    }

    /**
     * Remove the specified intermediate account
     */
    public function destroy($id)
    {
        $account = ChartAccount::where('account_type', 'intermediate')->findOrFail($id);

        // Check if account is linked
        if ($account->is_linked) {
            return redirect()
                ->route('intermediate-accounts.index')
                ->with('error', 'لا يمكن حذف حساب وسيط مرتبط بكيان آخر');
        }

        $account->delete();

        return redirect()
            ->route('intermediate-accounts.index')
            ->with('success', 'تم حذف الحساب الوسيط بنجاح');
    }

    /**
     * Get intermediate accounts by chart group (AJAX)
     */
    public function getByChartGroup($chartGroupId)
    {
        $accounts = ChartAccount::where('chart_group_id', $chartGroupId)
            ->where('account_type', 'intermediate')
            ->where('is_active', true)
            ->where('is_linked', false)
            ->get(['id', 'code', 'name', 'intermediate_for']);

        return response()->json($accounts);
    }

    /**
     * Get intermediate accounts by unit (AJAX)
     */
    public function getByUnit($unitId)
    {
        $accounts = ChartAccount::whereHas('chartGroup', function ($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            })
            ->where('account_type', 'intermediate')
            ->where('is_active', true)
            ->where('is_linked', false)
            ->with('chartGroup')
            ->get(['id', 'code', 'name', 'intermediate_for', 'chart_group_id']);

        return response()->json($accounts);
    }
}

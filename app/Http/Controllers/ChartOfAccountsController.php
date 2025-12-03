<?php

namespace App\Http\Controllers;

use App\Models\ChartGroup;
use App\Models\ChartAccount;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ChartOfAccountsController
 * 
 * Controller for managing multiple chart of accounts groups
 * 
 * @package App\Http\Controllers
 */
class ChartOfAccountsController extends Controller
{
    /**
     * Display the main page with all chart groups
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Get all active chart groups with their accounts count
            $chartGroups = ChartGroup::with('unit')
                ->withCount('accounts')
                ->active()
                ->ordered()
                ->get();
            
            return view('chart-of-accounts.index', compact('chartGroups'));
        } catch (\Exception $e) {
            return view('chart-of-accounts.index', [
                'chartGroups' => collect(),
                'error' => 'حدث خطأ أثناء تحميل الأدلة المحاسبية: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display a specific chart group with its accounts tree
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $chartGroup = ChartGroup::with(['unit', 'rootAccounts.descendants'])
                ->findOrFail($id);
            
            return view('chart-of-accounts.show', compact('chartGroup'));
        } catch (\Exception $e) {
            return redirect()->route('chart-of-accounts.index')
                ->with('error', 'حدث خطأ أثناء تحميل الدليل المحاسبي: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new chart group
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $units = Unit::active()->get();
        return view('chart-of-accounts.create', compact('units'));
    }

    /**
     * Store a newly created chart group
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'code' => 'required|string|max:50|unique:chart_groups,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:payroll,final_accounts,assets,budget,projects,inventory,sales,purchases,custom',
        ]);

        try {
            $chartGroup = ChartGroup::create([
                'unit_id' => $request->unit_id,
                'code' => $request->code,
                'name' => $request->name,
                'name_en' => $request->name_en,
                'type' => $request->type,
                'description' => $request->description,
                'icon' => $request->icon,
                'color' => $request->color,
                'is_active' => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('chart-of-accounts.show', $chartGroup->id)
                ->with('success', 'تم إنشاء الدليل المحاسبي بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الدليل المحاسبي: ' . $e->getMessage());
        }
    }

    /**
     * Add a new account to a chart group
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAccount(Request $request)
    {
        $request->validate([
            'chart_group_id' => 'required|exists:chart_groups,id',
            'parent_id' => 'nullable|exists:chart_accounts,id',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
        ]);

        try {
            DB::beginTransaction();

            // Calculate level
            $level = 1;
            if ($request->parent_id) {
                $parent = ChartAccount::find($request->parent_id);
                $level = $parent->level + 1;
                
                // Update parent's is_parent flag
                $parent->is_parent = true;
                $parent->save();
            }

            $account = ChartAccount::create([
                'chart_group_id' => $request->chart_group_id,
                'parent_id' => $request->parent_id,
                'level' => $level,
                'code' => $request->code,
                'name' => $request->name,
                'name_en' => $request->name_en,
                'account_type' => $request->account_type,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الحساب بنجاح',
                'account' => $account
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الحساب: ' . $e->getMessage()
            ], 500);
        }
    }
}

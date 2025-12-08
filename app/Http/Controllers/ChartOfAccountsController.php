<?php

namespace App\Http\Controllers;

use App\Models\ChartGroup;
use App\Models\ChartAccount;
use App\Models\Unit;
use App\Services\MasterChartService;
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
    protected $masterChartService;

    public function __construct(MasterChartService $masterChartService)
    {
        $this->masterChartService = $masterChartService;
    }
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
            // استخدام MasterChartService لإنشاء الدليل مع التفرع التلقائي
            $chartGroup = $this->masterChartService->createSubChart($request->unit_id, [
                'code' => $request->code,
                'name' => $request->name,
                'name_en' => $request->name_en,
                'type' => $request->type,
                'description' => $request->description,
                'icon' => $request->icon,
                'color' => $request->color,
                'is_active' => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0,
            ]);

            return redirect()->route('chart-of-accounts.show', $chartGroup->id)
                ->with('success', 'تم إنشاء الدليل المحاسبي بنجاح مع فرع تلقائي في دليل الحسابات الوسيطة');
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
            'is_parent' => 'required|boolean',
            'account_type' => 'nullable|in:general,cash_box,bank,wallet,atm,intermediate',
            'intermediate_for' => 'nullable|in:cash_boxes,banks,wallets,atms',
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
                'is_parent' => $request->is_parent,
                'account_type' => $request->account_type,
                'intermediate_for' => $request->intermediate_for,
                'is_linked' => false,
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

    /**
     * Update an existing account
     */
    public function updateAccount(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'is_parent' => 'required|in:0,1,true,false',
        ]);

        try {
            $account = ChartAccount::findOrFail($id);
            
            // Convert is_parent to boolean
            $isParent = in_array($request->is_parent, [1, '1', true, 'true'], true);
            
            // Convert is_active to boolean
            $isActive = $request->has('is_active') && $request->is_active ? true : false;
            
            // Handle account_group_id - convert empty string to null
            $accountGroupId = $request->account_group_id;
            if ($accountGroupId === '' || $accountGroupId === null) {
                $accountGroupId = null;
            }
            
            // Handle parent_id - convert empty string to null
            $parentId = $request->parent_id;
            if ($parentId === '' || $parentId === null) {
                $parentId = null;
            }
            
            $account->update([
                'parent_id' => $parentId,
                'code' => $request->code,
                'name' => $request->name,
                'name_en' => $request->name_en,
                'is_parent' => $isParent,
                'account_type' => $request->account_type,
                'account_group_id' => $accountGroupId,
                'intermediate_for' => $request->intermediate_for,
                'description' => $request->description,
                'is_active' => $isActive,
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الحساب بنجاح',
                'account' => $account
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحساب: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get account details
     */
    public function getAccount($id)
    {
        try {
            $account = ChartAccount::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'account' => $account
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل الحساب: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an account
     */
    public function deleteAccount($id)
    {
        try {
            $account = ChartAccount::findOrFail($id);
            
            // Check if account has children
            if ($account->children()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف حساب يحتوي على حسابات فرعية'
                ], 400);
            }

            // Check if account has transactions (if transactions table exists)
            // This is a placeholder - implement based on your transactions structure
            
            $account->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الحساب بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الحساب: ' . $e->getMessage()
            ], 500);
        }
    }
}

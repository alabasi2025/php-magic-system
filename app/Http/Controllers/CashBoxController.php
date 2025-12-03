<?php

namespace App\Http\Controllers;

use App\Models\CashBox;
use App\Models\Unit;
use App\Models\ChartAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * CashBoxController
 * 
 * Controller for managing cash boxes
 * 
 * @package App\Http\Controllers
 */
class CashBoxController extends Controller
{
    /**
     * Display a listing of cash boxes
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $cashBoxes = CashBox::with(['unit', 'intermediateAccount'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            return view('cash-boxes.index', compact('cashBoxes'));
        } catch (\Exception $e) {
            return view('cash-boxes.index', [
                'cashBoxes' => collect(),
                'error' => 'حدث خطأ أثناء تحميل الصناديق: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new cash box
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            // Get all active units
            $units = Unit::where('is_active', true)->get();
            
            // Get available intermediate accounts for cash boxes (not linked yet)
            $intermediateAccounts = ChartAccount::whereNotNull('parent_id')
                ->where('account_type', 'intermediate')
                ->where('intermediate_for', 'cash_boxes')
                ->where(function($query) {
                    $query->whereNull('is_linked')
                          ->orWhere('is_linked', false);
                })
                ->with('chartGroup')
                ->get();
            
            return view('cash-boxes.create', compact('units', 'intermediateAccounts'));
        } catch (\Exception $e) {
            return redirect()->route('cash-boxes.index')
                ->with('error', 'حدث خطأ أثناء تحميل نموذج الإنشاء: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created cash box
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'intermediate_account_id' => 'required|exists:chart_accounts,id',
            'name' => 'required|string|max:100|unique:cash_boxes,name',
            'code' => 'required|string|max:50|unique:cash_boxes,code',
            'balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Verify that the intermediate account is not already linked
            $intermediateAccount = ChartAccount::find($request->intermediate_account_id);
            if ($intermediateAccount->is_linked) {
                return back()->withInput()
                    ->with('error', 'الحساب الوسيط المحدد مرتبط بالفعل بصندوق آخر');
            }

            // Create the cash box
            $cashBox = CashBox::create([
                'unit_id' => $request->unit_id,
                'intermediate_account_id' => $request->intermediate_account_id,
                'name' => $request->name,
                'code' => $request->code,
                'balance' => $request->balance ?? 0,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'created_by' => Auth::id(),
            ]);

            // Mark the intermediate account as linked
            $intermediateAccount->is_linked = true;
            $intermediateAccount->save();

            DB::commit();

            return redirect()->route('cash-boxes.index')
                ->with('success', 'تم إنشاء الصندوق بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الصندوق: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified cash box
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $cashBox = CashBox::with(['unit', 'intermediateAccount.chartGroup', 'creator', 'updater'])
                ->findOrFail($id);
            
            return view('cash-boxes.show', compact('cashBox'));
        } catch (\Exception $e) {
            return redirect()->route('cash-boxes.index')
                ->with('error', 'حدث خطأ أثناء تحميل الصندوق: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified cash box
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $cashBox = CashBox::with(['unit', 'intermediateAccount'])->findOrFail($id);
            
            // Get all active units
            $units = Unit::where('is_active', true)->get();
            
            // Get available intermediate accounts (including the current one)
            $intermediateAccounts = ChartAccount::whereNotNull('parent_id')
                ->where('account_type', 'intermediate')
                ->where('intermediate_for', 'cash_boxes')
                ->where(function($query) use ($cashBox) {
                    $query->where('id', $cashBox->intermediate_account_id)
                          ->orWhere(function($q) {
                              $q->whereNull('is_linked')
                                ->orWhere('is_linked', false);
                          });
                })
                ->with('chartGroup')
                ->get();
            
            return view('cash-boxes.edit', compact('cashBox', 'units', 'intermediateAccounts'));
        } catch (\Exception $e) {
            return redirect()->route('cash-boxes.index')
                ->with('error', 'حدث خطأ أثناء تحميل نموذج التعديل: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified cash box
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $cashBox = CashBox::findOrFail($id);

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'intermediate_account_id' => 'required|exists:chart_accounts,id',
            'name' => 'required|string|max:100|unique:cash_boxes,name,' . $id,
            'code' => 'required|string|max:50|unique:cash_boxes,code,' . $id,
            'balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $oldIntermediateAccountId = $cashBox->intermediate_account_id;
            $newIntermediateAccountId = $request->intermediate_account_id;

            // If intermediate account changed
            if ($oldIntermediateAccountId != $newIntermediateAccountId) {
                // Verify that the new intermediate account is not already linked
                $newIntermediateAccount = ChartAccount::find($newIntermediateAccountId);
                if ($newIntermediateAccount->is_linked) {
                    return back()->withInput()
                        ->with('error', 'الحساب الوسيط المحدد مرتبط بالفعل بصندوق آخر');
                }

                // Unlink the old intermediate account
                $oldIntermediateAccount = ChartAccount::find($oldIntermediateAccountId);
                if ($oldIntermediateAccount) {
                    $oldIntermediateAccount->is_linked = false;
                    $oldIntermediateAccount->save();
                }

                // Link the new intermediate account
                $newIntermediateAccount->is_linked = true;
                $newIntermediateAccount->save();
            }

            // Update the cash box
            $cashBox->update([
                'unit_id' => $request->unit_id,
                'intermediate_account_id' => $request->intermediate_account_id,
                'name' => $request->name,
                'code' => $request->code,
                'balance' => $request->balance ?? $cashBox->balance,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('cash-boxes.show', $id)
                ->with('success', 'تم تحديث الصندوق بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الصندوق: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified cash box
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $cashBox = CashBox::findOrFail($id);

            // Unlink the intermediate account
            if ($cashBox->intermediate_account_id) {
                $intermediateAccount = ChartAccount::find($cashBox->intermediate_account_id);
                if ($intermediateAccount) {
                    $intermediateAccount->is_linked = false;
                    $intermediateAccount->save();
                }
            }

            // Soft delete the cash box
            $cashBox->delete();

            DB::commit();

            return redirect()->route('cash-boxes.index')
                ->with('success', 'تم حذف الصندوق بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cash-boxes.index')
                ->with('error', 'حدث خطأ أثناء حذف الصندوق: ' . $e->getMessage());
        }
    }

    /**
     * Get intermediate accounts for a specific unit via AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIntermediateAccounts(Request $request)
    {
        try {
            $unitId = $request->input('unit_id');
            $currentAccountId = $request->input('current_account_id');

            // Get chart groups for this unit
            $chartGroupIds = \App\Models\ChartGroup::where('unit_id', $unitId)
                ->pluck('id');

            // Get available intermediate accounts
            $query = ChartAccount::whereIn('chart_group_id', $chartGroupIds)
                ->where('is_main_account', false)
                ->where('account_type', 'intermediate')
                ->where('intermediate_for', 'cash_boxes');

            if ($currentAccountId) {
                $query->where(function($q) use ($currentAccountId) {
                    $q->where('id', $currentAccountId)
                      ->orWhere(function($subQ) {
                          $subQ->whereNull('is_linked')
                               ->orWhere('is_linked', false);
                      });
                });
            } else {
                $query->where(function($q) {
                    $q->whereNull('is_linked')
                      ->orWhere('is_linked', false);
                });
            }

            $accounts = $query->with('chartGroup')->get();

            return response()->json([
                'success' => true,
                'accounts' => $accounts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل الحسابات: ' . $e->getMessage()
            ], 500);
        }
    }
}

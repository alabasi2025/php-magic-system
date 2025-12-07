<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use App\Models\ChartGroup;
use Illuminate\Http\Request;

class FinancialSettingsController extends Controller
{
    /**
     * عرض صفحة الإعدادات المالية
     */
    public function index()
    {
        $accountTypes = AccountType::orderBy('sort_order')->get();
        $chartGroups = ChartGroup::with('unit')->orderBy('created_at', 'desc')->get();
        
        return view('financial-settings.index', compact('accountTypes', 'chartGroups'));
    }

    /**
     * إضافة نوع حساب جديد
     */
    public function storeAccountType(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|unique:account_types,key|alpha_dash',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_system'] = false; // الأنواع المضافة من المستخدم ليست نظام
        
        $accountType = AccountType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة نوع الحساب بنجاح',
            'data' => $accountType
        ]);
    }

    /**
     * تحديث نوع حساب
     */
    public function updateAccountType(Request $request, $id)
    {
        $accountType = AccountType::findOrFail($id);

        $validated = $request->validate([
            'key' => 'required|alpha_dash|unique:account_types,key,' . $id,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        $accountType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث نوع الحساب بنجاح',
            'data' => $accountType
        ]);
    }

    /**
     * حذف نوع حساب
     */
    public function deleteAccountType($id)
    {
        $accountType = AccountType::findOrFail($id);

        // منع حذف الأنواع النظامية
        if ($accountType->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف أنواع الحسابات النظامية'
            ], 403);
        }

        $accountType->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف نوع الحساب بنجاح'
        ]);
    }
}

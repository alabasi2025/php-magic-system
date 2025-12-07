<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use App\Models\AccountGroup;
use App\Models\ChartGroup;
use Illuminate\Http\Request;

class FinancialSettingsController extends Controller
{
    /**
     * عرض صفحة الإعدادات المالية
     */
    public function index()
    {
        $accountTypes = AccountType::orderBy('id')->get();
        
        // Check if account_groups table exists
        try {
            $accountGroups = AccountGroup::withCount('accounts')->orderBy('sort_order')->orderBy('name')->get();
        } catch (\Exception $e) {
            $accountGroups = collect(); // Empty collection if table doesn't exist
        }
        
        $chartGroups = ChartGroup::with('unit')->orderBy('created_at', 'desc')->get();
        
        return view('financial-settings.index', compact('accountTypes', 'accountGroups', 'chartGroups'));
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

    /**
     * إضافة مجموعة حسابات جديدة
     */
    public function storeAccountGroup(Request $request)
    {
        try {
            // تنظيف البيانات - إزالة القيم الفارغة
            $data = $request->all();
            if (empty($data['code'])) {
                unset($data['code']);
            }
            
            $validated = validator($data, [
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:50|unique:account_groups,code',
                'description' => 'nullable|string',
                'sort_order' => 'nullable|integer',
            ])->validate();

            $validated['is_active'] = $request->has('is_active') ? true : true; // Default to true
            
            $accountGroup = AccountGroup::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة مجموعة الحسابات بنجاح',
                'data' => $accountGroup
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating account group: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث مجموعة حسابات
     */
    public function updateAccountGroup(Request $request, $id)
    {
        $accountGroup = AccountGroup::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:account_groups,code,' . $id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        $accountGroup->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث مجموعة الحسابات بنجاح',
            'data' => $accountGroup
        ]);
    }

    /**
     * حذف مجموعة حسابات
     */
    public function deleteAccountGroup($id)
    {
        $accountGroup = AccountGroup::findOrFail($id);

        // التحقق من عدم وجود حسابات مربوطة بهذه المجموعة
        if ($accountGroup->accounts()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف المجموعة لأنها مرتبطة بحسابات. قم بإزالة الحسابات أولاً.'
            ], 403);
        }

        $accountGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف مجموعة الحسابات بنجاح'
        ]);
    }

    public function getAccountGroups()
    {
        try {
            $accountGroups = AccountGroup::withCount('accounts')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $accountGroups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAccountGroup($id)
    {
        try {
            $accountGroup = AccountGroup::withCount('accounts')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $accountGroup
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات المجموعة: ' . $e->getMessage()
            ], 500);
        }
    }
}

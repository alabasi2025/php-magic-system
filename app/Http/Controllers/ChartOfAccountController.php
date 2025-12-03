<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChartOfAccountController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * عرض قائمة دليل الحسابات
     */
    public function index(Request $request)
    {
        try {
            // التحقق من وجود الجداول المطلوبة
            $this->checkRequiredTables();
            
            $query = ChartOfAccount::with(['unit', 'parent', 'children'])
                ->latest();

            // تصفية حسب الوحدة
            if ($request->filled('unit_id')) {
                $query->where('unit_id', $request->unit_id);
            }

            // تصفية حسب نوع الحساب
            if ($request->filled('account_type')) {
                $query->where('account_type', $request->account_type);
            }

            // تصفية حسب النوع التحليلي
            if ($request->filled('analytical_type')) {
                $query->where('analytical_type', $request->analytical_type);
            }

            // تصفية حسب المستوى (رئيسي/فرعي)
            if ($request->filled('account_level')) {
                $query->where('account_level', $request->account_level);
            }

            // البحث
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('name_en', 'like', "%{$search}%");
                });
            }

            $accounts = $query->paginate(20);
            $units = Unit::active()->get();

            return view('chart-of-accounts.index', compact('accounts', 'units'));
            
        } catch (\Illuminate\Database\QueryException $e) {
            // خطأ في قاعدة البيانات
            return $this->handleDatabaseError($e);
        } catch (\Exception $e) {
            // أي خطأ آخر
            return $this->handleGeneralError($e);
        }
    }

    /**
     * التحقق من وجود الجداول المطلوبة
     */
    private function checkRequiredTables()
    {
        // فحص جدول units
        if (!Schema::hasTable('units')) {
            throw new \Exception('جدول الوحدات (units) غير موجود في قاعدة البيانات. يرجى تشغيل: php artisan migrate');
        }

        // فحص جدول chart_of_accounts
        if (!Schema::hasTable('chart_of_accounts')) {
            throw new \Exception('جدول دليل الحسابات (chart_of_accounts) غير موجود في قاعدة البيانات. يرجى تشغيل: php artisan migrate');
        }

        // فحص وجود بيانات في جدول units
        $unitsCount = DB::table('units')->count();
        if ($unitsCount === 0) {
            throw new \Exception('لا توجد وحدات في النظام. يرجى إنشاء وحدة واحدة على الأقل قبل استخدام دليل الحسابات.');
        }
    }

    /**
     * معالجة أخطاء قاعدة البيانات
     */
    private function handleDatabaseError(\Illuminate\Database\QueryException $e)
    {
        $errorCode = $e->getCode();
        $errorMessage = $e->getMessage();

        // تحديد نوع الخطأ
        if (str_contains($errorMessage, "Table") && str_contains($errorMessage, "doesn't exist")) {
            $tableName = $this->extractTableName($errorMessage);
            
            return response()->view('errors.setup-required', [
                'title' => 'دليل الحسابات',
                'message' => "جدول {$tableName} غير موجود في قاعدة البيانات.",
                'instructions' => $this->getMigrationInstructions(),
                'error' => $errorMessage,
                'solution' => 'تشغيل Migrations'
            ], 500);
        }

        // خطأ عام في قاعدة البيانات
        return response()->view('errors.database-error', [
            'title' => 'خطأ في قاعدة البيانات',
            'message' => 'حدث خطأ أثناء الاتصال بقاعدة البيانات.',
            'error' => config('app.debug') ? $errorMessage : 'يرجى الاتصال بمسؤول النظام',
            'code' => $errorCode
        ], 500);
    }

    /**
     * معالجة الأخطاء العامة
     */
    private function handleGeneralError(\Exception $e)
    {
        $errorMessage = $e->getMessage();

        // إذا كانت رسالة الخطأ تحتوي على تعليمات واضحة
        if (str_contains($errorMessage, 'php artisan migrate') || 
            str_contains($errorMessage, 'غير موجود') ||
            str_contains($errorMessage, 'لا توجد وحدات')) {
            
            return response()->view('errors.setup-required', [
                'title' => 'دليل الحسابات',
                'message' => $errorMessage,
                'instructions' => $this->getMigrationInstructions(),
                'error' => $errorMessage,
                'solution' => 'إعداد النظام'
            ], 500);
        }

        // خطأ عام
        return response()->view('errors.general', [
            'title' => 'حدث خطأ',
            'message' => 'حدث خطأ غير متوقع.',
            'error' => config('app.debug') ? $errorMessage : 'يرجى الاتصال بمسؤول النظام'
        ], 500);
    }

    /**
     * استخراج اسم الجدول من رسالة الخطأ
     */
    private function extractTableName($errorMessage)
    {
        preg_match("/Table '.*\.(.*)' doesn't exist/", $errorMessage, $matches);
        return $matches[1] ?? 'غير معروف';
    }

    /**
     * الحصول على تعليمات تشغيل Migrations
     */
    private function getMigrationInstructions()
    {
        return [
            'عبر Laravel Cloud Dashboard:' => [
                '1. اذهب إلى: https://cloud.laravel.com',
                '2. افتح مشروع: php-magic-system',
                '3. اذهب إلى: Deployments',
                '4. انقر على: Run Command',
                '5. اكتب: php artisan migrate',
                '6. انقر على: Run'
            ],
            'عبر Terminal (إذا كان لديك وصول):' => [
                'php artisan migrate'
            ]
        ];
    }

    /**
     * عرض نموذج إنشاء حساب جديد
     */
    public function create()
    {
        try {
            $this->checkRequiredTables();
            
            $units = Unit::active()->get();
            $parentAccounts = ChartOfAccount::active()
                ->parentAccounts()
                ->get();

            return view('chart-of-accounts.create', compact('units', 'parentAccounts'));
        } catch (\Exception $e) {
            return $this->handleGeneralError($e);
        }
    }

    /**
     * حفظ حساب جديد
     */
    public function store(Request $request)
    {
        $rules = [
            'unit_id' => 'required|exists:units,id',
            'code' => 'required|string|max:50|unique:chart_of_accounts,code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'account_level' => 'required|in:parent,sub',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ];

        // إذا كان الحساب فرعي، نطلب معلومات إضافية
        if ($request->account_level === 'sub') {
            $rules['account_type'] = 'nullable|in:asset,liability,equity,revenue,expense';
            $rules['analytical_type'] = 'nullable|in:cash_box,bank,cashier,wallet,customer,supplier,warehouse,employee,partner,other';
            $rules['preferred_currencies'] = 'nullable|array';
            $rules['preferred_currencies.*'] = 'string|max:3';
        }

        $validated = $request->validate($rules);

        // إضافة معلومات المستخدم
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        // إنشاء الحساب
        $account = ChartOfAccount::create($validated);

        // حساب المستوى والكود الكامل
        $account->level = $account->calculateLevel();
        $account->full_code = $account->buildFullCode();
        $account->save();

        return redirect()
            ->route('chart-of-accounts.show', $account)
            ->with('success', 'تم إنشاء الحساب بنجاح');
    }

    /**
     * عرض تفاصيل حساب
     */
    public function show(ChartOfAccount $chartOfAccount)
    {
        $chartOfAccount->load(['unit', 'parent', 'children', 'creator', 'updater']);

        return view('chart-of-accounts.show', compact('chartOfAccount'));
    }

    /**
     * عرض نموذج تعديل حساب
     */
    public function edit(ChartOfAccount $chartOfAccount)
    {
        $units = Unit::active()->get();
        $parentAccounts = ChartOfAccount::active()
            ->parentAccounts()
            ->where('id', '!=', $chartOfAccount->id)
            ->get();

        return view('chart-of-accounts.edit', compact('chartOfAccount', 'units', 'parentAccounts'));
    }

    /**
     * تحديث حساب
     */
    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        $rules = [
            'unit_id' => 'required|exists:units,id',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('chart_of_accounts', 'code')->ignore($chartOfAccount->id),
            ],
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'account_level' => 'required|in:parent,sub',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'boolean',
        ];

        // منع تغيير رقم الحساب إذا كان حساب جذر
        if ($chartOfAccount->is_root) {
            $rules['code'] = [
                'required',
                'string',
                'max:50',
                Rule::in([$chartOfAccount->code]), // يجب أن يبقى نفس الرقم
            ];
        }

        // منع تغيير نوع الحساب إذا كان له حسابات فرعية
        if ($chartOfAccount->has_children && $request->account_level !== $chartOfAccount->account_level) {
            return back()->withErrors(['account_level' => 'لا يمكن تغيير نوع الحساب لأنه يحتوي على حسابات فرعية']);
        }

        // إذا كان الحساب فرعي، نطلب معلومات إضافية
        if ($request->account_level === 'sub') {
            $rules['account_type'] = 'nullable|in:asset,liability,equity,revenue,expense';
            $rules['analytical_type'] = 'nullable|in:cash_box,bank,cashier,wallet,customer,supplier,warehouse,employee,partner,other';
            $rules['preferred_currencies'] = 'nullable|array';
            $rules['preferred_currencies.*'] = 'string|max:3';
        }

        $validated = $request->validate($rules);

        // إضافة معلومات المستخدم
        $validated['updated_by'] = Auth::id();

        // تحديث الحساب
        $chartOfAccount->update($validated);

        // إعادة حساب المستوى والكود الكامل
        $chartOfAccount->level = $chartOfAccount->calculateLevel();
        $chartOfAccount->full_code = $chartOfAccount->buildFullCode();
        $chartOfAccount->save();

        return redirect()
            ->route('chart-of-accounts.show', $chartOfAccount)
            ->with('success', 'تم تحديث الحساب بنجاح');
    }

    /**
     * حذف حساب
     */
    public function destroy(ChartOfAccount $chartOfAccount)
    {
        // التحقق من إمكانية الحذف
        if (!$chartOfAccount->canBeDeleted()) {
            return back()->withErrors(['error' => 'لا يمكن حذف هذا الحساب']);
        }

        $chartOfAccount->delete();

        return redirect()
            ->route('chart-of-accounts.index')
            ->with('success', 'تم حذف الحساب بنجاح');
    }

    /**
     * عرض الشجرة الهرمية للحسابات
     */
    public function tree(Request $request)
    {
        try {
            $this->checkRequiredTables();
            
            $unitId = $request->get('unit_id');

            $query = ChartOfAccount::with('allChildren')
                ->rootAccounts()
                ->active();

            if ($unitId) {
                $query->where('unit_id', $unitId);
            }

            $accounts = $query->get();
            $units = Unit::active()->get();

            return view('chart-of-accounts.tree', compact('accounts', 'units'));
        } catch (\Exception $e) {
            return $this->handleGeneralError($e);
        }
    }
}

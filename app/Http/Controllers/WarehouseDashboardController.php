<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * متحكم لوحة التحكم (WarehouseDashboardController)
 * مسؤول عن عرض لوحة التحكم الرئيسية للمخازن.
 */
class WarehouseDashboardController extends Controller
{
    /**
     * خدمة لوحة التحكم.
     * @var DashboardService
     */
    protected $dashboardService;

    /**
     * إنشاء مثيل جديد للمتحكم.
     *
     * @param DashboardService $dashboardService
     */
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
        // تطبيق الأمان: يجب أن يكون المستخدم مصرحاً له بعرض لوحة التحكم
        $this->middleware('can:view-warehouse-dashboard');
    }

    /**
     * عرض لوحة التحكم الرئيسية للمخازن.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // التحقق من الصلاحيات (إجراء إضافي للتأكيد، بالرغم من وجوده في الـ middleware)
        if (Gate::denies('view-warehouse-dashboard')) {
            // معالجة الأخطاء: إرجاع خطأ 403 إذا لم يكن مصرحاً له
            abort(403, 'غير مصرح لك بالوصول إلى لوحة التحكم هذه.');
        }

        try {
            // 1. استخراج الإحصائيات السريعة
            $quickStats = $this->dashboardService->getQuickStats();

            // 2. استخراج بيانات الرسوم البيانية
            $chartData = $this->dashboardService->getStockChartData();

            // 3. استخراج التنبيهات النشطة
            $alerts = $this->dashboardService->getActiveAlerts(5);

            // 4. استخراج آخر الحركات
            $recentMovements = $this->dashboardService->getRecentMovements(10);

            // تمرير البيانات إلى العرض
            return view('dashboard', [
                'quickStats' => $quickStats,
                'chartData' => $chartData,
                'alerts' => $alerts,
                'recentMovements' => $recentMovements,
            ]);

        } catch (\Exception $e) {
            // معالجة الأخطاء: تسجيل الخطأ وإرجاع رسالة خطأ للمستخدم
            \Log::error("Dashboard data retrieval failed: " . $e->getMessage());
            return back()->withError('حدث خطأ أثناء تحميل بيانات لوحة التحكم.');
        }
    }
}

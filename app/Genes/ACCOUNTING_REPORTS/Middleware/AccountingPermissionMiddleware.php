<?php

namespace App\Genes\ACCOUNTING_REPORTS\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * @class AccountingPermissionMiddleware
 * @package App\Genes\ACCOUNTING_REPORTS\Middleware
 *
 * وسيط للتحقق من صلاحيات المستخدم للوصول إلى تقارير المحاسبة.
 * يتطلب تمرير اسم الصلاحية المطلوبة كوسيط للميدل وير.
 *
 * يتكامل هذا الوسيط مع الأجنحة المحاسبية الأخرى (INTERMEDIATE_ACCOUNTS, CASH_BOXES, PARTNER_ACCOUNTING)
 * من خلال التحقق من الصلاحيات العامة التي قد تشمل الوصول إلى بيانات هذه الأجنحة.
 */
class AccountingPermissionMiddleware
{
    /**
     * معالجة طلب وارد.
     *
     * @param Request $request كائن الطلب HTTP.
     * @param Closure $next الدالة التي تمرر الطلب إلى المرحلة التالية.
     * @param string ...$permissions الصلاحيات المطلوبة (يمكن تمرير صلاحية واحدة أو أكثر).
     * @return Response
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        // 1. التحقق من وجود مستخدم مسجل الدخول
        if (!Auth::check()) {
            // إذا لم يكن هناك مستخدم مسجل الدخول، يتم توجيهه إلى صفحة تسجيل الدخول
            // أو إرجاع استجابة غير مصرح بها (401) إذا كان الطلب AJAX/API
            return $this->unauthorizedResponse($request, 'يجب تسجيل الدخول للوصول إلى هذه الميزة.');
        }

        $user = Auth::user();

        // 2. التحقق من الصلاحيات المطلوبة
        if (empty($permissions)) {
            // إذا لم يتم تحديد صلاحيات، نفترض أن المستخدم يحتاج فقط إلى صلاحية "عرض-التقارير-المحاسبية"
            $permissions = ['view-accounting-reports'];
        }

        // استخدام ميزة PHP 8.0+ للتحقق من الصلاحيات بكفاءة
        $hasPermission = match (true) {
            // التحقق من صلاحية المدير العام (Super Admin) - يفترض أن لديه كل الصلاحيات
            $user->isSuperAdmin() => true,
            // التحقق من الصلاحيات الممررة
            default => $user->hasAnyPermission($permissions),
        };

        // 3. تطبيق منطق التكامل مع الأجنحة الأخرى (افتراضياً)
        // يمكن إضافة منطق أكثر تعقيداً هنا للتحقق من صلاحيات الأجنحة الفرعية
        // مثال: إذا كانت الصلاحية المطلوبة هي 'view-partner-report'، يتم التحقق من صلاحية 'PARTNER_ACCOUNTING'
        // في هذا المثال، نعتمد على أن الصلاحيات الممررة تغطي متطلبات الأجنحة المدمجة.

        if (!$hasPermission) {
            // إذا لم تتوفر الصلاحية، يتم إرجاع استجابة غير مصرح بها (403)
            $requiredPermissions = implode(', ', $permissions);
            $message = "غير مصرح لك بالوصول. تحتاج إلى إحدى الصلاحيات التالية: {$requiredPermissions}.";
            return $this->unauthorizedResponse($request, $message, 403);
        }

        // 4. تمرير الطلب إلى المرحلة التالية
        return $next($request);
    }

    /**
     * إرجاع استجابة غير مصرح بها بناءً على نوع الطلب.
     *
     * @param Request $request كائن الطلب.
     * @param string $message رسالة الخطأ.
     * @param int $status كود حالة HTTP (401 أو 403).
     * @return Response
     */
    protected function unauthorizedResponse(Request $request, string $message, int $status = 401): Response
    {
        // استخدام ميزة PHP 8.0+ (match expression) لتحديد الاستجابة
        return match (true) {
            $request->expectsJson() => response()->json(['message' => $message], $status),
            default => redirect()->guest(route('login'))->withErrors(['permission' => $message]),
        };
    }
}

// ملاحظة: يفترض هذا الكود وجود دوال مثل isSuperAdmin() و hasAnyPermission()
// على نموذج المستخدم (User Model) أو من خلال حزمة صلاحيات مثل Spatie.
// كما يفترض وجود مسار باسم 'login' لصفحة تسجيل الدخول.

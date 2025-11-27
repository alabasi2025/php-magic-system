<?php

namespace App\Http\Middleware\Cashiers;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: EnsureCashierRole
 *
 * يتحقق هذا الـ Middleware من أن المستخدم الحالي لديه صلاحية "cashier"
 * قبل السماح له بالوصول إلى مسارات نظام الصرافين (Cashiers Gene).
 *
 * @package App\Http\Middleware\Cashiers
 */
class EnsureCashierRole
{
    /**
     * معالجة طلب وارد.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق من تسجيل الدخول أولاً
        if (!Auth::check()) {
            // إذا لم يكن المستخدم مسجلاً، يتم توجيهه إلى صفحة تسجيل الدخول
            return redirect()->route('login');
        }

        $user = Auth::user();

        // افتراض مبسط: التحقق من وجود حقل 'role' بقيمة 'cashier' في جدول المستخدمين
        // في تطبيق حقيقي، يفضل استخدام مكتبة صلاحيات مثل Spatie/laravel-permission
        if (!isset($user->role) || $user->role !== 'cashier') {
            // توجيه المستخدم إلى صفحة غير مصرح بها أو إرجاع خطأ 403
            return response()->json(['message' => 'Forbidden. Access restricted to cashiers.'], 403);
        }

        return $next($request);
    }
}
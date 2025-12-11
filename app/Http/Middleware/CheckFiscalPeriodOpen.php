<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\FiscalPeriod;
use Carbon\Carbon;

class CheckFiscalPeriodOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // فقط للطلبات التي تنشئ أو تعدل القيود
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            // الحصول على تاريخ القيد من الطلب
            $entryDate = $request->input('entry_date');
            
            if ($entryDate) {
                $date = Carbon::parse($entryDate);
                
                // البحث عن الفترة المالية المقابلة
                $period = FiscalPeriod::where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->first();
                
                if (!$period) {
                    return back()->withErrors([
                        'entry_date' => 'لا توجد فترة مالية محددة لهذا التاريخ.'
                    ])->withInput();
                }
                
                if ($period->is_closed) {
                    return back()->withErrors([
                        'entry_date' => 'الفترة المالية لهذا التاريخ مقفلة. لا يمكن إنشاء أو تعديل قيود في فترة مقفلة.'
                    ])->withInput();
                }
            }
        }
        
        return $next($request);
    }
}

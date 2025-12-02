<?php

namespace App\Genes\PARTNERSHIP_ACCOUNTING\Services;

use App\Genes\PARTNERSHIP_ACCOUNTING\Models\Partner;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\PartnershipShare;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\SimpleRevenue;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\SimpleExpense;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\ProfitCalculation;
use App\Genes\PARTNERSHIP_ACCOUNTING\Models\ProfitDistribution;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

/**
 * PartnershipService
 * 
 * خدمة إدارة محاسبة الشراكات.
 * تتضمن دوال لحساب الأرباح الشهرية، توزيعها على الشركاء،
 * وإدارة الإيرادات والمصروفات.
 * 
 * @version 1.0.0
 * @since 2025-12-02
 */
class PartnershipService
{
    /**
     * حساب الربح الشهري.
     * 
     * يتم حساب إجمالي الإيرادات والمصروفات للشهر المحدد،
     * ثم حساب صافي الربح (الإيرادات - المصروفات).
     *
     * @param int $month الشهر (1-12).
     * @param int $year السنة.
     * @return ProfitCalculation حساب الربح المُنشأ.
     * @throws Exception في حالة فشل الحساب
     */
    public function calculateMonthlyProfit(int $month, int $year): ProfitCalculation
    {
        try {
            DB::beginTransaction();
            
            // التحقق من صحة الشهر
            if ($month < 1 || $month > 12) {
                throw new Exception("الشهر غير صالح: {$month}");
            }
            
            // تحديد بداية ونهاية الفترة
            $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
            $periodEnd = Carbon::create($year, $month, 1)->endOfMonth();
            
            // حساب إجمالي الإيرادات
            $totalRevenue = SimpleRevenue::whereBetween('revenue_date', [$periodStart, $periodEnd])
                ->where('status', 'confirmed')
                ->sum('amount');
            
            // حساب إجمالي المصروفات
            $totalExpense = SimpleExpense::whereBetween('expense_date', [$periodStart, $periodEnd])
                ->where('status', 'confirmed')
                ->sum('amount');
            
            // حساب صافي الربح
            $netProfit = $totalRevenue - $totalExpense;
            
            // إنشاء سجل حساب الربح
            $profitCalculation = ProfitCalculation::create([
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'total_revenue' => $totalRevenue,
                'total_expense' => $totalExpense,
                'net_profit' => $netProfit,
                'status' => 'calculated',
                'notes' => "حساب الربح لشهر {$month}/{$year}",
                'created_by' => auth()->id() ?? 1,
            ]);
            
            DB::commit();
            return $profitCalculation;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * توزيع الأرباح على الشركاء.
     * 
     * يتم توزيع صافي الربح على الشركاء حسب نسب حصصهم.
     *
     * @param int $calculationId معرف حساب الربح.
     * @return Collection مجموعة من توزيعات الأرباح المُنشأة.
     * @throws Exception في حالة فشل التوزيع
     */
    public function distributeProfit(int $calculationId): Collection
    {
        try {
            DB::beginTransaction();
            
            // الحصول على حساب الربح
            $profitCalculation = ProfitCalculation::findOrFail($calculationId);
            
            // التحقق من أن الحساب لم يتم توزيعه مسبقاً
            if ($profitCalculation->status === 'distributed') {
                throw new Exception("تم توزيع هذا الربح مسبقاً");
            }
            
            // الحصول على جميع الشركاء النشطين
            $partners = Partner::where('is_active', true)->get();
            
            if ($partners->isEmpty()) {
                throw new Exception("لا يوجد شركاء نشطين للتوزيع");
            }
            
            // التحقق من أن مجموع النسب = 100%
            $totalPercentage = $partners->sum('share_percentage');
            if ($totalPercentage != 100) {
                throw new Exception("مجموع نسب الشركاء ({$totalPercentage}%) لا يساوي 100%");
            }
            
            $distributions = collect();
            
            // توزيع الربح على كل شريك
            foreach ($partners as $partner) {
                $partnerShare = ($profitCalculation->net_profit * $partner->share_percentage) / 100;
                
                $distribution = ProfitDistribution::create([
                    'profit_calculation_id' => $calculationId,
                    'partner_id' => $partner->id,
                    'share_percentage' => $partner->share_percentage,
                    'share_amount' => $partnerShare,
                    'status' => 'pending',
                    'notes' => "نصيب {$partner->name} من الربح",
                    'created_by' => auth()->id() ?? 1,
                ]);
                
                $distributions->push($distribution);
            }
            
            // تحديث حالة حساب الربح
            $profitCalculation->status = 'distributed';
            $profitCalculation->save();
            
            DB::commit();
            return $distributions;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * الحصول على كشف حساب شريك.
     * 
     * يعرض جميع توزيعات الأرباح للشريك في فترة محددة.
     *
     * @param int $partnerId معرف الشريك.
     * @param string $startDate تاريخ البداية.
     * @param string $endDate تاريخ النهاية.
     * @return array كشف حساب الشريك.
     * @throws Exception في حالة عدم العثور على الشريك
     */
    public function getPartnerStatement(
        int $partnerId,
        string $startDate,
        string $endDate
    ): array {
        $partner = Partner::findOrFail($partnerId);
        
        // الحصول على جميع التوزيعات في الفترة
        $distributions = ProfitDistribution::where('partner_id', $partnerId)
            ->whereHas('profitCalculation', function($query) use ($startDate, $endDate) {
                $query->whereBetween('period_start', [$startDate, $endDate]);
            })
            ->with('profitCalculation')
            ->orderBy('created_at')
            ->get();
        
        // حساب الإحصائيات
        $totalDistributions = $distributions->sum('share_amount');
        $paidDistributions = $distributions->where('status', 'paid')->sum('share_amount');
        $pendingDistributions = $distributions->where('status', 'pending')->sum('share_amount');
        
        return [
            'partner' => $partner,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'summary' => [
                'total_distributions' => $totalDistributions,
                'paid_distributions' => $paidDistributions,
                'pending_distributions' => $pendingDistributions,
                'distributions_count' => $distributions->count(),
            ],
            'distributions' => $distributions,
        ];
    }

    /**
     * تسجيل إيراد جديد.
     *
     * @param array $data بيانات الإيراد.
     * @return SimpleRevenue الإيراد المُنشأ.
     * @throws Exception في حالة فشل التسجيل
     */
    public function createRevenue(array $data): SimpleRevenue
    {
        try {
            DB::beginTransaction();
            
            // تعيين القيم الافتراضية
            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }
            
            if (!isset($data['revenue_date'])) {
                $data['revenue_date'] = now();
            }
            
            if (!isset($data['created_by'])) {
                $data['created_by'] = auth()->id() ?? 1;
            }
            
            $revenue = SimpleRevenue::create($data);
            
            DB::commit();
            return $revenue;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تسجيل مصروف جديد.
     *
     * @param array $data بيانات المصروف.
     * @return SimpleExpense المصروف المُنشأ.
     * @throws Exception في حالة فشل التسجيل
     */
    public function createExpense(array $data): SimpleExpense
    {
        try {
            DB::beginTransaction();
            
            // تعيين القيم الافتراضية
            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }
            
            if (!isset($data['expense_date'])) {
                $data['expense_date'] = now();
            }
            
            if (!isset($data['created_by'])) {
                $data['created_by'] = auth()->id() ?? 1;
            }
            
            $expense = SimpleExpense::create($data);
            
            DB::commit();
            return $expense;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تأكيد إيراد.
     *
     * @param int $revenueId معرف الإيراد.
     * @return SimpleRevenue الإيراد بعد التأكيد.
     * @throws Exception في حالة فشل التأكيد
     */
    public function confirmRevenue(int $revenueId): SimpleRevenue
    {
        try {
            DB::beginTransaction();
            
            $revenue = SimpleRevenue::findOrFail($revenueId);
            $revenue->status = 'confirmed';
            $revenue->save();
            
            DB::commit();
            return $revenue;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تأكيد مصروف.
     *
     * @param int $expenseId معرف المصروف.
     * @return SimpleExpense المصروف بعد التأكيد.
     * @throws Exception في حالة فشل التأكيد
     */
    public function confirmExpense(int $expenseId): SimpleExpense
    {
        try {
            DB::beginTransaction();
            
            $expense = SimpleExpense::findOrFail($expenseId);
            $expense->status = 'confirmed';
            $expense->save();
            
            DB::commit();
            return $expense;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تحديث حالة توزيع ربح.
     *
     * @param int $distributionId معرف التوزيع.
     * @param string $status الحالة الجديدة (pending/paid/cancelled).
     * @return ProfitDistribution التوزيع بعد التحديث.
     * @throws Exception في حالة فشل التحديث
     */
    public function updateDistributionStatus(int $distributionId, string $status): ProfitDistribution
    {
        try {
            DB::beginTransaction();
            
            // التحقق من صحة الحالة
            if (!in_array($status, ['pending', 'paid', 'cancelled'])) {
                throw new Exception("حالة غير صالحة: {$status}");
            }
            
            $distribution = ProfitDistribution::findOrFail($distributionId);
            $distribution->status = $status;
            
            if ($status === 'paid') {
                $distribution->paid_at = now();
            }
            
            $distribution->save();
            
            DB::commit();
            return $distribution;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * الحصول على ملخص الأرباح السنوي.
     *
     * @param int $year السنة.
     * @return array الملخص السنوي.
     */
    public function getYearlySummary(int $year): array
    {
        $calculations = ProfitCalculation::whereYear('period_start', $year)
            ->orderBy('period_start')
            ->get();
        
        $totalRevenue = $calculations->sum('total_revenue');
        $totalExpense = $calculations->sum('total_expense');
        $totalProfit = $calculations->sum('net_profit');
        
        // تجميع حسب الشهر
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthCalculation = $calculations->first(function($calc) use ($month) {
                return Carbon::parse($calc->period_start)->month === $month;
            });
            
            $monthlyData[] = [
                'month' => $month,
                'month_name' => Carbon::create($year, $month, 1)->locale('ar')->monthName,
                'revenue' => $monthCalculation->total_revenue ?? 0,
                'expense' => $monthCalculation->total_expense ?? 0,
                'profit' => $monthCalculation->net_profit ?? 0,
                'status' => $monthCalculation->status ?? 'not_calculated',
            ];
        }
        
        return [
            'year' => $year,
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_expense' => $totalExpense,
                'total_profit' => $totalProfit,
                'calculations_count' => $calculations->count(),
            ],
            'monthly_data' => $monthlyData,
        ];
    }

    /**
     * الحصول على إحصائيات الشركاء.
     *
     * @return array إحصائيات جميع الشركاء.
     */
    public function getPartnersStatistics(): array
    {
        $partners = Partner::where('is_active', true)->get();
        
        $statistics = [];
        
        foreach ($partners as $partner) {
            $totalDistributions = ProfitDistribution::where('partner_id', $partner->id)
                ->sum('share_amount');
            
            $paidDistributions = ProfitDistribution::where('partner_id', $partner->id)
                ->where('status', 'paid')
                ->sum('share_amount');
            
            $pendingDistributions = ProfitDistribution::where('partner_id', $partner->id)
                ->where('status', 'pending')
                ->sum('share_amount');
            
            $statistics[] = [
                'partner' => $partner,
                'total_distributions' => $totalDistributions,
                'paid_distributions' => $paidDistributions,
                'pending_distributions' => $pendingDistributions,
                'distributions_count' => ProfitDistribution::where('partner_id', $partner->id)->count(),
            ];
        }
        
        return $statistics;
    }
}

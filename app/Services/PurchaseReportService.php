<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Purchase Report Service
 * توليد تقارير المشتريات
 */
class PurchaseReportService
{
    /**
     * تقرير أوامر الشراء
     * Purchase orders report
     *
     * @param array $filters
     * @return Collection
     */
    public function getPurchaseOrdersReport(array $filters = []): Collection
    {
        $query = PurchaseOrder::with(['supplier', 'warehouse', 'creator'])
            ->orderBy('order_date', 'desc');

        // Apply filters
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('order_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (isset($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        return $query->get();
    }

    /**
     * تقرير المشتريات حسب المورد
     * Purchases by supplier report
     *
     * @param array $filters
     * @return Collection
     */
    public function getPurchasesBySupplierReport(array $filters = []): Collection
    {
        $query = Supplier::with(['purchaseInvoices' => function ($q) use ($filters) {
            $q->where('status', 'approved');
            
            if (isset($filters['start_date']) && isset($filters['end_date'])) {
                $q->whereBetween('invoice_date', [$filters['start_date'], $filters['end_date']]);
            }
        }])
        ->withCount(['purchaseInvoices as total_invoices' => function ($q) use ($filters) {
            $q->where('status', 'approved');
            
            if (isset($filters['start_date']) && isset($filters['end_date'])) {
                $q->whereBetween('invoice_date', [$filters['start_date'], $filters['end_date']]);
            }
        }])
        ->withSum(['purchaseInvoices as total_amount' => function ($q) use ($filters) {
            $q->where('status', 'approved');
            
            if (isset($filters['start_date']) && isset($filters['end_date'])) {
                $q->whereBetween('invoice_date', [$filters['start_date'], $filters['end_date']]);
            }
        }], 'total_amount')
        ->withSum(['purchaseInvoices as paid_amount' => function ($q) use ($filters) {
            $q->where('status', 'approved');
            
            if (isset($filters['start_date']) && isset($filters['end_date'])) {
                $q->whereBetween('invoice_date', [$filters['start_date'], $filters['end_date']]);
            }
        }], 'paid_amount')
        ->having('total_invoices', '>', 0)
        ->orderBy('total_amount', 'desc');

        if (isset($filters['supplier_id'])) {
            $query->where('id', $filters['supplier_id']);
        }

        return $query->get();
    }

    /**
     * تقرير المشتريات حسب الصنف
     * Purchases by item report
     *
     * @param array $filters
     * @return Collection
     */
    public function getPurchasesByItemReport(array $filters = []): Collection
    {
        $query = DB::table('purchase_invoice_items')
            ->join('purchase_invoices', 'purchase_invoice_items.purchase_invoice_id', '=', 'purchase_invoices.id')
            ->join('items', 'purchase_invoice_items.item_id', '=', 'items.id')
            ->where('purchase_invoices.status', 'approved')
            ->select(
                'items.id as item_id',
                'items.name as item_name',
                'items.code as item_code',
                DB::raw('SUM(purchase_invoice_items.quantity) as total_quantity'),
                DB::raw('AVG(purchase_invoice_items.unit_price) as avg_unit_price'),
                DB::raw('SUM(purchase_invoice_items.total_amount) as total_amount'),
                DB::raw('COUNT(DISTINCT purchase_invoices.id) as invoice_count')
            )
            ->groupBy('items.id', 'items.name', 'items.code');

        // Apply filters
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('purchase_invoices.invoice_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['item_id'])) {
            $query->where('items.id', $filters['item_id']);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('purchase_invoices.supplier_id', $filters['supplier_id']);
        }

        $query->orderBy('total_amount', 'desc');

        return collect($query->get());
    }

    /**
     * تقرير الفواتير المستحقة
     * Due invoices report
     *
     * @return Collection
     */
    public function getDueInvoicesReport(): Collection
    {
        return PurchaseInvoice::with(['supplier'])
            ->where('status', 'approved')
            ->whereIn('payment_status', ['unpaid', 'partially_paid'])
            ->where(function ($q) {
                $q->where('due_date', '<=', now())
                  ->orWhereNull('due_date');
            })
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * تقرير أداء الموردين
     * Supplier performance report
     *
     * @param array $filters
     * @return Collection
     */
    public function getSupplierPerformanceReport(array $filters = []): Collection
    {
        $startDate = $filters['start_date'] ?? now()->subMonths(6)->toDateString();
        $endDate = $filters['end_date'] ?? now()->toDateString();

        return Supplier::with(['purchaseOrders', 'purchaseInvoices'])
            ->withCount([
                'purchaseOrders as total_orders' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('order_date', [$startDate, $endDate]);
                },
                'purchaseOrders as completed_orders' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'received')
                      ->whereBetween('order_date', [$startDate, $endDate]);
                },
                'purchaseOrders as cancelled_orders' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'cancelled')
                      ->whereBetween('order_date', [$startDate, $endDate]);
                },
            ])
            ->withSum([
                'purchaseInvoices as total_purchases' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'approved')
                      ->whereBetween('invoice_date', [$startDate, $endDate]);
                }
            ], 'total_amount')
            ->withSum([
                'purchaseInvoices as total_paid' => function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'approved')
                      ->whereBetween('invoice_date', [$startDate, $endDate]);
                }
            ], 'paid_amount')
            ->having('total_orders', '>', 0)
            ->orderBy('total_purchases', 'desc')
            ->get()
            ->map(function ($supplier) {
                // Calculate performance metrics
                $supplier->completion_rate = $supplier->total_orders > 0 
                    ? ($supplier->completed_orders / $supplier->total_orders) * 100 
                    : 0;
                
                $supplier->cancellation_rate = $supplier->total_orders > 0 
                    ? ($supplier->cancelled_orders / $supplier->total_orders) * 100 
                    : 0;
                
                $supplier->payment_rate = $supplier->total_purchases > 0 
                    ? ($supplier->total_paid / $supplier->total_purchases) * 100 
                    : 0;
                
                return $supplier;
            });
    }

    /**
     * تصدير التقرير إلى Excel
     * Export report to Excel
     *
     * @param string $reportType
     * @param array $filters
     * @return string
     */
    public function exportToExcel(string $reportType, array $filters = []): string
    {
        // Get report data based on type
        switch ($reportType) {
            case 'orders':
                $data = $this->getPurchaseOrdersReport($filters);
                break;
            case 'by_supplier':
                $data = $this->getPurchasesBySupplierReport($filters);
                break;
            case 'by_item':
                $data = $this->getPurchasesByItemReport($filters);
                break;
            case 'due_invoices':
                $data = $this->getDueInvoicesReport();
                break;
            case 'supplier_performance':
                $data = $this->getSupplierPerformanceReport($filters);
                break;
            default:
                throw new \Exception('نوع التقرير غير صحيح');
        }

        // Generate Excel file (using Laravel Excel or similar library)
        $filename = $reportType . '_' . now()->format('Y-m-d_His') . '.xlsx';
        $filepath = storage_path('app/public/reports/' . $filename);

        // Create directory if not exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // Export to Excel (implementation depends on the Excel library used)
        // This is a placeholder - actual implementation would use Laravel Excel
        // \Excel::store(new ReportExport($data), $filepath);

        return $filepath;
    }

    /**
     * الحصول على إحصائيات لوحة التحكم
     * Get dashboard statistics
     *
     * @return array
     */
    public function getDashboardStatistics(): array
    {
        $currentMonth = now()->format('Y-m');
        
        return [
            'total_orders' => PurchaseOrder::whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year)
                ->count(),
            
            'total_orders_amount' => PurchaseOrder::whereMonth('order_date', now()->month)
                ->whereYear('order_date', now()->year)
                ->sum('total_amount'),
            
            'total_invoices' => PurchaseInvoice::where('status', 'approved')
                ->whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)
                ->count(),
            
            'total_invoices_amount' => PurchaseInvoice::where('status', 'approved')
                ->whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)
                ->sum('total_amount'),
            
            'total_paid' => PurchaseInvoice::where('status', 'approved')
                ->whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)
                ->sum('paid_amount'),
            
            'pending_orders' => PurchaseOrder::whereIn('status', ['draft', 'sent', 'confirmed'])
                ->count(),
            
            'overdue_invoices' => PurchaseInvoice::overdue()->count(),
            
            'overdue_amount' => PurchaseInvoice::overdue()->sum('remaining_amount'),
        ];
    }
}

<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    /**
     * Display purchases dashboard
     */
    public function dashboard()
    {
        return view('purchases.dashboard');
    }

    /**
     * Orders report
     */
    public function ordersReport(Request $request)
    {
        return view('purchases.reports.orders');
    }

    /**
     * By supplier report
     */
    public function bySupplierReport(Request $request)
    {
        return view('purchases.reports.by-supplier');
    }

    /**
     * By item report
     */
    public function byItemReport(Request $request)
    {
        return view('purchases.reports.by-item');
    }

    /**
     * Due invoices report
     */
    public function dueInvoicesReport(Request $request)
    {
        return view('purchases.reports.due-invoices');
    }

    /**
     * Supplier performance report
     */
    public function supplierPerformanceReport(Request $request)
    {
        return view('purchases.reports.supplier-performance');
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        // Export logic here
        return response()->json(['message' => 'Export functionality coming soon']);
    }
}

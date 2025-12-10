<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoiceType;
use Illuminate\Http\Request;

class PurchaseSettingsController extends Controller
{
    /**
     * Display purchase settings page.
     * عرض صفحة إعدادات المشتريات
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $invoiceTypes = PurchaseInvoiceType::orderBy('name')->get();
        
        return view('purchases.settings.index', compact('invoiceTypes'));
    }

    /**
     * Store a new invoice type.
     * إضافة نوع فاتورة جديد
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeInvoiceType(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:purchase_invoice_types,name',
                'code' => 'required|string|max:50|unique:purchase_invoice_types,code',
                'prefix' => 'required|string|max:10',
                'description' => 'nullable|string',
            ]);

            $invoiceType = PurchaseInvoiceType::create($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إضافة نوع الفاتورة بنجاح',
                    'data' => $invoiceType
                ]);
            }

            return redirect()->back()->with('success', 'تم إضافة نوع الفاتورة بنجاح');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل التحقق من البيانات',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إضافة نوع الفاتورة: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة نوع الفاتورة');
        }
    }

    /**
     * Update an invoice type.
     * تحديث نوع فاتورة
     *
     * @param Request $request
     * @param PurchaseInvoiceType $invoiceType
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInvoiceType(Request $request, PurchaseInvoiceType $invoiceType)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:purchase_invoice_types,name,' . $invoiceType->id,
                'code' => 'required|string|max:50|unique:purchase_invoice_types,code,' . $invoiceType->id,
                'prefix' => 'required|string|max:10',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $invoiceType->update($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث نوع الفاتورة بنجاح',
                    'data' => $invoiceType
                ]);
            }

            return redirect()->back()->with('success', 'تم تحديث نوع الفاتورة بنجاح');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء تحديث نوع الفاتورة: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث نوع الفاتورة');
        }
    }

    /**
     * Delete an invoice type.
     * حذف نوع فاتورة
     *
     * @param PurchaseInvoiceType $invoiceType
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteInvoiceType(PurchaseInvoiceType $invoiceType)
    {
        try {
            // التحقق من عدم وجود فواتير مرتبطة
            if ($invoiceType->purchaseInvoices()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف هذا النوع لوجود فواتير مرتبطة به'
                ], 400);
            }

            $invoiceType->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف نوع الفاتورة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف نوع الفاتورة: ' . $e->getMessage()
            ], 500);
        }
    }
}

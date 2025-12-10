<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Purchase Invoice Controller
 * متحكم فواتير المشتريات
 * 
 * @version 5.0.3
 * @date 2025-12-09
 */
class PurchaseInvoiceController extends Controller
{
    /**
     * Display a listing of purchase invoices.
     * عرض قائمة فواتير المشتريات
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $invoices = PurchaseInvoice::with(['supplier', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('purchases.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new purchase invoice.
     * عرض نموذج إنشاء فاتورة مشتريات جديدة
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // جلب الموردين النشطين
        $suppliers = Supplier::where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get();

        // جلب الأصناف النشطة
        $items = Item::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        // جلب المخازن النشطة
        $warehouses = \App\Models\Warehouse::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        // جلب أوامر الشراء المعتمدة (اختياري)
        $purchaseOrders = PurchaseOrder::where('status', 'approved')
            ->whereDoesntHave('invoices')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('purchases.invoices.create', compact('suppliers', 'items', 'warehouses', 'purchaseOrders'));
    }

    /**
     * Store a newly created purchase invoice in storage.
     * حفظ فاتورة مشتريات جديدة
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'payment_method' => 'required|in:cash,credit,bank_transfer,check',
            'status' => 'required|in:draft,pending,approved,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        try {
            // إنشاء الفاتورة باستخدام Model method
            $invoice = PurchaseInvoice::createPurchaseInvoice($request);

            return redirect()
                ->route('purchases.invoices.show', $invoice->id)
                ->with('success', 'تم إنشاء فاتورة المشتريات بنجاح');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase invoice.
     * عرض تفاصيل فاتورة مشتريات محددة
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $invoice = PurchaseInvoice::with([
            'supplier',
            'items.item',
            'creator',
            'approver'
        ])->findOrFail($id);

        return view('purchases.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified purchase invoice.
     * عرض نموذج تعديل فاتورة مشتريات
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $invoice = PurchaseInvoice::with(['items'])->findOrFail($id);

        // لا يمكن تعديل الفاتورة المعتمدة
        if ($invoice->isApproved()) {
            return redirect()
                ->route('purchases.invoices.show', $id)
                ->with('error', 'لا يمكن تعديل فاتورة معتمدة');
        }

        // جلب الموردين النشطين
        $suppliers = Supplier::where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get();

        // جلب الأصناف النشطة
        $items = Item::where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get();

        return view('purchases.invoices.edit', compact('invoice', 'suppliers', 'items'));
    }

    /**
     * Update the specified purchase invoice in storage.
     * تحديث فاتورة مشتريات
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);

        // لا يمكن تعديل الفاتورة المعتمدة
        if ($invoice->isApproved()) {
            return redirect()
                ->route('purchases.invoices.show', $id)
                ->with('error', 'لا يمكن تعديل فاتورة معتمدة');
        }

        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'payment_method' => 'required|in:cash,credit,bank_transfer,check',
            'status' => 'required|in:draft,pending,approved,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        try {
            // تحديث الفاتورة باستخدام Model method
            $result = $invoice->updatePurchaseInvoice($request);
            
            // التحقق من نجاح التحديث
            if (is_string($result)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'لا يمكن تعديل فاتورة معتمدة');
            }

            return redirect()
                ->route('purchases.invoices.show', $invoice->id)
                ->with('success', 'تم تحديث فاتورة المشتريات بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase invoice from storage.
     * حذف فاتورة مشتريات
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);

        // لا يمكن حذف الفاتورة المعتمدة
        if ($invoice->isApproved()) {
            return redirect()
                ->route('purchases.invoices.index')
                ->with('error', 'لا يمكن حذف فاتورة معتمدة');
        }

        // لا يمكن حذف الفاتورة التي تم الدفع لها
        if ($invoice->paid_amount > 0) {
            return redirect()
                ->route('purchases.invoices.index')
                ->with('error', 'لا يمكن حذف فاتورة تم الدفع لها');
        }

        DB::beginTransaction();
        try {
            // حذف الأصناف
            $invoice->items()->delete();
            
            // حذف الفاتورة
            $invoice->delete();

            DB::commit();

            return redirect()
                ->route('purchases.invoices.index')
                ->with('success', 'تم حذف فاتورة المشتريات بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->route('purchases.invoices.index')
                ->with('error', 'حدث خطأ أثناء حذف الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Approve the specified purchase invoice.
     * اعتماد فاتورة مشتريات
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);

        if ($invoice->isApproved()) {
            return redirect()
                ->route('purchases.invoices.show', $id)
                ->with('info', 'الفاتورة معتمدة بالفعل');
        }

        DB::beginTransaction();
        try {
            $invoice->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // TODO: إنشاء قيد محاسبي تلقائي

            DB::commit();

            return redirect()
                ->route('purchases.invoices.show', $id)
                ->with('success', 'تم اعتماد الفاتورة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->route('purchases.invoices.show', $id)
                ->with('error', 'حدث خطأ أثناء اعتماد الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Record a payment for the specified purchase invoice.
     * تسجيل دفعة لفاتورة مشتريات
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordPayment(Request $request, $id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->remaining_amount,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // تسجيل الدفعة
            $payment = $invoice->payments()->create([
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['reference'],
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // تحديث المبلغ المدفوع والمتبقي
            $invoice->paid_amount += $validated['amount'];
            $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
            $invoice->updatePaymentStatus();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدفعة بنجاح',
                'data' => $payment
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الدفعة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add an item to the specified purchase invoice.
     * إضافة صنف لفاتورة مشتريات
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addItem(Request $request, $id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);

        if ($invoice->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إضافة أصناف لفاتورة معتمدة'
            ], 403);
        }

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $total = ($validated['quantity'] * $validated['unit_price']) - ($validated['discount'] ?? 0);

            $item = $invoice->items()->create([
                'item_id' => $validated['item_id'],
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'discount' => $validated['discount'] ?? 0,
                'total' => $total,
            ]);

            // تحديث إجماليات الفاتورة
            $invoice->subtotal += ($validated['quantity'] * $validated['unit_price']);
            $invoice->discount_amount += ($validated['discount'] ?? 0);
            $invoice->total_amount = ($invoice->subtotal - $invoice->discount_amount) + $invoice->tax_amount;
            $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
            $invoice->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الصنف بنجاح',
                'data' => $item
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الصنف',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an item in the specified purchase invoice.
     * تحديث صنف في فاتورة مشتريات
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $invoiceId
     * @param  int  $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateItem(Request $request, $invoiceId, $itemId)
    {
        $invoice = PurchaseInvoice::findOrFail($invoiceId);
        $item = $invoice->items()->findOrFail($itemId);

        if ($invoice->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعديل أصناف فاتورة معتمدة'
            ], 403);
        }

        $validated = $request->validate([
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // حفظ القيم القديمة
            $oldSubtotal = $item->quantity * $item->unit_price;
            $oldDiscount = $item->discount;

            // تحديث الصنف
            $total = ($validated['quantity'] * $validated['unit_price']) - ($validated['discount'] ?? 0);
            
            $item->update([
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'discount' => $validated['discount'] ?? 0,
                'total' => $total,
            ]);

            // تحديث إجماليات الفاتورة
            $newSubtotal = $validated['quantity'] * $validated['unit_price'];
            $newDiscount = $validated['discount'] ?? 0;

            $invoice->subtotal = $invoice->subtotal - $oldSubtotal + $newSubtotal;
            $invoice->discount_amount = $invoice->discount_amount - $oldDiscount + $newDiscount;
            $invoice->total_amount = ($invoice->subtotal - $invoice->discount_amount) + $invoice->tax_amount;
            $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
            $invoice->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الصنف بنجاح',
                'data' => $item
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الصنف',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an item from the specified purchase invoice.
     * حذف صنف من فاتورة مشتريات
     *
     * @param  int  $invoiceId
     * @param  int  $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteItem($invoiceId, $itemId)
    {
        $invoice = PurchaseInvoice::findOrFail($invoiceId);
        $item = $invoice->items()->findOrFail($itemId);

        if ($invoice->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف أصناف من فاتورة معتمدة'
            ], 403);
        }

        // التأكد من وجود أكثر من صنف واحد
        if ($invoice->items()->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'يجب أن تحتوي الفاتورة على صنف واحد على الأقل'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // تحديث إجماليات الفاتورة
            $invoice->subtotal -= ($item->quantity * $item->unit_price);
            $invoice->discount_amount -= $item->discount;
            $invoice->total_amount = ($invoice->subtotal - $invoice->discount_amount) + $invoice->tax_amount;
            $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
            $invoice->save();

            // حذف الصنف
            $item->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الصنف بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الصنف',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

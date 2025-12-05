<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Http\Requests\StockTransferRequest;
use App\Services\StockTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\StockTransferException;

/**
 * المتحكم الخاص بتحويلات المخزون.
 */
class StockTransferController extends Controller
{
    protected $stockTransferService;

    /**
     * تهيئة المتحكم وحقن خدمة التحويلات.
     */
    public function __construct(StockTransferService $stockTransferService)
    {
        $this->stockTransferService = $stockTransferService;
        // تطبيق سياسة التخويل على جميع الدوال
        $this->authorizeResource(StockTransfer::class, 'stock_transfer');
    }

    /**
     * عرض قائمة بتحويلات المخزون.
     */
    public function index()
    {
        $transfers = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'creator'])
            ->latest()
            ->paginate(10);

        return view('stock_transfers.index', compact('transfers'));
    }

    /**
     * عرض نموذج إنشاء تحويل جديد.
     */
    public function create()
    {
        // يجب تمرير قائمة المخازن والمواد للواجهة
        // $warehouses = Warehouse::all();
        // $items = Item::all();
        return view('stock_transfers.create'); // , compact('warehouses', 'items')
    }

    /**
     * تخزين تحويل مخزون جديد في قاعدة البيانات.
     */
    public function store(StockTransferRequest $request)
    {
        try {
            $transfer = $this->stockTransferService->createTransfer(
                $request->validated(),
                Auth::id()
            );

            return redirect()->route('stock_transfers.show', $transfer)
                ->with('success', 'تم إنشاء طلب التحويل بنجاح. بانتظار الموافقة.');

        } catch (\Exception $e) {
            // معالجة الأخطاء العامة
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء طلب التحويل: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل تحويل مخزون معين.
     */
    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['details.item', 'fromWarehouse', 'toWarehouse', 'creator', 'approver']);
        return view('stock_transfers.show', compact('stockTransfer'));
    }

    /**
     * عرض نموذج تعديل تحويل مخزون.
     */
    public public function edit(StockTransfer $stockTransfer)
    {
        // يتم التحقق من حالة التحويل في Policy
        // $warehouses = Warehouse::all();
        // $items = Item::all();
        return view('stock_transfers.edit', compact('stockTransfer')); // , compact('warehouses', 'items')
    }

    /**
     * تحديث تحويل مخزون معين في قاعدة البيانات.
     */
    public function update(StockTransferRequest $request, StockTransfer $stockTransfer)
    {
        // يتم التحقق من حالة التحويل في Policy
        $this->authorize('update', $stockTransfer);

        try {
            $stockTransfer->update($request->validated());
            // تحديث التفاصيل يتطلب منطقاً إضافياً في الخدمة، هنا نكتفي بتحديث الرأس
            // $this->stockTransferService->updateTransferDetails($stockTransfer, $request->input('details'));

            return redirect()->route('stock_transfers.show', $stockTransfer)
                ->with('success', 'تم تحديث طلب التحويل بنجاح.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث طلب التحويل: ' . $e->getMessage());
        }
    }

    /**
     * حذف تحويل مخزون معين من قاعدة البيانات.
     */
    public function destroy(StockTransfer $stockTransfer)
    {
        // يتم التحقق من حالة التحويل في Policy
        $this->authorize('delete', $stockTransfer);

        try {
            $stockTransfer->delete();
            return redirect()->route('stock_transfers.index')
                ->with('success', 'تم حذف طلب التحويل بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف طلب التحويل: ' . $e->getMessage());
        }
    }

    /**
     * الموافقة على طلب تحويل مخزون.
     */
    public function approve(StockTransfer $stockTransfer)
    {
        // التحقق من الصلاحية والحالة عبر Policy
        $this->authorize('approve', $stockTransfer);

        try {
            $transfer = $this->stockTransferService->approveTransfer(
                $stockTransfer,
                Auth::id()
            );

            return redirect()->route('stock_transfers.show', $transfer)
                ->with('success', 'تمت الموافقة على التحويل بنجاح. تم خصم وإضافة المخزون.');

        } catch (StockTransferException $e) {
            // معالجة الأخطاء الخاصة بمنطق المخزون (مثل عدم توفر كمية)
            return back()->with('error', 'فشل الموافقة: ' . $e->getMessage());
        } catch (\Exception $e) {
            // معالجة الأخطاء العامة
            return back()->with('error', 'حدث خطأ غير متوقع أثناء الموافقة: ' . $e->getMessage());
        }
    }
}

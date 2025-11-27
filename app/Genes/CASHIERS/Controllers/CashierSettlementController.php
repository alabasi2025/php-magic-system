<?php

namespace App\Genes\CASHIERS\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Genes\CASHIERS\Models\CashierSettlement; // افتراض وجود نموذج بهذا الاسم

/**
 * @group CASHIERS
 *
 * إدارة تسويات الصرافين (Cashier Settlements).
 * يوفر واجهة API لإدارة عمليات تسوية الصرافين.
 */
class CashierSettlementController extends Controller
{
    /**
     * عرض قائمة بجميع تسويات الصرافين.
     *
     * @queryParam page int رقم الصفحة. مثال: 1
     * @queryParam per_page int عدد العناصر في الصفحة. مثال: 15
     * @queryParam cashier_id int فلترة حسب معرف الصراف. مثال: 1
     * @queryParam status string فلترة حسب حالة التسوية. مثال: 'pending'
     * @responseFile status=200 resources/responses/cashier_settlements/index.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::info('CashierSettlementController@index called', $request->all());

        // منطق استرجاع البيانات مع الفلترة والتقسيم
        $settlements = CashierSettlement::query()
            ->when($request->has('cashier_id'), function ($query) use ($request) {
                $query->where('cashier_id', $request->cashier_id);
            })
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'message' => 'تم استرجاع قائمة تسويات الصرافين بنجاح.',
            'data' => $settlements->items(),
            'meta' => [
                'total' => $settlements->total(),
                'per_page' => $settlements->perPage(),
                'current_page' => $settlements->currentPage(),
                'last_page' => $settlements->lastPage(),
            ]
        ], 200);
    }

    /**
     * عرض نموذج لإنشاء تسوية صراف جديدة.
     *
     * هذه الوظيفة عادة ما تكون مخصصة لواجهات الويب ولا تستخدم في API بشكل مباشر.
     * @response status=200 { "message": "نموذج إنشاء تسوية صراف جاهز." }
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        return response()->json(['message' => 'نموذج إنشاء تسوية صراف جاهز.'], 200);
    }

    /**
     * تخزين تسوية صراف جديدة في قاعدة البيانات.
     *
     * @bodyParam cashier_id int required معرف الصراف. مثال: 1
     * @bodyParam amount numeric required المبلغ المسوّى. مثال: 1500.50
     * @bodyParam notes string ملاحظات حول التسوية. مثال: 'تسوية نهاية اليوم'
     * @responseFile status=201 resources/responses/cashier_settlements/store.json
     * @response status=422 { "message": "خطأ في التحقق من البيانات", "errors": { "cashier_id": ["حقل معرف الصراف مطلوب."] } }
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cashier_id' => 'required|integer|exists:cashiers,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // منطق إنشاء التسوية
        $settlement = CashierSettlement::create([
            'cashier_id' => $validatedData['cashier_id'],
            'amount' => $validatedData['amount'],
            'status' => 'pending', // حالة افتراضية
            'notes' => $validatedData['notes'],
            'settlement_date' => now(),
        ]);

        Log::info('New CashierSettlement created', ['id' => $settlement->id]);

        return response()->json([
            'message' => 'تم إنشاء تسوية الصراف بنجاح.',
            'data' => $settlement
        ], 201);
    }

    /**
     * عرض تفاصيل تسوية صراف محددة.
     *
     * @urlParam settlement int required معرف التسوية. مثال: 1
     * @responseFile status=200 resources/responses/cashier_settlements/show.json
     * @response status=404 { "message": "لم يتم العثور على التسوية." }
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(CashierSettlement $settlement)
    {
        return response()->json([
            'message' => 'تم استرجاع تفاصيل تسوية الصراف بنجاح.',
            'data' => $settlement
        ], 200);
    }

    /**
     * عرض نموذج لتعديل تسوية صراف محددة.
     *
     * هذه الوظيفة عادة ما تكون مخصصة لواجهات الويب ولا تستخدم في API بشكل مباشر.
     * @urlParam settlement int required معرف التسوية. مثال: 1
     * @response status=200 { "message": "نموذج تعديل تسوية صراف جاهز." }
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(CashierSettlement $settlement)
    {
        return response()->json(['message' => 'نموذج تعديل تسوية صراف جاهز.'], 200);
    }

    /**
     * تحديث تسوية صراف محددة في قاعدة البيانات.
     *
     * @urlParam settlement int required معرف التسوية. مثال: 1
     * @bodyParam amount numeric المبلغ المسوّى. مثال: 1600.00
     * @bodyParam status string حالة التسوية. مثال: 'approved'
     * @responseFile status=200 resources/responses/cashier_settlements/update.json
     * @response status=404 { "message": "لم يتم العثور على التسوية." }
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, CashierSettlement $settlement)
    {
        $validatedData = $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|in:pending,approved,rejected',
            'notes' => 'nullable|string|max:500',
        ]);

        // منطق تحديث التسوية
        $settlement->update($validatedData);

        Log::info('CashierSettlement updated', ['id' => $settlement->id]);

        return response()->json([
            'message' => 'تم تحديث تسوية الصراف بنجاح.',
            'data' => $settlement
        ], 200);
    }

    /**
     * حذف تسوية صراف محددة من قاعدة البيانات.
     *
     * @urlParam settlement int required معرف التسوية. مثال: 1
     * @response status=204 لا يوجد محتوى
     * @response status=404 { "message": "لم يتم العثور على التسوية." }
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CashierSettlement $settlement)
    {
        // منطق الحذف
        $settlement->delete();

        Log::info('CashierSettlement deleted', ['id' => $settlement->id]);

        return response()->json(null, 204);
    }
}
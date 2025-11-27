<?php

namespace App\Genes\CASHIERS\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Genes\CASHIERS\Models\CashierTransaction; // افتراض وجود نموذج CashierTransaction

/**
 * @group CASHIERS - إدارة معاملات الصرافين
 *
 * إدارة وعرض معاملات الصرافين.
 */
class CashierTransactionController extends Controller
{
    /**
     * عرض قائمة بجميع معاملات الصرافين.
     *
     * @queryParam page int رقم الصفحة. مثال: 1
     * @queryParam per_page int عدد العناصر في الصفحة. مثال: 15
     * @queryParam search string مصطلح البحث. مثال: "إيداع"
     * @responseFile status=200 storage/responses/cashier_transactions/index.json
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = CashierTransaction::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('transaction_type', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
        }

        $transactions = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'تم استرداد قائمة معاملات الصرافين بنجاح.',
            'data' => $transactions
        ]);
    }

    /**
     * عرض نموذج لإنشاء معاملة صراف جديدة.
     *
     * هذه الوظيفة مخصصة لواجهات الويب ولا تُستخدم كنقطة نهاية API.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // يمكن تمرير بيانات النماذج اللازمة لإنشاء معاملة
        return view('cashiers.transactions.create');
    }

    /**
     * تخزين معاملة صراف جديدة في قاعدة البيانات.
     *
     * @bodyParam cashier_id int required معرف الصراف. مثال: 1
     * @bodyParam transaction_type string required نوع المعاملة (إيداع، سحب، تحويل). مثال: "إيداع"
     * @bodyParam amount numeric required مبلغ المعاملة. مثال: 100.50
     * @bodyParam description string وصف المعاملة. مثال: "إيداع نقدي من العميل س"
     * @responseFile status=201 storage/responses/cashier_transactions/store.json
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cashier_id' => 'required|integer|exists:cashiers,id',
            'transaction_type' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'فشل التحقق من صحة البيانات.', 'errors' => $validator->errors()], 422);
        }

        $transaction = CashierTransaction::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء معاملة الصراف بنجاح.',
            'data' => $transaction
        ], 201);
    }

    /**
     * عرض تفاصيل معاملة صراف محددة.
     *
     * @urlParam transaction int required معرف المعاملة. مثال: 1
     * @responseFile status=200 storage/responses/cashier_transactions/show.json
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $transaction = CashierTransaction::find($id);

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'المعاملة غير موجودة.'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم استرداد تفاصيل المعاملة بنجاح.',
            'data' => $transaction
        ]);
    }

    /**
     * عرض نموذج لتعديل معاملة صراف موجودة.
     *
     * هذه الوظيفة مخصصة لواجهات الويب ولا تُستخدم كنقطة نهاية API.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $transaction = CashierTransaction::findOrFail($id);
        // يمكن تمرير بيانات المعاملة إلى نموذج التعديل
        return view('cashiers.transactions.edit', compact('transaction'));
    }

    /**
     * تحديث معاملة صراف محددة في قاعدة البيانات.
     *
     * @urlParam transaction int required معرف المعاملة. مثال: 1
     * @bodyParam transaction_type string نوع المعاملة (إيداع، سحب، تحويل). مثال: "سحب"
     * @bodyParam amount numeric مبلغ المعاملة. مثال: 50.00
     * @bodyParam description string وصف المعاملة. مثال: "سحب نقدي للعميل ص"
     * @responseFile status=200 storage/responses/cashier_transactions/update.json
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $transaction = CashierTransaction::find($id);

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'المعاملة غير موجودة.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'cashier_id' => 'sometimes|required|integer|exists:cashiers,id',
            'transaction_type' => 'sometimes|required|string|max:50',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'فشل التحقق من صحة البيانات.', 'errors' => $validator->errors()], 422);
        }

        $transaction->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث معاملة الصراف بنجاح.',
            'data' => $transaction
        ]);
    }

    /**
     * حذف معاملة صراف محددة من قاعدة البيانات.
     *
     * @urlParam transaction int required معرف المعاملة. مثال: 1
     * @response status=204 لا يوجد محتوى
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $transaction = CashierTransaction::find($id);

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'المعاملة غير موجودة.'], 404);
        }

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف معاملة الصراف بنجاح.'
        ], 204);
    }
}
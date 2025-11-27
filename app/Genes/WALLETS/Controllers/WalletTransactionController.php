<?php

namespace App\Genes\WALLETS\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Genes\WALLETS\Models\WalletTransaction; // افتراض وجود هذا الموديل
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WalletTransactionController extends Controller
{
    /**
     * عرض قائمة بجميع معاملات المحافظ.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // يمكن إضافة منطق التصفية والترتيب والبحث هنا
            $transactions = WalletTransaction::paginate(15);

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet transactions retrieved successfully.',
                'data' => $transactions
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error retrieving wallet transactions: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve wallet transactions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض نموذج إنشاء معاملة محفظة جديدة.
     * (عادةً ما تكون هذه الوظيفة لواجهات الويب، ولن يتم استخدامها في API)
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // في سياق API، يمكن أن تعيد بيانات أولية أو قائمة بالخيارات المتاحة
        return response()->json([
            'status' => 'info',
            'message' => 'Ready to create a new wallet transaction. Required fields: wallet_id, type, amount, description.'
        ], 200);
    }

    /**
     * تخزين معاملة محفظة جديدة في قاعدة البيانات.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wallet_id' => 'required|exists:wallets,id',
            'type' => 'required|in:deposit,withdrawal,transfer',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transaction = WalletTransaction::create($request->all());

            // منطق معالجة المعاملة الفعلي (مثل تحديث رصيد المحفظة) يجب أن يتم هنا أو في خدمة منفصلة
            // ...

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet transaction created successfully.',
                'data' => $transaction
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error creating wallet transaction: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create wallet transaction.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض معاملة محفظة محددة.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $transaction = WalletTransaction::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet transaction retrieved successfully.',
                'data' => $transaction
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wallet transaction not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error retrieving wallet transaction {$id}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve wallet transaction.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض نموذج تعديل معاملة محفظة محددة.
     * (عادةً ما تكون هذه الوظيفة لواجهات الويب، ولن يتم استخدامها في API)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $transaction = WalletTransaction::findOrFail($id);

            return response()->json([
                'status' => 'info',
                'message' => 'Ready to edit wallet transaction.',
                'data' => $transaction
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wallet transaction not found.'
            ], 404);
        }
    }

    /**
     * تحديث معاملة محفظة محددة في قاعدة البيانات.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'wallet_id' => 'sometimes|required|exists:wallets,id',
            'type' => 'sometimes|required|in:deposit,withdrawal,transfer',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transaction = WalletTransaction::findOrFail($id);

            // ملاحظة: تحديث المعاملات المالية قد يتطلب منطقًا معقدًا أو قد يكون محظورًا تمامًا.
            // هنا نفترض أن بعض الحقول قابلة للتحديث لأغراض إدارية.
            $transaction->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet transaction updated successfully.',
                'data' => $transaction
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wallet transaction not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error updating wallet transaction {$id}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update wallet transaction.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف معاملة محفظة محددة من قاعدة البيانات.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $transaction = WalletTransaction::findOrFail($id);
            $transaction->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet transaction deleted successfully.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wallet transaction not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error deleting wallet transaction {$id}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete wallet transaction.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
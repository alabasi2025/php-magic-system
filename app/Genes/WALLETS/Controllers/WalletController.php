<?php

namespace App\Genes\WALLETS\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet; // افتراض وجود نموذج Wallet
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * عرض قائمة بجميع المحافظ (Wallets).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $wallets = Wallet::paginate(15);

        return response()->json([
            'status' => 'success',
            'message' => 'Wallets retrieved successfully',
            'data' => $wallets
        ]);
    }

    /**
     * عرض نموذج إنشاء محفظة جديدة. (لواجهات الويب فقط، لكن ندرجها للامتثال لـ CRUD)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        // في سياق API، يمكن أن تعيد البيانات اللازمة لإنشاء النموذج (مثل قوائم منسدلة)
        return response()->json([
            'status' => 'success',
            'message' => 'Ready to create a new wallet',
            'data' => [
                // 'currencies' => Currency::all(),
            ]
        ]);
    }

    /**
     * تخزين محفظة جديدة في قاعدة البيانات.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'currency' => 'required|string|max:3',
            'balance' => 'required|numeric|min:0',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        try {
            $wallet = Wallet::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet created successfully',
                'data' => $wallet
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create wallet: ' . $e->getMessage()], 500);
        }
    }

    /**
     * عرض محفظة محددة.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $wallet = Wallet::find($id);

        if (!$wallet) {
            return response()->json(['status' => 'error', 'message' => 'Wallet not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet retrieved successfully',
            'data' => $wallet
        ]);
    }

    /**
     * عرض نموذج تعديل محفظة. (لواجهات الويب فقط، لكن ندرجها للامتثال لـ CRUD)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $wallet = Wallet::find($id);

        if (!$wallet) {
            return response()->json(['status' => 'error', 'message' => 'Wallet not found'], 404);
        }

        // في سياق API، يمكن أن تعيد البيانات اللازمة لتعديل النموذج
        return response()->json([
            'status' => 'success',
            'message' => 'Ready to edit wallet',
            'data' => $wallet
        ]);
    }

    /**
     * تحديث محفظة محددة في قاعدة البيانات.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $wallet = Wallet::find($id);

        if (!$wallet) {
            return response()->json(['status' => 'error', 'message' => 'Wallet not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|required|exists:users,id',
            'currency' => 'sometimes|required|string|max:3',
            'balance' => 'sometimes|required|numeric|min:0',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 422);
        }

        try {
            $wallet->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet updated successfully',
                'data' => $wallet
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update wallet: ' . $e->getMessage()], 500);
        }
    }

    /**
     * حذف محفظة محددة من قاعدة البيانات.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $wallet = Wallet::find($id);

        if (!$wallet) {
            return response()->json(['status' => 'error', 'message' => 'Wallet not found'], 404);
        }

        try {
            $wallet->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete wallet: ' . $e->getMessage()], 500);
        }
    }
}
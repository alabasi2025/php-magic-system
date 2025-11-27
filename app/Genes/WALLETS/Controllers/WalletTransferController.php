<?php

namespace App\Genes\WALLETS\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Genes\WALLETS\Models\WalletTransfer; // افتراض وجود نموذج بهذا الاسم
use App\Genes\WALLETS\Requests\WalletTransferRequest; // افتراض وجود Request Form

class WalletTransferController extends Controller
{
    /**
     * عرض قائمة بالموارد (التحويلات).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // يجب أن تكون هذه الوظيفة محمية بـ API Guard
        // والتحقق من صلاحيات المستخدم لعرض التحويلات
        $transfers = WalletTransfer::paginate(15);

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet transfers retrieved successfully.',
            'data' => $transfers
        ]);
    }

    /**
     * عرض نموذج لإنشاء مورد جديد. (لواجهة الويب فقط، يمكن أن تكون فارغة لـ API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        return response()->json([
            'status' => 'info',
            'message' => 'This endpoint is for web form creation, use POST /api/wallets/transfers to store data.'
        ]);
    }

    /**
     * تخزين مورد تم إنشاؤه حديثًا في التخزين.
     *
     * @param  \App\Genes\WALLETS\Requests\WalletTransferRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(WalletTransferRequest $request)
    {
        // التحقق من صحة البيانات يتم عبر WalletTransferRequest
        
        // منطق التحويل الفعلي للمحفظة
        // يجب أن يتم داخل Transaction لضمان سلامة البيانات
        try {
            $transfer = WalletTransfer::create($request->validated());
            
            // هنا يجب إضافة منطق تحديث أرصدة المحافظ
            // مثال:
            // $transfer->sourceWallet->decrement('balance', $transfer->amount);
            // $transfer->destinationWallet->increment('balance', $transfer->amount);

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet transfer created successfully.',
                'data' => $transfer
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create wallet transfer: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * عرض المورد المحدد.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $transfer = WalletTransfer::find($id);

        if (!$transfer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wallet transfer not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet transfer retrieved successfully.',
            'data' => $transfer
        ]);
    }

    /**
     * عرض نموذج لتحرير المورد المحدد. (لواجهة الويب فقط، يمكن أن تكون فارغة لـ API)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        return response()->json([
            'status' => 'info',
            'message' => 'This endpoint is for web form editing, use PUT/PATCH /api/wallets/transfers/{id} to update data.'
        ]);
    }

    /**
     * تحديث المورد المحدد في التخزين.
     *
     * ملاحظة: في سياق تحويلات المحافظ، قد لا يكون التحديث منطقيًا بعد الإنشاء.
     * هذه الوظيفة قد تستخدم لتحديث حالة التحويل (مثل: قيد الانتظار -> مكتمل).
     *
     * @param  \App\Genes\WALLETS\Requests\WalletTransferRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(WalletTransferRequest $request, $id)
    {
        $transfer = WalletTransfer::find($id);

        if (!$transfer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wallet transfer not found.'
            ], 404);
        }

        // منطق التحديث
        try {
            $transfer->update($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet transfer updated successfully.',
                'data' => $transfer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update wallet transfer: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * إزالة المورد المحدد من التخزين.
     *
     * ملاحظة: حذف التحويلات قد يتطلب منطقًا معقدًا لعكس العملية.
     * قد تكون هذه الوظيفة مخصصة لإلغاء التحويلات التي لم تتم معالجتها بعد.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $transfer = WalletTransfer::find($id);

        if (!$transfer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wallet transfer not found.'
            ], 404);
        }

        // منطق الحذف/الإلغاء
        try {
            $transfer->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet transfer deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete wallet transfer: ' . $e->getMessage()
            ], 500);
        }
    }
}
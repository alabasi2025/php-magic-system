<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Controllers;

use App\Http\Controllers\Controller;
use App\Genes\INTERMEDIATE_ACCOUNTS\Services\IntermediateAccountService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Controller: IntermediateAccountController
 * 
 * 💡 الفكرة:
 * كونترولر يدير واجهات برمجة التطبيقات (APIs) للحسابات الوسيطة.
 * 
 * 🎯 المسؤوليات:
 * - إنشاء وتعديل وحذف الحسابات الوسيطة
 * - عرض قائمة الحسابات الوسيطة
 * - تفعيل وتعطيل الحسابات الوسيطة
 * - عرض تفاصيل الحساب الوسيط
 * 
 * @version 1.0.0
 * @since 2025-11-27
 */
class IntermediateAccountController extends Controller
{
    /**
     * @var IntermediateAccountService
     */
    protected $service;

    /**
     * Constructor.
     *
     * @param IntermediateAccountService $service
     */
    public function __construct(IntermediateAccountService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of intermediate accounts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = \App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateAccount::with([
                'mainAccount',
                'intermediateAccount1',
                'intermediateAccount2',
                'intermediateAccount3',
            ]);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('main_account_id')) {
                $query->where('main_account_id', $request->main_account_id);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $accounts = $query->paginate($perPage);

            // Add computed fields
            $accounts->getCollection()->transform(function ($account) {
                return [
                    'id' => $account->id,
                    'main_account' => [
                        'id' => $account->main_account_id,
                        'name' => $account->mainAccount->name ?? 'غير معروف',
                    ],
                    'intermediate_account_1' => [
                        'id' => $account->intermediate_account_1_id,
                        'name' => $account->intermediateAccount1->name ?? 'غير معروف',
                    ],
                    'intermediate_account_2' => $account->intermediate_account_2_id ? [
                        'id' => $account->intermediate_account_2_id,
                        'name' => $account->intermediateAccount2->name ?? 'غير معروف',
                    ] : null,
                    'intermediate_account_3' => $account->intermediate_account_3_id ? [
                        'id' => $account->intermediate_account_3_id,
                        'name' => $account->intermediateAccount3->name ?? 'غير معروف',
                    ] : null,
                    'status' => $account->status,
                    'current_balance' => $account->getCurrentBalance(),
                    'is_balanced' => $account->isBalanced(),
                    'unlinked_count' => $account->getUnlinkedTransactionsCount(),
                    'notes' => $account->notes,
                    'created_at' => $account->created_at,
                    'updated_at' => $account->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $accounts,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created intermediate account.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'main_account_id' => 'required|integer|exists:accounts,id',
                'intermediate_account_1_id' => 'required|integer|exists:accounts,id',
                'intermediate_account_2_id' => 'nullable|integer|exists:accounts,id',
                'intermediate_account_3_id' => 'nullable|integer|exists:accounts,id',
                'notes' => 'nullable|string|max:1000',
            ]);

            $intermediateAccount = $this->service->create(
                $validated['main_account_id'],
                $validated['intermediate_account_1_id'],
                $validated['intermediate_account_2_id'] ?? null,
                $validated['intermediate_account_3_id'] ?? null,
                $validated['notes'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحساب الوسيط بنجاح',
                'data' => $intermediateAccount->load([
                    'mainAccount',
                    'intermediateAccount1',
                    'intermediateAccount2',
                    'intermediateAccount3',
                ]),
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified intermediate account.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $account = \App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateAccount::with([
                'mainAccount',
                'intermediateAccount1',
                'intermediateAccount2',
                'intermediateAccount3',
                'creator',
                'updater',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $account->id,
                    'main_account' => [
                        'id' => $account->main_account_id,
                        'name' => $account->mainAccount->name ?? 'غير معروف',
                        'code' => $account->mainAccount->code ?? null,
                    ],
                    'intermediate_account_1' => [
                        'id' => $account->intermediate_account_1_id,
                        'name' => $account->intermediateAccount1->name ?? 'غير معروف',
                        'code' => $account->intermediateAccount1->code ?? null,
                    ],
                    'intermediate_account_2' => $account->intermediate_account_2_id ? [
                        'id' => $account->intermediate_account_2_id,
                        'name' => $account->intermediateAccount2->name ?? 'غير معروف',
                        'code' => $account->intermediateAccount2->code ?? null,
                    ] : null,
                    'intermediate_account_3' => $account->intermediate_account_3_id ? [
                        'id' => $account->intermediate_account_3_id,
                        'name' => $account->intermediateAccount3->name ?? 'غير معروف',
                        'code' => $account->intermediateAccount3->code ?? null,
                    ] : null,
                    'status' => $account->status,
                    'balance' => [
                        'total_receipts' => $account->getTotalReceipts(),
                        'total_payments' => $account->getTotalPayments(),
                        'current_balance' => $account->getCurrentBalance(),
                        'unlinked_receipts' => $account->getUnlinkedAmount('receipt'),
                        'unlinked_payments' => $account->getUnlinkedAmount('payment'),
                        'is_balanced' => $account->isBalanced(),
                    ],
                    'statistics' => [
                        'total_transactions' => $account->transactions()->count(),
                        'unlinked_count' => $account->getUnlinkedTransactionsCount(),
                        'pending_count' => $account->transactions()->where('status', 'pending')->count(),
                        'completed_count' => $account->transactions()->where('status', 'completed')->count(),
                    ],
                    'notes' => $account->notes,
                    'created_by' => $account->creator->name ?? 'غير معروف',
                    'updated_by' => $account->updater->name ?? null,
                    'created_at' => $account->created_at,
                    'updated_at' => $account->updated_at,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified intermediate account.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'intermediate_account_1_id' => 'sometimes|required|integer|exists:accounts,id',
                'intermediate_account_2_id' => 'nullable|integer|exists:accounts,id',
                'intermediate_account_3_id' => 'nullable|integer|exists:accounts,id',
                'notes' => 'nullable|string|max:1000',
            ]);

            $intermediateAccount = $this->service->update($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الحساب الوسيط بنجاح',
                'data' => $intermediateAccount->load([
                    'mainAccount',
                    'intermediateAccount1',
                    'intermediateAccount2',
                    'intermediateAccount3',
                ]),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified intermediate account.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الحساب الوسيط بنجاح',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Activate an intermediate account.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function activate(int $id): JsonResponse
    {
        try {
            $this->service->activate($id);

            return response()->json([
                'success' => true,
                'message' => 'تم تفعيل الحساب الوسيط بنجاح',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Deactivate an intermediate account.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deactivate(int $id): JsonResponse
    {
        try {
            $this->service->deactivate($id);

            return response()->json([
                'success' => true,
                'message' => 'تم تعطيل الحساب الوسيط بنجاح',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get unbalanced intermediate accounts.
     *
     * @return JsonResponse
     */
    public function unbalanced(): JsonResponse
    {
        try {
            $accounts = $this->service->getUnbalancedAccounts();

            return response()->json([
                'success' => true,
                'data' => $accounts->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'main_account' => $account->mainAccount->name ?? 'غير معروف',
                        'current_balance' => $account->getCurrentBalance(),
                        'unlinked_count' => $account->getUnlinkedTransactionsCount(),
                    ];
                }),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get intermediate accounts with pending transactions.
     *
     * @return JsonResponse
     */
    public function withPendingTransactions(): JsonResponse
    {
        try {
            $accounts = $this->service->getAccountsWithPendingTransactions();

            return response()->json([
                'success' => true,
                'data' => $accounts->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'main_account' => $account->mainAccount->name ?? 'غير معروف',
                        'unlinked_count' => $account->getUnlinkedTransactionsCount(),
                        'current_balance' => $account->getCurrentBalance(),
                    ];
                }),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

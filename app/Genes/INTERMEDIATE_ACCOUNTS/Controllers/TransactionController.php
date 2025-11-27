<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Controllers;

use App\Http\Controllers\Controller;
use App\Genes\INTERMEDIATE_ACCOUNTS\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * 🧬 Gene: INTERMEDIATE_ACCOUNTS
 * Controller: TransactionController
 * 
 * 💡 الفكرة:
 * كونترولر يدير واجهات برمجة التطبيقات (APIs) للعمليات والربط.
 * 
 * 🎯 المسؤوليات:
 * - تسجيل عمليات القبض والصرف
 * - ربط العمليات المعاكسة
 * - فك الربط
 * - الربط التلقائي
 * - عرض تفاصيل العمليات
 * 
 * @version 1.0.0
 * @since 2025-11-27
 */
class TransactionController extends Controller
{
    /**
     * @var TransactionService
     */
    protected $service;

    /**
     * Constructor.
     *
     * @param TransactionService $service
     */
    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of transactions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = \App\Genes\INTERMEDIATE_ACCOUNTS\Models\IntermediateTransaction::with([
                'intermediateAccount.mainAccount',
                'creator',
            ]);

            // Apply filters
            if ($request->has('intermediate_account_id')) {
                $query->where('intermediate_account_id', $request->intermediate_account_id);
            }

            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('date_from')) {
                $query->where('transaction_date', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('transaction_date', '<=', $request->date_to);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'transaction_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $transactions = $query->paginate($perPage);

            // Add computed fields
            $transactions->getCollection()->transform(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'intermediate_account' => [
                        'id' => $transaction->intermediate_account_id,
                        'main_account_name' => $transaction->intermediateAccount->mainAccount->name ?? 'غير معروف',
                    ],
                    'type' => $transaction->type,
                    'type_label' => $transaction->getTypeLabel(),
                    'amount' => $transaction->amount,
                    'linked_amount' => $transaction->getLinkedAmount(),
                    'available_amount' => $transaction->getAvailableAmount(),
                    'description' => $transaction->description,
                    'reference_number' => $transaction->reference_number,
                    'status' => $transaction->status,
                    'status_label' => $transaction->getStatusLabel(),
                    'transaction_date' => $transaction->transaction_date,
                    'created_by' => $transaction->creator->name ?? 'غير معروف',
                    'created_at' => $transaction->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transactions,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created transaction.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'intermediate_account_id' => 'required|integer|exists:intermediate_accounts,id',
                'type' => 'required|in:receipt,payment',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'required|string|max:1000',
                'reference_number' => 'nullable|string|max:100',
                'metadata' => 'nullable|array',
            ]);

            $transaction = $this->service->recordTransaction(
                $validated['intermediate_account_id'],
                $validated['type'],
                $validated['amount'],
                $validated['description'],
                $validated['reference_number'] ?? null,
                $validated['metadata'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل العملية بنجاح',
                'data' => $transaction->load(['intermediateAccount.mainAccount', 'creator']),
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified transaction.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $transaction = $this->service->getTransactionWithLinks($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $transaction->id,
                    'intermediate_account' => [
                        'id' => $transaction->intermediate_account_id,
                        'main_account_name' => $transaction->intermediateAccount->mainAccount->name ?? 'غير معروف',
                    ],
                    'type' => $transaction->type,
                    'type_label' => $transaction->getTypeLabel(),
                    'amount' => $transaction->amount,
                    'linked_amount' => $transaction->getLinkedAmount(),
                    'available_amount' => $transaction->getAvailableAmount(),
                    'description' => $transaction->description,
                    'reference_number' => $transaction->reference_number,
                    'metadata' => $transaction->metadata,
                    'status' => $transaction->status,
                    'status_label' => $transaction->getStatusLabel(),
                    'transaction_date' => $transaction->transaction_date,
                    'created_by' => $transaction->creator->name ?? 'غير معروف',
                    'created_at' => $transaction->created_at,
                    'source_links' => $transaction->sourceLinks->map(function ($link) {
                        return [
                            'link_id' => $link->id,
                            'target_transaction_id' => $link->target_transaction_id,
                            'target_type' => $link->targetTransaction->type,
                            'target_description' => $link->targetTransaction->description,
                            'linked_amount' => $link->linked_amount,
                            'linked_at' => $link->linked_at,
                            'notes' => $link->notes,
                        ];
                    }),
                    'target_links' => $transaction->targetLinks->map(function ($link) {
                        return [
                            'link_id' => $link->id,
                            'source_transaction_id' => $link->source_transaction_id,
                            'source_type' => $link->sourceTransaction->type,
                            'source_description' => $link->sourceTransaction->description,
                            'linked_amount' => $link->linked_amount,
                            'linked_at' => $link->linked_at,
                            'notes' => $link->notes,
                        ];
                    }),
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
     * Link transactions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function link(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'links' => 'required|array|min:1',
                'links.*.source_id' => 'required|integer|exists:intermediate_transactions,id',
                'links.*.target_id' => 'required|integer|exists:intermediate_transactions,id',
                'links.*.amount' => 'required|numeric|min:0.01',
                'notes' => 'nullable|string|max:1000',
            ]);

            $links = $this->service->linkTransactions(
                $validated['links'],
                $validated['notes'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'تم ربط العمليات بنجاح',
                'data' => $links,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Unlink transactions.
     *
     * @param int $linkId
     * @return JsonResponse
     */
    public function unlink(int $linkId): JsonResponse
    {
        try {
            $this->service->unlinkTransactions($linkId);

            return response()->json([
                'success' => true,
                'message' => 'تم فك الربط بنجاح',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Auto-link transactions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function autoLink(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'intermediate_account_id' => 'required|integer|exists:intermediate_accounts,id',
            ]);

            $links = $this->service->autoLinkTransactions($validated['intermediate_account_id']);

            return response()->json([
                'success' => true,
                'message' => "تم ربط {count($links)} عملية تلقائياً",
                'data' => [
                    'links_count' => count($links),
                    'links' => $links,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get unlinked transactions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function unlinked(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'intermediate_account_id' => 'required|integer|exists:intermediate_accounts,id',
                'type' => 'nullable|in:receipt,payment',
            ]);

            $transactions = $this->service->getUnlinkedTransactions(
                $validated['intermediate_account_id'],
                $validated['type'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $transactions->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'type' => $transaction->type,
                        'type_label' => $transaction->getTypeLabel(),
                        'amount' => $transaction->amount,
                        'available_amount' => $transaction->getAvailableAmount(),
                        'description' => $transaction->description,
                        'reference_number' => $transaction->reference_number,
                        'transaction_date' => $transaction->transaction_date,
                        'status' => $transaction->status,
                    ];
                }),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancel a transaction.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function cancel(int $id): JsonResponse
    {
        try {
            $this->service->cancelTransaction($id);

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء العملية بنجاح',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get balance summary.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function balanceSummary(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'intermediate_account_id' => 'required|integer|exists:intermediate_accounts,id',
            ]);

            $summary = $this->service->getBalanceSummary($validated['intermediate_account_id']);

            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

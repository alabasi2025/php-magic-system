<?php

namespace App\Genes\WALLETS\Services;

use App\Genes\WALLETS\Models\WalletTransaction;
use App\Genes\WALLETS\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * Class WalletTransactionService
 * @package App\Genes\WALLETS\Services
 *
 * يوفر خدمات إدارة معاملات المحافظ (Wallet Transactions)، بما في ذلك
 * إنشاء المعاملات، التحقق من صحتها، وتطبيق منطق الأعمال المتعلق برصيد المحفظة.
 */
class WalletTransactionService
{
    /**
     * إنشاء معاملة محفظة جديدة وتحديث رصيد المحفظة.
     *
     * @param array $data بيانات المعاملة (wallet_id, type, amount, reference_id, description)
     * @return WalletTransaction
     * @throws ValidationException|Exception
     */
    public function createTransaction(array $data): WalletTransaction
    {
        $this->validateTransactionData($data, 'create');

        $wallet = Wallet::findOrFail($data['wallet_id']);

        return DB::transaction(function () use ($data, $wallet) {
            $amount = $data['amount'];
            $type = $data['type'];

            // 1. تطبيق منطق الأعمال لتحديث الرصيد
            $newBalance = $this->applyBusinessLogic($wallet, $type, $amount);

            // 2. التحقق من الرصيد بعد العملية (منع الرصيد السالب لبعض الأنواع)
            if ($newBalance < 0 && $type === 'debit') {
                throw new Exception('Insufficient funds for this transaction.');
            }

            // 3. تحديث رصيد المحفظة
            $wallet->balance = $newBalance;
            $wallet->save();

            // 4. إنشاء سجل المعاملة
            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => $type,
                'amount' => $amount,
                'current_balance' => $newBalance,
                'reference_id' => $data['reference_id'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            return $transaction;
        });
    }

    /**
     * قراءة (جلب) معاملة محفظة بواسطة المعرف.
     *
     * @param int $transactionId
     * @return WalletTransaction
     */
    public function getTransaction(int $transactionId): WalletTransaction
    {
        return WalletTransaction::findOrFail($transactionId);
    }

    /**
     * جلب جميع معاملات محفظة معينة.
     *
     * @param int $walletId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWalletTransactions(int $walletId)
    {
        return WalletTransaction::where('wallet_id', $walletId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * لا يمكن تعديل المعاملات بعد إنشائها (منطق أعمال أساسي).
     * هذه الوظيفة موجودة فقط للامتثال لنمط Service، ولكنها تثير استثناء.
     *
     * @param int $transactionId
     * @param array $data
     * @return WalletTransaction
     * @throws Exception
     */
    public function updateTransaction(int $transactionId, array $data): WalletTransaction
    {
        throw new Exception('Wallet transactions cannot be updated after creation due to financial integrity rules.');
    }

    /**
     * حذف معاملة محفظة (منطق أعمال أساسي: يجب أن يتم عبر عملية عكسية).
     * هذه الوظيفة موجودة فقط للامتثال لنمط Service، ولكنها تثير استثناء.
     *
     * @param int $transactionId
     * @return bool|null
     * @throws Exception
     */
    public function deleteTransaction(int $transactionId): ?bool
    {
        throw new Exception('Wallet transactions cannot be deleted. Use a compensating transaction (reversal) instead.');
    }

    /**
     * تطبيق منطق الأعمال لتحديد الرصيد الجديد.
     *
     * @param Wallet $wallet
     * @param string $type
     * @param float $amount
     * @return float
     */
    protected function applyBusinessLogic(Wallet $wallet, string $type, float $amount): float
    {
        $currentBalance = $wallet->balance;

        switch ($type) {
            case 'credit':
                $newBalance = $currentBalance + $amount;
                break;
            case 'debit':
                $newBalance = $currentBalance - $amount;
                break;
            default:
                // يجب أن يتم منع هذا بواسطة validateTransactionData
                $newBalance = $currentBalance;
                break;
        }

        return $newBalance;
    }

    /**
     * التحقق من صحة بيانات المعاملة.
     *
     * @param array $data
     * @param string $scenario
     * @throws ValidationException
     */
    protected function validateTransactionData(array $data, string $scenario = 'create'): void
    {
        $rules = [
            'wallet_id' => ['required', 'integer', 'exists:wallets,id'],
            'type' => ['required', 'string', 'in:credit,debit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_id' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ];

        if ($scenario === 'update') {
            // لا يوجد تحديث مسموح به حاليًا
            $rules = [];
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * جلب جميع المعاملات التي تتطابق مع معايير معينة (وظيفة بحث).
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchTransactions(array $filters)
    {
        $query = WalletTransaction::query();

        if (isset($filters['wallet_id'])) {
            $query->where('wallet_id', $filters['wallet_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['reference_id'])) {
            $query->where('reference_id', $filters['reference_id']);
        }

        if (isset($filters['min_amount'])) {
            $query->where('amount', '>=', $filters['min_amount']);
        }

        if (isset($filters['max_amount'])) {
            $query->where('amount', '<=', $filters['max_amount']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
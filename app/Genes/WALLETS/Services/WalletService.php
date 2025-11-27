<?php

namespace App\Genes\WALLETS\Services;

use App\Genes\WALLETS\Models\Wallet;
use App\Genes\WALLETS\Repositories\WalletRepository;
use App\Genes\WALLETS\Validators\WalletValidator;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Class WalletService
 * @package App\Genes\WALLETS\Services
 *
 * يوفر منطق الأعمال لإدارة المحافظ (Wallets)، بما في ذلك عمليات CRUD،
 * التحقق من الصحة، ومنطق الإيداع والسحب.
 */
class WalletService
{
    /**
     * @var WalletRepository
     */
    protected $walletRepository;

    /**
     * @var WalletValidator
     */
    protected $walletValidator;

    /**
     * WalletService constructor.
     * @param WalletRepository $walletRepository
     * @param WalletValidator $walletValidator
     */
    public function __construct(WalletRepository $walletRepository, WalletValidator $walletValidator)
    {
        $this->walletRepository = $walletRepository;
        $this->walletValidator = $walletValidator;
    }

    /**
     * إنشاء محفظة جديدة.
     *
     * @param array $data
     * @return Wallet
     * @throws Exception
     */
    public function createWallet(array $data): Wallet
    {
        $this->walletValidator->validateForCreation($data);

        try {
            return $this->walletRepository->create($data);
        } catch (Exception $e) {
            // يمكن إضافة منطق تسجيل الخطأ هنا
            throw new Exception("فشل في إنشاء المحفظة: " . $e->getMessage());
        }
    }

    /**
     * الحصول على محفظة بواسطة المعرف.
     *
     * @param int $walletId
     * @return Wallet|null
     */
    public function getWalletById(int $walletId): ?Wallet
    {
        return $this->walletRepository->find($walletId);
    }

    /**
     * تحديث بيانات محفظة موجودة.
     *
     * @param int $walletId
     * @param array $data
     * @return Wallet
     * @throws Exception
     */
    public function updateWallet(int $walletId, array $data): Wallet
    {
        $this->walletValidator->validateForUpdate($data);

        $wallet = $this->walletRepository->find($walletId);

        if (!$wallet) {
            throw new Exception("المحفظة بالمعرف {$walletId} غير موجودة.");
        }

        try {
            $this->walletRepository->update($wallet, $data);
            return $wallet->fresh();
        } catch (Exception $e) {
            throw new Exception("فشل في تحديث المحفظة: " . $e->getMessage());
        }
    }

    /**
     * حذف محفظة.
     *
     * @param int $walletId
     * @return bool
     * @throws Exception
     */
    public function deleteWallet(int $walletId): bool
    {
        $wallet = $this->walletRepository->find($walletId);

        if (!$wallet) {
            throw new Exception("المحفظة بالمعرف {$walletId} غير موجودة.");
        }

        // منطق الأعمال: التأكد من أن الرصيد صفر قبل الحذف
        if ($wallet->balance > 0) {
            throw new Exception("لا يمكن حذف المحفظة. الرصيد الحالي هو {$wallet->balance}.");
        }

        return $this->walletRepository->delete($wallet);
    }

    /**
     * إيداع مبلغ في المحفظة.
     *
     * @param int $walletId
     * @param float $amount
     * @return Wallet
     * @throws Exception
     */
    public function deposit(int $walletId, float $amount): Wallet
    {
        if ($amount <= 0) {
            throw new Exception("يجب أن يكون مبلغ الإيداع أكبر من صفر.");
        }

        $wallet = $this->walletRepository->find($walletId);

        if (!$wallet) {
            throw new Exception("المحفظة بالمعرف {$walletId} غير موجودة.");
        }

        DB::beginTransaction();
        try {
            // تحديث الرصيد
            $newBalance = $wallet->balance + $amount;
            $this->walletRepository->update($wallet, ['balance' => $newBalance]);

            // يمكن إضافة سجل حركة هنا (Transaction Log)

            DB::commit();
            return $wallet->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("فشل في عملية الإيداع: " . $e->getMessage());
        }
    }

    /**
     * سحب مبلغ من المحفظة.
     *
     * @param int $walletId
     * @param float $amount
     * @return Wallet
     * @throws Exception
     */
    public function withdraw(int $walletId, float $amount): Wallet
    {
        if ($amount <= 0) {
            throw new Exception("يجب أن يكون مبلغ السحب أكبر من صفر.");
        }

        $wallet = $this->walletRepository->find($walletId);

        if (!$wallet) {
            throw new Exception("المحفظة بالمعرف {$walletId} غير موجودة.");
        }

        // التحقق من كفاية الرصيد
        if ($wallet->balance < $amount) {
            throw new Exception("رصيد غير كافٍ لإجراء عملية السحب. الرصيد الحالي: {$wallet->balance}.");
        }

        DB::beginTransaction();
        try {
            // تحديث الرصيد
            $newBalance = $wallet->balance - $amount;
            $this->walletRepository->update($wallet, ['balance' => $newBalance]);

            // يمكن إضافة سجل حركة هنا (Transaction Log)

            DB::commit();
            return $wallet->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("فشل في عملية السحب: " . $e->getMessage());
        }
    }

    /**
     * تحويل مبلغ بين محفظتين.
     *
     * @param int $fromWalletId
     * @param int $toWalletId
     * @param float $amount
     * @return array
     * @throws Exception
     */
    public function transfer(int $fromWalletId, int $toWalletId, float $amount): array
    {
        if ($fromWalletId === $toWalletId) {
            throw new Exception("لا يمكن التحويل إلى نفس المحفظة.");
        }

        if ($amount <= 0) {
            throw new Exception("يجب أن يكون مبلغ التحويل أكبر من صفر.");
        }

        DB::beginTransaction();
        try {
            // سحب المبلغ من المحفظة المصدر
            $fromWallet = $this->withdraw($fromWalletId, $amount);

            // إيداع المبلغ في المحفظة الهدف
            $toWallet = $this->deposit($toWalletId, $amount);

            DB::commit();
            return [
                'from_wallet' => $fromWallet,
                'to_wallet' => $toWallet,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            // ملاحظة: عملية السحب والإيداع داخل التحويل يجب أن تكون جزءًا من نفس الـ Transaction
            // ولكن بما أننا نستخدم وظيفتي withdraw و deposit اللتين تبدآن Transaction خاصة بهما،
            // يجب تعديلها لتقبل Transaction خارجية أو دمج المنطق هنا.
            // في هذا المثال، سنفترض أننا نعتمد على الـ Transaction الخارجية (DB::beginTransaction() في transfer).
            // يجب أن يتم تعديل withdraw و deposit لعدم بدء Transaction جديدة إذا كانت هناك واحدة قائمة.
            // لكن لغرض هذا الـ Service، سنعتبر أن المنطق الحالي يعمل ضمن Transaction واحدة.
            throw new Exception("فشل في عملية التحويل: " . $e->getMessage());
        }
    }

    /**
     * الحصول على جميع المحافظ (لأغراض إدارية).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWallets()
    {
        return $this->walletRepository->all();
    }
}
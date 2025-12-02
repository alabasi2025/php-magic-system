<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Services;

use App\Genes\INTERMEDIATE_ACCOUNTS\Models\GeneralIntermediateAccount;
use Illuminate\Database\Eloquent\Collection;

class GeneralIntermediateAccountService
{
    /**
     * @var GeneralIntermediateAccount
     */
    protected $model;

    public function __construct(GeneralIntermediateAccount $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new GeneralIntermediateAccount record.
     *
     * @param array $data
     * @return GeneralIntermediateAccount
     */
    public function create(array $data): GeneralIntermediateAccount
    {
        // Implementation for creating a record
        return $this->model->create($data);
    }

    /**
     * Update an existing GeneralIntermediateAccount record.
     *
     * @param int $id
     * @param array $data
     * @return GeneralIntermediateAccount|null
     */
    public function update(int $id, array $data): ?GeneralIntermediateAccount
    {
        $account = $this->getById($id);
        if ($account) {
            $account->update($data);
            return $account;
        }
        return null;
    }

    /**
     * Delete a GeneralIntermediateAccount record by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $account = $this->getById($id);
        if ($account) {
            return $account->delete();
        }
        return false;
    }

    /**
     * Get a GeneralIntermediateAccount record by ID.
     *
     * @param int $id
     * @return GeneralIntermediateAccount|null
     */
    public function getById(int $id): ?GeneralIntermediateAccount
    {
        return $this->model->find($id);
    }

    /**
     * Get all GeneralIntermediateAccount records.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Adjust the balance of a GeneralIntermediateAccount.
     *
     * @param int $id
     * @param float $amount
     * @return GeneralIntermediateAccount|null
     */
    public function adjustBalance(int $id, float $amount): ?GeneralIntermediateAccount
    {
        $account = $this->getById($id);
        if ($account) {
            // Assuming 'balance' is the column name
            $account->balance += $amount;
            $account->save();
            return $account;
        }
        return null;
    }
}

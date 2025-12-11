<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ChartAccount;

class ChartAccountPolicy
{
    /**
     * Determine if the user can view any chart accounts.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('chart-accounts.view');
    }

    /**
     * Determine if the user can view the chart account.
     */
    public function view(User $user, ChartAccount $chartAccount): bool
    {
        return $user->hasPermission('chart-accounts.view');
    }

    /**
     * Determine if the user can create chart accounts.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('chart-accounts.create');
    }

    /**
     * Determine if the user can update the chart account.
     */
    public function update(User $user, ChartAccount $chartAccount): bool
    {
        // لا يمكن تعديل حساب له حركات
        if ($chartAccount->journalEntryDetails()->count() > 0) {
            return false;
        }
        
        return $user->hasPermission('chart-accounts.update');
    }

    /**
     * Determine if the user can delete the chart account.
     */
    public function delete(User $user, ChartAccount $chartAccount): bool
    {
        // لا يمكن حذف حساب له حركات أو حسابات فرعية
        if ($chartAccount->journalEntryDetails()->count() > 0 || $chartAccount->children()->count() > 0) {
            return false;
        }
        
        return $user->hasPermission('chart-accounts.delete');
    }
}

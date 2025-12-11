<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FiscalPeriod;

class FiscalPeriodPolicy
{
    /**
     * Determine if the user can view any fiscal periods.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('fiscal-periods.view');
    }

    /**
     * Determine if the user can view the fiscal period.
     */
    public function view(User $user, FiscalPeriod $fiscalPeriod): bool
    {
        return $user->hasPermission('fiscal-periods.view');
    }

    /**
     * Determine if the user can create fiscal periods.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('fiscal-periods.create');
    }

    /**
     * Determine if the user can update the fiscal period.
     */
    public function update(User $user, FiscalPeriod $fiscalPeriod): bool
    {
        // لا يمكن تعديل فترة مالية مقفلة
        if ($fiscalPeriod->is_closed) {
            return false;
        }
        
        return $user->hasPermission('fiscal-periods.update');
    }

    /**
     * Determine if the user can delete the fiscal period.
     */
    public function delete(User $user, FiscalPeriod $fiscalPeriod): bool
    {
        // لا يمكن حذف فترة مالية مقفلة أو لها قيود
        if ($fiscalPeriod->is_closed || $fiscalPeriod->journalEntries()->count() > 0) {
            return false;
        }
        
        return $user->hasPermission('fiscal-periods.delete');
    }

    /**
     * Determine if the user can close the fiscal period.
     */
    public function close(User $user, FiscalPeriod $fiscalPeriod): bool
    {
        // لا يمكن إقفال فترة مالية مقفلة
        if ($fiscalPeriod->is_closed) {
            return false;
        }
        
        return $user->hasPermission('fiscal-periods.close');
    }
}

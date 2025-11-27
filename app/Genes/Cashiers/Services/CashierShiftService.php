<?php

namespace App\Genes\Cashiers\Services;

use App\Genes\Cashiers\Models\CashierShift;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Class CashierShiftService
 *
 * This service handles the business logic for managing Cashier Shifts.
 * Task 7: Implement the logic to open a new cashier shift.
 */
class CashierShiftService
{
    /**
     * Opens a new cashier shift.
     *
     * @param User $cashier The user (cashier) opening the shift.
     * @param float $startingCash The starting cash amount for the shift.
     * @param string|null $notes Any initial notes for the shift.
     * @return CashierShift
     * @throws ValidationException
     */
    public function openShift(User $cashier, float $startingCash, ?string $notes = null): CashierShift
    {
        // 1. Check if the cashier already has an open shift
        if (CashierShift::where('cashier_id', $cashier->id)->open()->exists()) {
            throw ValidationException::withMessages([
                'cashier_id' => [__('cashiers.validation.shift_already_open')],
            ]);
        }

        // 2. Validate starting cash
        if ($startingCash < 0) {
            throw ValidationException::withMessages([
                'starting_cash' => [__('cashiers.validation.starting_cash_negative')],
            ]);
        }

        // 3. Create the new shift record within a transaction
        return DB::transaction(function () use ($cashier, $startingCash, $notes) {
            $shift = CashierShift::create([
                'cashier_id' => $cashier->id,
                'starting_cash' => $startingCash,
                'status' => 'open',
                'notes' => $notes,
                'opened_by' => auth()->id() ?? $cashier->id, // Use authenticated user or cashier if not authenticated
            ]);

            // Optional: Log the action or dispatch an event
            // event(new CashierShiftOpened($shift));

            return $shift;
        });
    }

    // Future tasks (e.g., Task 8, 9, etc.) would implement closeShift, reconcileShift, etc.
}
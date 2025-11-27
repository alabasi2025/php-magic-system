<?php

namespace App\Genes\Cashiers\Tasks;

use App\Genes\Cashiers\Cashier;

/**
 * Class Task2037
 * @package App\Genes\Cashiers\Tasks
 *
 * Task 22: Backend - Cashiers Gene - Create a method to get the cashier's daily balance.
 */
class Task2037
{
    /**
     * Get the cashier's daily balance.
     *
     * @param int $cashierId
     * @return float
     */
    public function getDailyBalance(int $cashierId): float
    {
        // This is a placeholder implementation.
        // In a real application, you would fetch this data from the database,
        // likely by querying transactions for the current day associated with the cashier.
        $cashier = Cashier::find($cashierId);

        if (!$cashier) {
            // Log an error or throw an exception if the cashier is not found
            // For now, return 0.0 as a safe default.
            return 0.0;
        }

        // TODO: Implement actual logic to calculate the daily balance from transactions.
        // Example:
        /*
        $dailyBalance = $cashier->transactions()
            ->whereDate('created_at', today())
            ->sum('amount');

        return (float) $dailyBalance;
        */

        // For demonstration purposes and to fulfill the task with a placeholder:
        return rand(1000, 5000) / 100;
    }
}

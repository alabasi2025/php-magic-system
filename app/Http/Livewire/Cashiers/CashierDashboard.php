<?php

namespace App\Http\Livewire\Cashiers;

use Livewire\Component;

/**
 * @gene Cashiers
 * @task 2044
 * @category Frontend
 * @description Livewire component for the Cashier Dashboard view.
 *
 * This component handles the main dashboard view for the cashier system,
 * displaying key metrics, recent transactions, and navigation links.
 * It adheres to the Gene Architecture for the 'Cashiers' module.
 */
class CashierDashboard extends Component
{
    /**
     * Render the component view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // TODO: Implement logic to fetch dashboard data (e.g., today's sales,
        // number of transactions, quick links) from the backend services.

        return view('livewire.cashiers.cashier-dashboard')
            ->layout('layouts.app', ['title' => __('Cashier Dashboard')]);
    }
}
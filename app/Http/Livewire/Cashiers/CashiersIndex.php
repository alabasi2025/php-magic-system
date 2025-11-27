<?php

namespace App\Http\Livewire\Cashiers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cashier; // افتراض وجود نموذج Cashier

/**
 * @gene Cashiers
 * @task 2057
 * @category Frontend
 * @description Livewire component for listing and managing cashiers.
 */
class CashiersIndex extends Component
{
    use WithPagination;

    // Search and filter properties
    public $search = '';
    public $statusFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    /**
     * Reset pagination when search or filter changes
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'statusFilter'])) {
            $this->resetPage();
        }
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // 1. Prepare the base query
        $query = Cashier::query();

        // 2. Apply search filter
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
        }

        // 3. Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // 4. Get paginated results
        $cashiers = $query->latest()->paginate(10);

        return view('livewire.cashiers.cashiers-index', [
            'cashiers' => $cashiers,
        ])->layout('layouts.app', ['title' => __('Cashiers List')]); // افتراض استخدام تخطيط رئيسي
    }

    /**
     * Example method to delete a cashier (placeholder for action)
     *
     * @param int $cashierId
     */
    public function deleteCashier($cashierId)
    {
        // Logic to delete the cashier
        // Cashier::find($cashierId)->delete();
        // session()->flash('message', 'Cashier deleted successfully.');
    }
}
// -----------------------------------------------------------------------------
// File: /home/ubuntu/all_repos/php-magic-system/resources/views/livewire/cashiers/cashiers-index.blade.php
// -----------------------------------------------------------------------------
// <div>
//     {{-- @gene Cashiers --}}
//     {{-- @task 2057 --}}
//     {{-- @category Frontend --}}
//     {{-- Livewire view for Cashiers Index --}}

//     <x-slot name="header">
//         <h2 class="font-semibold text-xl text-gray-800 leading-tight">
//             {{ __('Cashiers Management') }}
//         </h2>
//     </x-slot>

//     <div class="py-12">
//         <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
//             <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

//                 {{-- Search and Filter Section --}}
//                 <div class="flex flex-col md:flex-row justify-between mb-4 space-y-4 md:space-y-0">
//                     <div class="w-full md:w-1/3">
//                         <input wire:model.live="search" type="text" placeholder="{{ __('Search cashiers...') }}"
//                             class="form-input w-full rounded-md shadow-sm">
//                     </div>
//                     <div class="flex space-x-4">
//                         <select wire:model.live="statusFilter" class="form-select rounded-md shadow-sm">
//                             <option value="all">{{ __('All Statuses') }}</option>
//                             <option value="active">{{ __('Active') }}</option>
//                             <option value="inactive">{{ __('Inactive') }}</option>
//                         </select>
//                         <a href="{{ route('cashiers.create') }}"
//                             class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-md transition duration-150 ease-in-out">
//                             {{ __('Add New Cashier') }}
//                         </a>
//                     </div>
//                 </div>

//                 {{-- Cashiers Table --}}
//                 <div class="overflow-x-auto">
//                     <table class="min-w-full divide-y divide-gray-200">
//                         <thead>
//                             <tr>
//                                 <th
//                                     class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
//                                     {{ __('ID') }}
//                                 </th>
//                                 <th
//                                     class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
//                                     {{ __('Name') }}
//                                 </th>
//                                 <th
//                                     class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
//                                     {{ __('Email') }}
//                                 </th>
//                                 <th
//                                     class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
//                                     {{ __('Status') }}
//                                 </th>
//                                 <th class="px-6 py-3 bg-gray-50">
//                                     {{ __('Actions') }}
//                                 </th>
//                             </tr>
//                         </thead>
//                         <tbody class="bg-white divide-y divide-gray-200">
//                             @forelse ($cashiers as $cashier)
//                                 <tr>
//                                     <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
//                                         {{ $cashier->id }}
//                                     </td>
//                                     <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
//                                         {{ $cashier->name }}
//                                     </td>
//                                     <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
//                                         {{ $cashier->email }}
//                                     </td>
//                                     <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5">
//                                         <span
//                                             class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $cashier->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
//                                             {{ ucfirst($cashier->status) }}
//                                         </span>
//                                     </td>
//                                     <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
//                                         <a href="{{ route('cashiers.edit', $cashier->id) }}"
//                                             class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('Edit') }}</a>
//                                         <button wire:click="deleteCashier({{ $cashier->id }})"
//                                             class="text-red-600 hover:text-red-900"
//                                             onclick="confirm('Are you sure you want to delete this cashier?') || event.stopImmediatePropagation()">
//                                             {{ __('Delete') }}
//                                         </button>
//                                     </td>
//                                 </tr>
//                             @empty
//                                 <tr>
//                                     <td colspan="5" class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500 text-center">
//                                         {{ __('No cashiers found.') }}
//                                     </td>
//                                 </tr>
//                             @endforelse
//                         </tbody>
//                     </table>
//                 </div>

//                 {{-- Pagination Links --}}
//                 <div class="mt-4">
//                     {{ $cashiers->links() }}
//                 </div>

//             </div>
//         </div>
//     </div>
// </div>
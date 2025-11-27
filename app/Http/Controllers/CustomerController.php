<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction; // Assuming a Transaction model exists for history
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

/**
 * Class CustomerController
 * Handles all operations related to Customer management, including CRUD,
 * credit limit updates, and transaction history viewing.
 *
 * @package App\Http\Controllers
 */
class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Fetch all customers with pagination.
        // Eager load transactions count for quick overview.
        $customers = Customer::withCount('transactions')
            ->orderBy('name')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'message' => 'Customers retrieved successfully.',
            'data' => $customers
        ]);
    }

    /**
     * Store a newly created customer in storage.
     *
     * @param StoreCustomerRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            // The request is already validated by StoreCustomerRequest
            $customer = Customer::create($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Customer created successfully.',
                'data' => $customer
            ], 201); // 201 Created
        } catch (\Exception $e) {
            Log::error('Customer creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create customer.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified customer.
     *
     * @param Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Customer $customer)
    {
        // Eager load the transactions relationship for the customer details view
        $customer->load('transactions');

        return response()->json([
            'status' => 'success',
            'message' => 'Customer details retrieved successfully.',
            'data' => $customer
        ]);
    }

    /**
     * Update the specified customer in storage.
     *
     * @param UpdateCustomerRequest $request
     * @param Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        try {
            // The request is already validated by UpdateCustomerRequest
            $customer->update($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Customer updated successfully.',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            Log::error('Customer update failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update customer.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified customer from storage.
     *
     * @param Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Customer $customer)
    {
        try {
            // Use a database transaction to ensure atomicity, especially if there are related records
            DB::beginTransaction();

            // Check for related transactions before deleting (optional, depending on foreign key constraints)
            if ($customer->transactions()->exists()) {
                // Option 1: Prevent deletion if transactions exist
                // return response()->json([
                //     'status' => 'error',
                //     'message' => 'Cannot delete customer with existing transactions.'
                // ], 409); // 409 Conflict

                // Option 2: Soft delete the customer (recommended for historical data)
                $customer->delete();
            } else {
                // Hard delete if no transactions exist
                $customer->forceDelete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Customer deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer deletion failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete customer.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // --- Custom Functionality: Credit Limit Management ---

    /**
     * Update the credit limit for the specified customer.
     *
     * @param Request $request
     * @param Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCreditLimit(Request $request, Customer $customer)
    {
        // 1. Validate the incoming request for the new credit limit
        $validated = $request->validate([
            'credit_limit' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        try {
            // 2. Update the credit limit
            $customer->credit_limit = $validated['credit_limit'];
            $customer->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Credit limit updated successfully.',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            Log::error('Credit limit update failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update credit limit.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // --- Custom Functionality: Transaction History ---

    /**
     * Display the transaction history for the specified customer.
     *
     * @param Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function transactionHistory(Customer $customer)
    {
        // Fetch transactions for the customer, ordered by date descending
        // Assuming 'Transaction' model has a 'customer_id' foreign key
        $transactions = $customer->transactions()
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction history retrieved successfully.',
            'data' => $transactions
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Class SupplierController
 * Handles CRUD operations for Suppliers, along with payment tracking and transaction management.
 * Adheres to Laravel 12 best practices, using proper validation, relationships, and error handling.
 */
class SupplierController extends Controller
{
    /**
     * Display a listing of the suppliers.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Retrieve a paginated list of suppliers, ordered by creation date.
        $suppliers = Supplier::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->with(['transactions' => function ($query) {
                // Eager load transactions to calculate the current balance efficiently
                $query->select('supplier_id', DB::raw('SUM(amount) as total_balance'))
                      ->groupBy('supplier_id');
            }])
            ->latest()
            ->paginate(15);

        // Map the results to include the calculated balance
        $suppliers->getCollection()->transform(function ($supplier) {
            $supplier->current_balance = $supplier->transactions->first()->total_balance ?? 0;
            unset($supplier->transactions); // Remove the raw transactions collection
            return $supplier;
        });

        return response()->json([
            'message' => 'Suppliers retrieved successfully.',
            'data' => $suppliers
        ]);
    }

    /**
     * Store a newly created supplier in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // 1. Validation
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:suppliers,name',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255|unique:suppliers,email',
                'phone' => 'required|string|max:20|unique:suppliers,phone',
                'address' => 'nullable|string|max:500',
                'initial_balance' => 'nullable|numeric|min:0', // Initial balance for the supplier
            ]);

            // 2. Database Transaction for Atomicity
            $supplier = DB::transaction(function () use ($validatedData) {
                // Create the supplier
                $supplier = Supplier::create($validatedData);

                // If an initial balance is provided, create a corresponding transaction
                if (isset($validatedData['initial_balance']) && $validatedData['initial_balance'] > 0) {
                    // A positive initial balance means the supplier owes us (a debit transaction)
                    SupplierTransaction::create([
                        'supplier_id' => $supplier->id,
                        'type' => 'initial_balance',
                        'amount' => $validatedData['initial_balance'],
                        'description' => 'Initial balance entry for the supplier.',
                        'transaction_date' => now(),
                    ]);
                }

                return $supplier;
            });

            return response()->json([
                'message' => 'Supplier created successfully.',
                'data' => $supplier
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating supplier: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while creating the supplier.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified supplier.
     *
     * @param Supplier $supplier
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Supplier $supplier)
    {
        // Load transactions to calculate the current balance
        $balance = $supplier->transactions()->sum('amount');

        return response()->json([
            'message' => 'Supplier details retrieved successfully.',
            'data' => array_merge($supplier->toArray(), [
                'current_balance' => $balance
            ])
        ]);
    }

    /**
     * Update the specified supplier in storage.
     *
     * @param Request $request
     * @param Supplier $supplier
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Supplier $supplier)
    {
        try {
            // 1. Validation
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255|unique:suppliers,email,' . $supplier->id,
                'phone' => 'required|string|max:20|unique:suppliers,phone,' . $supplier->id,
                'address' => 'nullable|string|max:500',
            ]);

            // 2. Update
            $supplier->update($validatedData);

            return response()->json([
                'message' => 'Supplier updated successfully.',
                'data' => $supplier
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating supplier: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while updating the supplier.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified supplier from storage.
     *
     * @param Supplier $supplier
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Supplier $supplier)
    {
        // Use a database transaction to ensure related records are handled before deletion
        DB::transaction(function () use ($supplier) {
            // NOTE: It is highly recommended to implement soft deletes on the Supplier model
            // and to restrict deletion if there are related transactions or payments.
            // For this example, we assume cascading deletes are set up in the database
            // or that the related models (SupplierPayment, SupplierTransaction) use foreign key constraints
            // with 'onDelete' => 'cascade' or 'set null'.
            // A safer approach is to check for related records and throw an exception.

            if ($supplier->transactions()->exists() || $supplier->payments()->exists()) {
                // In a production environment, we would typically prevent hard deletion
                // or use soft deletes.
                // For this example, we'll prevent hard deletion if related records exist.
                throw new \Exception('Cannot delete supplier with existing transactions or payments. Consider soft deleting.');
            }

            $supplier->delete();
        });

        return response()->json([
            'message' => 'Supplier deleted successfully.'
        ], 204);
    }

    // --- Payment Tracking and Transaction Management Methods ---

    /**
     * Record a payment made to the supplier.
     * This creates a SupplierPayment record and a corresponding SupplierTransaction.
     *
     * @param Request $request
     * @param Supplier $supplier
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordPayment(Request $request, Supplier $supplier)
    {
        try {
            // 1. Validation
            $validatedData = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|string|max:50',
                'reference' => 'nullable|string|max:255',
                'payment_date' => 'nullable|date',
            ]);

            // 2. Database Transaction for Atomicity
            DB::transaction(function () use ($supplier, $validatedData) {
                // Create the payment record
                SupplierPayment::create([
                    'supplier_id' => $supplier->id,
                    'amount' => $validatedData['amount'],
                    'payment_method' => $validatedData['payment_method'],
                    'reference' => $validatedData['reference'] ?? null,
                    'payment_date' => $validatedData['payment_date'] ?? now(),
                ]);

                // Create a corresponding transaction entry.
                // A payment *to* the supplier reduces their credit/our debt, so it's a negative transaction (credit).
                SupplierTransaction::create([
                    'supplier_id' => $supplier->id,
                    'type' => 'payment_out', // Payment made to the supplier
                    'amount' => -$validatedData['amount'], // Negative amount to reduce the balance
                    'description' => 'Payment made to supplier. Ref: ' . ($validatedData['reference'] ?? 'N/A'),
                    'transaction_date' => $validatedData['payment_date'] ?? now(),
                ]);
            });

            return response()->json([
                'message' => 'Payment recorded and transaction updated successfully.',
                'data' => $supplier->refresh()
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error recording payment: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while recording the payment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the transaction history for a specific supplier.
     *
     * @param Supplier $supplier
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transactions(Supplier $supplier, Request $request)
    {
        // Retrieve paginated transactions for the supplier
        $transactions = $supplier->transactions()
            ->when($request->has('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->latest()
            ->paginate(20);

        // Calculate the running balance for the transactions (optional but useful)
        // This is complex to do efficiently in a single query with pagination,
        // so we'll just return the transactions and the current total balance.
        $currentBalance = $supplier->transactions()->sum('amount');

        return response()->json([
            'message' => 'Supplier transactions retrieved successfully.',
            'current_balance' => $currentBalance,
            'data' => $transactions
        ]);
    }

    /**
     * Manually adjust the supplier's balance (e.g., for error correction or credit note).
     *
     * @param Request $request
     * @param Supplier $supplier
     * @return \Illuminate\Http\JsonResponse
     */
    public function adjustBalance(Request $request, Supplier $supplier)
    {
        try {
            // 1. Validation
            $validatedData = $request->validate([
                'amount' => 'required|numeric|min:0.01', // The absolute amount of the adjustment
                'adjustment_type' => 'required|in:debit,credit', // debit (supplier owes us) or credit (we owe supplier)
                'description' => 'required|string|max:500',
                'transaction_date' => 'nullable|date',
            ]);

            // 2. Determine the sign of the transaction amount
            $transactionAmount = $validatedData['amount'];
            $type = 'balance_adjustment';

            if ($validatedData['adjustment_type'] === 'credit') {
                // Credit: We owe the supplier more, so the balance increases (positive transaction)
                // This is an increase in the supplier's credit/our debt.
                $transactionAmount = abs($transactionAmount);
            } else {
                // Debit: Supplier owes us more, so the balance decreases (negative transaction)
                // This is a decrease in the supplier's credit/our debt.
                $transactionAmount = -abs($transactionAmount);
            }

            // 3. Create the transaction
            $transaction = SupplierTransaction::create([
                'supplier_id' => $supplier->id,
                'type' => $type,
                'amount' => $transactionAmount,
                'description' => 'Manual balance adjustment (' . $validatedData['adjustment_type'] . '): ' . $validatedData['description'],
                'transaction_date' => $validatedData['transaction_date'] ?? now(),
            ]);

            return response()->json([
                'message' => 'Supplier balance adjusted successfully.',
                'transaction' => $transaction,
                'current_balance' => $supplier->transactions()->sum('amount')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error adjusting supplier balance: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while adjusting the balance.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
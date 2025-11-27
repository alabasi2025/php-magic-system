<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for managing Journal Entries.
 * Implements CRUD operations, a 'post' method, and balance validation.
 */
class JournalEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Retrieve all journal entries, paginated, with related items and accounts
        $entries = JournalEntry::with(['items.account'])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'message' => 'Journal entries retrieved successfully.',
            'data' => $entries,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validation
        $validatedData = $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'items' => 'required|array|min:2', // A journal entry must have at least two items (debit and credit)
            'items.*.account_id' => 'required|exists:accounts,id',
            'items.*.type' => 'required|in:debit,credit',
            'items.*.amount' => 'required|numeric|min:0.01',
        ]);

        // 2. Balance Validation (Debit must equal Credit)
        $totalDebit = collect($validatedData['items'])
            ->where('type', 'debit')
            ->sum('amount');

        $totalCredit = collect($validatedData['items'])
            ->where('type', 'credit')
            ->sum('amount');

        if (abs($totalDebit - $totalCredit) > 0.001) { // Use a small tolerance for float comparison
            throw ValidationException::withMessages([
                'balance' => ['The total debit ('. $totalDebit .') must equal the total credit ('. $totalCredit .').'],
            ]);
        }

        // 3. Transactional Creation
        try {
            DB::beginTransaction();

            $entry = JournalEntry::create([
                'date' => $validatedData['date'],
                'description' => $validatedData['description'],
                'status' => 'draft', // Default status
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'user_id' => auth()->id(), // Assuming authentication is in place
            ]);

            // Attach items to the journal entry
            $entry->items()->createMany($validatedData['items']);

            DB::commit();

            return response()->json([
                'message' => 'Journal entry created successfully.',
                'data' => $entry->load('items.account'),
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create journal entry: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to create journal entry.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JournalEntry  $journalEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(JournalEntry $journalEntry)
    {
        // Load related items and accounts for a complete view
        return response()->json([
            'message' => 'Journal entry retrieved successfully.',
            'data' => $journalEntry->load('items.account'),
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JournalEntry  $journalEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, JournalEntry $journalEntry)
    {
        // Only allow updates if the entry is in 'draft' status
        if ($journalEntry->status !== 'draft') {
            return response()->json([
                'message' => 'Cannot update a journal entry that is not in draft status.',
            ], Response::HTTP_FORBIDDEN);
        }

        // 1. Validation
        $validatedData = $request->validate([
            'date' => 'sometimes|required|date',
            'description' => 'nullable|string|max:255',
            'items' => 'sometimes|required|array|min:2',
            'items.*.id' => 'nullable|exists:journal_entry_items,id', // Optional ID for existing items
            'items.*.account_id' => 'required|exists:accounts,id',
            'items.*.type' => 'required|in:debit,credit',
            'items.*.amount' => 'required|numeric|min:0.01',
        ]);

        // 2. Balance Validation (if items are being updated)
        if (isset($validatedData['items'])) {
            $totalDebit = collect($validatedData['items'])
                ->where('type', 'debit')
                ->sum('amount');

            $totalCredit = collect($validatedData['items'])
                ->where('type', 'credit')
                ->sum('amount');

            if (abs($totalDebit - $totalCredit) > 0.001) {
                throw ValidationException::withMessages([
                    'balance' => ['The total debit ('. $totalDebit .') must equal the total credit ('. $totalCredit .').'],
                ]);
            }

            $validatedData['total_debit'] = $totalDebit;
            $validatedData['total_credit'] = $totalCredit;
        }

        // 3. Transactional Update
        try {
            DB::beginTransaction();

            $journalEntry->update($validatedData);

            if (isset($validatedData['items'])) {
                // Simple approach: delete all existing items and re-create them
                // A more complex approach would be to sync/diff, but this is safer for financial data
                $journalEntry->items()->delete();
                $journalEntry->items()->createMany($validatedData['items']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Journal entry updated successfully.',
                'data' => $journalEntry->load('items.account'),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update journal entry: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update journal entry.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JournalEntry  $journalEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(JournalEntry $journalEntry)
    {
        // Only allow deletion if the entry is in 'draft' status
        if ($journalEntry->status !== 'draft') {
            return response()->json([
                'message' => 'Cannot delete a journal entry that is not in draft status.',
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            DB::beginTransaction();

            // Delete related items first (assuming cascade delete is not set up or as a safeguard)
            $journalEntry->items()->delete();
            $journalEntry->delete();

            DB::commit();

            return response()->json([
                'message' => 'Journal entry deleted successfully.',
            ], Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete journal entry: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete journal entry.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Change the status of the specified journal entry to 'posted'.
     * This action typically triggers the actual posting to the general ledger accounts.
     *
     * @param  \App\Models\JournalEntry  $journalEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function post(JournalEntry $journalEntry)
    {
        // 1. Check current status
        if ($journalEntry->status === 'posted') {
            return response()->json([
                'message' => 'Journal entry is already posted.',
                'data' => $journalEntry,
            ], Response::HTTP_CONFLICT);
        }

        // 2. Re-validate balance before posting (a final safeguard)
        if (abs($journalEntry->total_debit - $journalEntry->total_credit) > 0.001) {
            return response()->json([
                'message' => 'Cannot post journal entry. Debit and credit totals do not match.',
                'errors' => [
                    'balance' => ['The total debit ('. $journalEntry->total_debit .') must equal the total credit ('. $journalEntry->total_credit .').'],
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 3. Transactional Posting
        try {
            DB::beginTransaction();

            // Update the status
            $journalEntry->status = 'posted';
            $journalEntry->posted_at = now(); // Assuming a 'posted_at' column exists
            $journalEntry->posted_by = auth()->id(); // Assuming a 'posted_by' column exists
            $journalEntry->save();

            // NOTE: In a real-world scenario, the posting logic (e.g., updating account balances)
            // would be implemented here or triggered via an event/job.
            // For this task, we only focus on the controller logic and status change.

            DB::commit();

            return response()->json([
                'message' => 'Journal entry posted successfully.',
                'data' => $journalEntry->load('items.account'),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to post journal entry: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to post journal entry.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
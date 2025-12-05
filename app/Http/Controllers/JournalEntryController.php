<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\ChartAccount;
use App\Http\Requests\StoreJournalEntryRequest;
use App\Http\Requests\UpdateJournalEntryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JournalEntryController extends Controller
{
    /**
     * Display a listing of journal entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = JournalEntry::with(['details.account']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('entry_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'entry_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $entries = $query->paginate($perPage)->withQueryString();

        return view('journal-entries.index', compact('entries'));
    }

    /**
     * Show the form for creating a new journal entry.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get all active accounts
        $accounts = ChartAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get();

        // Generate next entry number
        $nextEntryNumber = $this->generateEntryNumber();

        return view('journal-entries.create', compact('accounts', 'nextEntryNumber'));
    }

    /**
     * Store a newly created journal entry in storage.
     *
     * @param  \App\Http\Requests\StoreJournalEntryRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreJournalEntryRequest $request)
    {
        try {
            DB::beginTransaction();

            // Calculate totals
            $totalDebit = collect($request->details)->sum('debit');
            $totalCredit = collect($request->details)->sum('credit');

            // Create journal entry
            $journalEntry = JournalEntry::create([
                'entry_number' => $request->entry_number,
                'entry_date' => $request->entry_date,
                'description' => $request->description,
                'reference' => $request->reference,
                'status' => $request->status ?? 'draft',
                'notes' => $request->notes,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'created_by' => auth()->id(),
            ]);

            // Create journal entry details
            foreach ($request->details as $detail) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $detail['account_id'],
                    'debit' => $detail['debit'] ?? 0,
                    'credit' => $detail['credit'] ?? 0,
                    'description' => $detail['description'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('journal-entries.show', $journalEntry)
                ->with('success', 'تم إنشاء القيد اليومي بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating journal entry: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء القيد اليومي: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified journal entry.
     *
     * @param  \App\Models\JournalEntry  $journalEntry
     * @return \Illuminate\View\View
     */
    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['details.account', 'creator', 'approver']);

        return view('journal-entries.show', compact('journalEntry'));
    }

    /**
     * Show the form for editing the specified journal entry.
     *
     * @param  \App\Models\JournalEntry  $journalEntry
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(JournalEntry $journalEntry)
    {
        // Check if entry can be edited
        if (in_array($journalEntry->status, ['posted', 'approved'])) {
            return redirect()
                ->route('journal-entries.show', $journalEntry)
                ->with('error', 'لا يمكن تعديل القيد بعد اعتماده أو ترحيله.');
        }

        $journalEntry->load('details.account');
        
        $accounts = ChartAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get();

        return view('journal-entries.edit', compact('journalEntry', 'accounts'));
    }

    /**
     * Update the specified journal entry in storage.
     *
     * @param  \App\Http\Requests\UpdateJournalEntryRequest  $request
     * @param  \App\Models\JournalEntry  $journalEntry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateJournalEntryRequest $request, JournalEntry $journalEntry)
    {
        try {
            DB::beginTransaction();

            // Check if entry can be updated
            if (in_array($journalEntry->status, ['posted', 'approved'])) {
                return redirect()
                    ->back()
                    ->with('error', 'لا يمكن تعديل القيد بعد اعتماده أو ترحيله.');
            }

            // Calculate totals
            $totalDebit = collect($request->details)->sum('debit');
            $totalCredit = collect($request->details)->sum('credit');

            // Update journal entry
            $journalEntry->update([
                'entry_number' => $request->entry_number,
                'entry_date' => $request->entry_date,
                'description' => $request->description,
                'reference' => $request->reference,
                'status' => $request->status,
                'notes' => $request->notes,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'updated_by' => auth()->id(),
            ]);

            // Delete existing details
            $journalEntry->details()->delete();

            // Create new details
            foreach ($request->details as $detail) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $detail['account_id'],
                    'debit' => $detail['debit'] ?? 0,
                    'credit' => $detail['credit'] ?? 0,
                    'description' => $detail['description'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('journal-entries.show', $journalEntry)
                ->with('success', 'تم تحديث القيد اليومي بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating journal entry: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث القيد اليومي: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified journal entry from storage (soft delete).
     *
     * @param  \App\Models\JournalEntry  $journalEntry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(JournalEntry $journalEntry)
    {
        try {
            // Check if entry can be deleted
            if ($journalEntry->status === 'posted') {
                return redirect()
                    ->back()
                    ->with('error', 'لا يمكن حذف القيد بعد ترحيله.');
            }

            DB::beginTransaction();

            // Log the deletion
            Log::info('Journal Entry deleted', [
                'entry_id' => $journalEntry->id,
                'entry_number' => $journalEntry->entry_number,
                'deleted_by' => auth()->id(),
                'deleted_at' => now(),
            ]);

            // Soft delete the entry (details will be cascade deleted)
            $journalEntry->delete();

            DB::commit();

            return redirect()
                ->route('journal-entries.index')
                ->with('success', 'تم حذف القيد اليومي بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting journal entry: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء حذف القيد اليومي: ' . $e->getMessage());
        }
    }

    /**
     * Generate a unique entry number.
     *
     * @return string
     */
    private function generateEntryNumber(): string
    {
        $year = date('Y');
        $lastEntry = JournalEntry::whereYear('entry_date', $year)
            ->orderBy('entry_number', 'desc')
            ->first();

        if ($lastEntry && preg_match('/JE-' . $year . '-(\d+)/', $lastEntry->entry_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('JE-%s-%04d', $year, $nextNumber);
    }
}

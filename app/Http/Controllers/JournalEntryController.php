<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JournalEntryController extends Controller
{
    public function index()
    {
        $entries = JournalEntry::with('lines')->orderByDesc('date')->paginate(15);
        return response()->json($entries);
    }

    public function show($id)
    {
        $entry = JournalEntry::with('lines')->findOrFail($id);
        return response()->json($entry);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'lines' => 'required|array|min:1',
            'lines.*.account_id' => 'required|integer|exists:accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
            'lines.*.description' => 'nullable|string|max:500',
        ]);

        $totalDebit = collect($data['lines'])->sum('debit');
        $totalCredit = collect($data['lines'])->sum('credit');

        if (bccomp($totalDebit, $totalCredit, 2) !== 0) {
            throw ValidationException::withMessages(['lines' => ['Total debit and credit must be equal.']]);
        }

        return DB::transaction(function () use ($data) {
            $lastNumber = JournalEntry::max('number');
            $number = $lastNumber ? $lastNumber + 1 : 1;

            $entry = JournalEntry::create([
                'number' => $number,
                'date' => $data['date'],
                'description' => $data['description'] ?? null,
            ]);

            foreach ($data['lines'] as $line) {
                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'description' => $line['description'] ?? null,
                ]);
            }

            return response()->json($entry->load('lines'), 201);
        });
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'lines' => 'required|array|min:1',
            'lines.*.id' => 'nullable|integer|exists:journal_entry_lines,id',
            'lines.*.account_id' => 'required|integer|exists:accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
            'lines.*.description' => 'nullable|string|max:500',
        ]);

        $totalDebit = collect($data['lines'])->sum('debit');
        $totalCredit = collect($data['lines'])->sum('credit');

        if (bccomp($totalDebit, $totalCredit, 2) !== 0) {
            throw ValidationException::withMessages(['lines' => ['Total debit and credit must be equal.']]);
        }

        return DB::transaction(function () use ($data, $id) {
            $entry = JournalEntry::findOrFail($id);
            $entry->update([
                'date' => $data['date'],
                'description' => $data['description'] ?? null,
            ]);

            $existingLineIds = $entry->lines()->pluck('id')->toArray();
            $submittedLineIds = collect($data['lines'])->pluck('id')->filter()->toArray();

            $toDelete = array_diff($existingLineIds, $submittedLineIds);
            if (!empty($toDelete)) {
                JournalEntryLine::whereIn('id', $toDelete)->delete();
            }

            foreach ($data['lines'] as $line) {
                if (!empty($line['id'])) {
                    $entryLine = JournalEntryLine::findOrFail($line['id']);
                    $entryLine->update([
                        'account_id' => $line['account_id'],
                        'debit' => $line['debit'],
                        'credit' => $line['credit'],
                        'description' => $line['description'] ?? null,
                    ]);
                } else {
                    $entry->lines()->create([
                        'account_id' => $line['account_id'],
                        'debit' => $line['debit'],
                        'credit' => $line['credit'],
                        'description' => $line['description'] ?? null,
                    ]);
                }
            }

            return response()->json($entry->load('lines'));
        });
    }

    public function destroy($id)
    {
        $entry = JournalEntry::findOrFail($id);
        $entry->lines()->delete();
        $entry->delete();

        return response()->json(null, 204);
    }
}
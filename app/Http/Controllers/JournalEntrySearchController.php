<?php

namespace App\Http\Controllers;

use App\Models\SavedSearch;
use App\Services\JournalEntrySearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JournalEntrySearchController extends Controller
{
    protected $searchService;

    /**
     * تهيئة وحدة التحكم وحقن خدمة البحث.
     *
     * @param JournalEntrySearchService $searchService
     */
    public function __construct(JournalEntrySearchService $searchService)
    {
        $this->searchService = $searchService;
        // تطبيق حماية المسارات (middleware)
        $this->middleware('auth');
    }

    /**
     * عرض نموذج البحث وتنفيذ البحث.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        // الحصول على معايير البحث من الطلب
        $criteria = $request->all();

        // تنفيذ البحث
        $journalEntries = $this->searchService->search($criteria);

        // جلب عمليات البحث المحفوظة للمستخدم
        $savedSearches = $this->searchService->getSavedSearches();

        // نفترض وجود بيانات للحسابات والمستخدمين
        $accounts = []; // يجب جلبها من قاعدة البيانات
        $users = [];    // يجب جلبها من قاعدة البيانات
        $statuses = ['pending', 'approved', 'rejected'];

        return view('journal_entries.search', [
            'journalEntries' => $journalEntries,
            'criteria' => $criteria,
            'savedSearches' => $savedSearches,
            'accounts' => $accounts,
            'users' => $users,
            'statuses' => $statuses,
        ]);
    }

    /**
     * حفظ معايير البحث الحالية.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_name' => 'required|string|max:255',
            'criteria' => 'required|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $this->searchService->saveSearch(
                $request->input('search_name'),
                $request->input('criteria')
            );

            return back()->with('success', 'تم حفظ البحث بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل حفظ البحث: ' . $e->getMessage());
        }
    }

    /**
     * تطبيق بحث محفوظ.
     *
     * @param int $id معرف البحث المحفوظ.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function applySavedSearch(int $id)
    {
        $savedSearch = SavedSearch::where('user_id', Auth::id())->findOrFail($id);

        // إعادة توجيه إلى صفحة البحث مع تمرير المعايير كمدخلات للطلب
        return redirect()->route('journal_entries.search', $savedSearch->criteria);
    }

    /**
     * تصدير نتائج البحث الحالية إلى ملف CSV.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request): StreamedResponse
    {
        $criteria = $request->all();
        $query = $this->searchService->buildQuery($criteria);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="journal_entries_export_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function() use ($query) {
            $file = fopen('php://output', 'w');
            // كتابة رؤوس الأعمدة
            fputcsv($file, ['ID', 'Entry Number', 'Date', 'Status', 'Total Debit', 'Total Credit', 'Created By']);

            // جلب البيانات على دفعات لتجنب استهلاك الذاكرة
            $query->chunk(1000, function ($entries) use ($file) {
                foreach ($entries as $entry) {
                    // نفترض أن JournalEntry لديه حقول TotalDebit و TotalCredit
                    fputcsv($file, [
                        $entry->id,
                        $entry->entry_number,
                        $entry->entry_date,
                        $entry->status,
                        $entry->total_debit ?? 0,
                        $entry->total_credit ?? 0,
                        $entry->user->name ?? 'N/A', // نفترض علاقة مع المستخدم
                    ]);
                }
            });

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

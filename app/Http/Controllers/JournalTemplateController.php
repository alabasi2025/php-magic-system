<?php

namespace App\Http\Controllers;

use App\Models\JournalTemplate;
use App\Models\ChartAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * متحكم قوالب القيود اليومية
 * يدير عمليات CRUD للقوالب الجاهزة
 */
class JournalTemplateController extends Controller
{
    /**
     * عرض قائمة جميع القوالب
     */
    public function index()
    {
        $templates = JournalTemplate::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('journal-templates.index', compact('templates'));
    }

    /**
     * عرض نموذج إنشاء قالب جديد
     */
    public function create()
    {
        $accounts = ChartAccount::orderBy('code')->get();
        return view('journal-templates.create', compact('accounts'));
    }

    /**
     * حفظ قالب جديد في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'template_data' => 'required|json',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['template_data'] = json_decode($validated['template_data'], true);

        JournalTemplate::create($validated);

        return redirect()->route('journal-templates.index')
            ->with('success', 'تم إنشاء القالب بنجاح!');
    }

    /**
     * عرض تفاصيل قالب محدد
     */
    public function show(JournalTemplate $journalTemplate)
    {
        $journalTemplate->load('creator');
        return view('journal-templates.show', compact('journalTemplate'));
    }

    /**
     * عرض نموذج تعديل قالب
     */
    public function edit(JournalTemplate $journalTemplate)
    {
        $accounts = ChartAccount::orderBy('code')->get();
        return view('journal-templates.edit', compact('journalTemplate', 'accounts'));
    }

    /**
     * تحديث قالب في قاعدة البيانات
     */
    public function update(Request $request, JournalTemplate $journalTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'template_data' => 'required|json',
            'is_active' => 'boolean',
        ]);

        $validated['template_data'] = json_decode($validated['template_data'], true);

        $journalTemplate->update($validated);

        return redirect()->route('journal-templates.index')
            ->with('success', 'تم تحديث القالب بنجاح!');
    }

    /**
     * حذف قالب من قاعدة البيانات
     */
    public function destroy(JournalTemplate $journalTemplate)
    {
        $journalTemplate->delete();

        return redirect()->route('journal-templates.index')
            ->with('success', 'تم حذف القالب بنجاح!');
    }

    /**
     * استخدام قالب لإنشاء قيد جديد
     */
    public function use(JournalTemplate $journalTemplate)
    {
        $accounts = ChartAccount::orderBy('code')->get();
        $templateData = $journalTemplate->template_data;

        return view('journal-entries.create', compact('accounts', 'templateData', 'journalTemplate'));
    }

    /**
     * تفعيل/تعطيل قالب
     */
    public function toggle(Request $request, JournalTemplate $journalTemplate)
    {
        $journalTemplate->update([
            'is_active' => $request->input('is_active', false)
        ]);

        return redirect()->route('journal-templates.index')
            ->with('success', 'تم تحديث حالة القالب بنجاح!');
    }

    /**
     * نسخ قالب
     */
    public function duplicate(JournalTemplate $journalTemplate)
    {
        $newTemplate = $journalTemplate->replicate();
        $newTemplate->name = $journalTemplate->name . ' (نسخة)';
        $newTemplate->created_by = Auth::id();
        $newTemplate->save();

        return redirect()->route('journal-templates.index')
            ->with('success', 'تم نسخ القالب بنجاح!');
    }
}

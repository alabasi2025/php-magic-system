<?php

namespace App\Http\Controllers;

use App\Models\JournalEntryTemplate;
use App\Services\JournalEntryTemplateService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

// نفترض وجود طلبات التحقق (Form Requests) التالية لضمان كود متحكم نظيف:
// use App\Http\Requests\StoreJournalEntryTemplateRequest;
// use App\Http\Requests\UpdateJournalEntryTemplateRequest;

/**
 * المتحكم الخاص بإدارة قوالب القيود اليومية الذكية.
 */
class JournalEntryTemplateController extends Controller
{
    /**
     * @var JournalEntryTemplateService
     */
    protected JournalEntryTemplateService $templateService;

    /**
     * تهيئة المتحكم وحقن الخدمة.
     *
     * @param JournalEntryTemplateService $templateService
     */
    public function __construct(JournalEntryTemplateService $templateService)
    {
        $this->templateService = $templateService;
        // يمكن إضافة middleware هنا للتحقق من الصلاحيات
    }

    /**
     * عرض قائمة بجميع قوالب القيود اليومية.
     *
     * @return View
     */
    public function index(): View
    {
        $templates = $this->templateService->getAllTemplates();

        return view('templates.index', compact('templates'));
    }

    /**
     * عرض نموذج إنشاء قالب جديد.
     *
     * @return View
     */
    public function create(): View
    {
        return view('templates.create');
    }

    /**
     * تخزين قالب جديد في قاعدة البيانات.
     *
     * @param Request $request // يجب استبدالها بـ StoreJournalEntryTemplateRequest
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // في بيئة إنتاجية، يجب استخدام StoreJournalEntryTemplateRequest للتحقق
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_data' => 'required|json', // بيانات القيد بصيغة JSON
            'is_active' => 'boolean',
        ]);

        try {
            $this->templateService->createTemplate($validatedData);
            return redirect()->route('templates.index')->with('success', 'تم إنشاء القالب بنجاح.');
        } catch (\Exception $e) {
            Log::error('Error creating template: ' . $e->getMessage());
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء القالب.');
        }
    }

    /**
     * عرض نموذج تعديل قالب موجود.
     *
     * @param JournalEntryTemplate $template
     * @return View
     */
    public function edit(JournalEntryTemplate $template): View
    {
        return view('templates.edit', compact('template'));
    }

    /**
     * تحديث قالب موجود في قاعدة البيانات.
     *
     * @param Request $request // يجب استبدالها بـ UpdateJournalEntryTemplateRequest
     * @param JournalEntryTemplate $template
     * @return RedirectResponse
     */
    public function update(Request $request, JournalEntryTemplate $template): RedirectResponse
    {
        // في بيئة إنتاجية، يجب استخدام UpdateJournalEntryTemplateRequest للتحقق
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_data' => 'required|json',
            'is_active' => 'boolean',
        ]);

        try {
            $this->templateService->updateTemplate($template, $validatedData);
            return redirect()->route('templates.index')->with('success', 'تم تحديث القالب بنجاح.');
        } catch (\Exception $e) {
            Log::error('Error updating template: ' . $e->getMessage());
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث القالب.');
        }
    }

    /**
     * حذف قالب من قاعدة البيانات.
     *
     * @param JournalEntryTemplate $template
     * @return RedirectResponse
     */
    public function destroy(JournalEntryTemplate $template): RedirectResponse
    {
        try {
            $this->templateService->deleteTemplate($template);
            return redirect()->route('templates.index')->with('success', 'تم حذف القالب بنجاح.');
        } catch (\Exception $e) {
            Log::error('Error deleting template: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف القالب.');
        }
    }

    /**
     * تطبيق القالب: جلب بيانات القيد المخزنة في القالب.
     *
     * @param JournalEntryTemplate $template
     * @return \Illuminate\Http\JsonResponse
     */
    public function apply(JournalEntryTemplate $template): \Illuminate\Http\JsonResponse
    {
        try {
            $templateData = $this->templateService->applyTemplate($template);
            return response()->json([
                'success' => true,
                'data' => $templateData,
                'message' => 'تم جلب بيانات القالب بنجاح.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error applying template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تطبيق القالب.',
            ], 500);
        }
    }

    /**
     * حفظ بيانات قيد موجود كقالب جديد.
     *
     * @param Request $request
     * @return RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function saveAsTemplate(Request $request)
    {
        // التحقق من البيانات المطلوبة لحفظ القيد كقالب
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'journal_entry_data' => 'required|array', // بيانات القيد الفعلي المراد حفظه
        ]);

        try {
            $this->templateService->saveAsTemplate(
                $validatedData['journal_entry_data'],
                $validatedData['name'],
                $validatedData['description']
            );

            // يمكن أن تكون استجابة JSON إذا تم استدعاؤها عبر AJAX
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'تم حفظ القيد كقالب بنجاح.']);
            }

            return back()->with('success', 'تم حفظ القيد كقالب بنجاح.');
        } catch (\Exception $e) {
            Log::error('Error saving as template: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء حفظ القيد كقالب.'], 500);
            }
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ القيد كقالب.');
        }
    }
}

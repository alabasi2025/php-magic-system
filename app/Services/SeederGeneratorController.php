<?php

/**
 * 🧬 Gene: SeederGeneratorController
 * 
 * Controller لإدارة توليد الـ Seeders
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Controllers
 * @package App\Http\Controllers
 */

namespace App\Http\Controllers;

use App\Models\SeederGeneration;
use App\Models\SeederTemplate;
use App\Services\SeederGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;

class SeederGeneratorController extends Controller
{
    /**
     * خدمة توليد الـ Seeders
     */
    protected SeederGeneratorService $seederService;

    /**
     * Constructor
     */
    public function __construct(SeederGeneratorService $seederService)
    {
        $this->seederService = $seederService;
    }

    /**
     * عرض الصفحة الرئيسية
     */
    public function index(Request $request): View
    {
        $query = SeederGeneration::with('creator')->orderBy('created_at', 'desc');

        // الفلترة حسب الحالة
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // الفلترة حسب طريقة الإدخال
        if ($request->has('input_method') && $request->input_method !== '') {
            $query->where('input_method', $request->input_method);
        }

        // البحث
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('table_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $seeders = $query->paginate(20);

        // الإحصائيات
        $stats = [
            'total' => SeederGeneration::count(),
            'generated' => SeederGeneration::byStatus(SeederGeneration::STATUS_GENERATED)->count(),
            'executed' => SeederGeneration::executed()->count(),
            'failed' => SeederGeneration::failed()->count(),
        ];

        return view('seeder-generator.index', compact('seeders', 'stats'));
    }

    /**
     * عرض صفحة الإنشاء
     */
    public function create(): View
    {
        $templates = SeederTemplate::active()->orderBy('category')->get();
        $categories = SeederTemplate::getCategories();
        
        return view('seeder-generator.create', compact('templates', 'categories'));
    }

    /**
     * توليد من وصف نصي
     */
    public function generateFromText(Request $request): RedirectResponse
    {
        $request->validate([
            'description' => 'required|string|min:10',
            'use_ai' => 'nullable|boolean',
        ]);

        try {
            $generation = $this->seederService->generateFromText(
                $request->description,
                'web',
                auth()->id()
            );

            return redirect()
                ->route('seeder-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Seeder بنجاح!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'فشل توليد الـ Seeder: ' . $e->getMessage());
        }
    }

    /**
     * توليد من JSON Schema
     */
    public function generateFromJson(Request $request): RedirectResponse
    {
        $request->validate([
            'schema' => 'required|json',
        ]);

        try {
            $schema = json_decode($request->schema, true);
            
            $generation = $this->seederService->generateFromJson(
                $schema,
                'json',
                auth()->id()
            );

            return redirect()
                ->route('seeder-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Seeder بنجاح!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'فشل توليد الـ Seeder: ' . $e->getMessage());
        }
    }

    /**
     * توليد من قالب
     */
    public function generateFromTemplate(Request $request): RedirectResponse
    {
        $request->validate([
            'template_id' => 'required|exists:seeder_templates,id',
            'count' => 'nullable|integer|min:1|max:10000',
        ]);

        try {
            $generation = $this->seederService->generateFromTemplate(
                $request->template_id,
                $request->count,
                'template',
                auth()->id()
            );

            return redirect()
                ->route('seeder-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Seeder بنجاح!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'فشل توليد الـ Seeder: ' . $e->getMessage());
        }
    }

    /**
     * توليد من جدول موجود
     */
    public function generateFromTable(Request $request): RedirectResponse
    {
        $request->validate([
            'table_name' => 'required|string',
            'count' => 'nullable|integer|min:1|max:10000',
        ]);

        try {
            $generation = $this->seederService->generateFromTable(
                $request->table_name,
                $request->count ?? 10,
                'reverse',
                auth()->id()
            );

            return redirect()
                ->route('seeder-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Seeder بنجاح!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'فشل توليد الـ Seeder: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل Seeder
     */
    public function show(int $id): View
    {
        $seeder = SeederGeneration::with('creator')->findOrFail($id);
        
        return view('seeder-generator.show', compact('seeder'));
    }

    /**
     * تحديث Seeder
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $seeder = SeederGeneration::findOrFail($id);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'generated_content' => 'nullable|string',
        ]);

        try {
            $seeder->update($request->only(['name', 'description', 'generated_content']));

            return back()->with('success', 'تم تحديث الـ Seeder بنجاح!');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل تحديث الـ Seeder: ' . $e->getMessage());
        }
    }

    /**
     * حفظ كملف
     */
    public function saveFile(int $id): RedirectResponse
    {
        $seeder = SeederGeneration::findOrFail($id);

        try {
            $filePath = $this->seederService->saveToFile($seeder);

            return back()->with('success', "تم حفظ الملف بنجاح في: {$filePath}");
        } catch (\Exception $e) {
            return back()->with('error', 'فشل حفظ الملف: ' . $e->getMessage());
        }
    }

    /**
     * تنفيذ الـ Seeder
     */
    public function execute(int $id): RedirectResponse
    {
        $seeder = SeederGeneration::findOrFail($id);

        try {
            $result = $this->seederService->execute($seeder);

            if ($result['success']) {
                return back()->with('success', 
                    "تم تنفيذ الـ Seeder بنجاح! تم إنشاء {$result['records_created']} سجل في {$result['execution_time']} ثانية."
                );
            } else {
                return back()->with('error', 'فشل تنفيذ الـ Seeder: ' . $result['error']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'فشل تنفيذ الـ Seeder: ' . $e->getMessage());
        }
    }

    /**
     * تحميل الملف
     */
    public function download(int $id)
    {
        $seeder = SeederGeneration::findOrFail($id);

        $fileName = $seeder->getSeederFileName();
        $content = $seeder->generated_content;

        return Response::make($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * حذف Seeder
     */
    public function destroy(int $id): RedirectResponse
    {
        $seeder = SeederGeneration::findOrFail($id);

        try {
            $seeder->delete();

            return redirect()
                ->route('seeder-generator.index')
                ->with('success', 'تم حذف الـ Seeder بنجاح!');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل حذف الـ Seeder: ' . $e->getMessage());
        }
    }

    // ========== API Endpoints ==========

    /**
     * API: الحصول على قائمة الـ Seeders
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $query = SeederGeneration::orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $seeders = $query->get();

        return response()->json([
            'success' => true,
            'data' => $seeders,
        ]);
    }

    /**
     * API: توليد Seeder جديد
     */
    public function apiGenerate(Request $request): JsonResponse
    {
        $request->validate([
            'method' => 'required|in:text,json,template,reverse',
            'data' => 'required',
        ]);

        try {
            $generation = null;

            switch ($request->method) {
                case 'text':
                    $generation = $this->seederService->generateFromText(
                        $request->data,
                        'api',
                        auth()->id()
                    );
                    break;

                case 'json':
                    $schema = is_string($request->data) 
                        ? json_decode($request->data, true) 
                        : $request->data;
                    
                    $generation = $this->seederService->generateFromJson(
                        $schema,
                        'api',
                        auth()->id()
                    );
                    break;

                case 'template':
                    $generation = $this->seederService->generateFromTemplate(
                        $request->data['template_id'],
                        $request->data['count'] ?? null,
                        'api',
                        auth()->id()
                    );
                    break;

                case 'reverse':
                    $generation = $this->seederService->generateFromTable(
                        $request->data['table_name'],
                        $request->data['count'] ?? 10,
                        'api',
                        auth()->id()
                    );
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => $generation,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * API: الحصول على تفاصيل Seeder
     */
    public function apiShow(int $id): JsonResponse
    {
        $seeder = SeederGeneration::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $seeder,
        ]);
    }

    /**
     * API: تحديث Seeder
     */
    public function apiUpdate(Request $request, int $id): JsonResponse
    {
        $seeder = SeederGeneration::findOrFail($id);

        try {
            $seeder->update($request->only(['name', 'description', 'generated_content']));

            return response()->json([
                'success' => true,
                'data' => $seeder,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * API: حذف Seeder
     */
    public function apiDestroy(int $id): JsonResponse
    {
        $seeder = SeederGeneration::findOrFail($id);

        try {
            $seeder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Seeder deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * API: تنفيذ Seeder
     */
    public function apiExecute(int $id): JsonResponse
    {
        $seeder = SeederGeneration::findOrFail($id);

        try {
            $result = $this->seederService->execute($seeder);

            return response()->json([
                'success' => $result['success'],
                'data' => $result,
            ], $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * API: الحصول على قائمة القوالب
     */
    public function apiTemplates(Request $request): JsonResponse
    {
        $query = SeederTemplate::active();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $templates = $query->get();

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }
}

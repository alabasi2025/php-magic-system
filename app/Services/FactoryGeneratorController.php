<?php

namespace App\Http\Controllers;

use App\Models\FactoryGeneration;
use App\Models\FactoryTemplate;
use App\Services\FactoryGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

/**
 * 🧬 Controller: FactoryGeneratorController
 * 
 * متحكم نظام توليد الـ Factories
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Controllers
 * @package App\Http\Controllers
 */
class FactoryGeneratorController extends Controller
{
    /**
     * خدمة توليد الـ Factories
     */
    protected FactoryGeneratorService $service;

    /**
     * Constructor
     */
    public function __construct(FactoryGeneratorService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض قائمة الـ Factories المولدة
     */
    public function index(Request $request): View
    {
        $query = FactoryGeneration::with(['creator', 'updater'])
            ->latest();

        // تصفية حسب الحالة
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // تصفية حسب طريقة الإدخال
        if ($request->has('input_method')) {
            $query->byInputMethod($request->input_method);
        }

        // البحث
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $generations = $query->paginate(15);

        return view('factory-generator.index', compact('generations'));
    }

    /**
     * عرض صفحة إنشاء Factory جديد
     */
    public function create(): View
    {
        $templates = FactoryTemplate::public()
            ->orderBy('usage_count', 'desc')
            ->get();

        return view('factory-generator.create', compact('templates'));
    }

    /**
     * توليد Factory من وصف نصي
     */
    public function generateFromText(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $generation = $this->service->generateFromText(
                $request->description,
                FactoryGeneration::INPUT_METHOD_WEB,
                auth()->id()
            );

            return redirect()
                ->route('factory-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Factory بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * توليد Factory من JSON
     */
    public function generateFromJson(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'json_schema' => 'required|json',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $schema = json_decode($request->json_schema, true);
            
            $generation = $this->service->generateFromJson(
                $schema,
                FactoryGeneration::INPUT_METHOD_JSON,
                auth()->id()
            );

            return redirect()
                ->route('factory-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Factory بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * توليد Factory من قالب
     */
    public function generateFromTemplate(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:factory_templates,id',
            'model_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $variables = [
                'model_name' => $request->model_name,
                'table_name' => $request->table_name,
            ];

            $generation = $this->service->generateFromTemplate(
                $request->template_id,
                $variables,
                FactoryGeneration::INPUT_METHOD_TEMPLATE,
                auth()->id()
            );

            return redirect()
                ->route('factory-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Factory بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * توليد Factory من Model موجود
     */
    public function generateFromModel(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'model_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $generation = $this->service->generateFromModel(
                $request->model_name,
                FactoryGeneration::INPUT_METHOD_REVERSE,
                auth()->id()
            );

            return redirect()
                ->route('factory-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Factory بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل Factory
     */
    public function show(int $id): View
    {
        $generation = FactoryGeneration::with(['creator', 'updater'])
            ->findOrFail($id);

        return view('factory-generator.show', compact('generation'));
    }

    /**
     * تحديث Factory
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $generation = FactoryGeneration::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'generated_content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $generation->update([
                'generated_content' => $request->generated_content,
                'updated_by' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('success', 'تم تحديث الـ Factory بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * حفظ Factory كملف
     */
    public function saveFile(int $id): RedirectResponse
    {
        try {
            $generation = FactoryGeneration::findOrFail($id);
            $filePath = $this->service->saveToFile($generation);

            return redirect()->back()
                ->with('success', "تم حفظ الملف بنجاح في: {$filePath}");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * تحميل Factory
     */
    public function download(int $id)
    {
        $generation = FactoryGeneration::findOrFail($id);
        $fileName = $generation->getFileName();

        return response()->streamDownload(function () use ($generation) {
            echo $generation->generated_content;
        }, $fileName, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * حذف Factory
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $generation = FactoryGeneration::findOrFail($id);
            $this->service->deleteFactory($generation);

            return redirect()
                ->route('factory-generator.index')
                ->with('success', 'تم حذف الـ Factory بنجاح!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ==================== API Methods ====================

    /**
     * API: قائمة الـ Factories
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $query = FactoryGeneration::latest();

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('input_method')) {
            $query->byInputMethod($request->input_method);
        }

        $generations = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $generations,
        ]);
    }

    /**
     * API: توليد Factory جديد
     */
    public function apiGenerate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'method' => 'required|in:text,json,template,model',
            'data' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $generation = match ($request->method) {
                'text' => $this->service->generateFromText(
                    $request->data,
                    FactoryGeneration::INPUT_METHOD_WEB,
                    $request->user()->id ?? null
                ),
                'json' => $this->service->generateFromJson(
                    is_string($request->data) ? json_decode($request->data, true) : $request->data,
                    FactoryGeneration::INPUT_METHOD_JSON,
                    $request->user()->id ?? null
                ),
                'template' => $this->service->generateFromTemplate(
                    $request->data['template_id'],
                    $request->data['variables'] ?? [],
                    FactoryGeneration::INPUT_METHOD_TEMPLATE,
                    $request->user()->id ?? null
                ),
                'model' => $this->service->generateFromModel(
                    $request->data,
                    FactoryGeneration::INPUT_METHOD_REVERSE,
                    $request->user()->id ?? null
                ),
            };

            return response()->json([
                'success' => true,
                'data' => $generation,
                'message' => 'تم توليد الـ Factory بنجاح',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: عرض تفاصيل Factory
     */
    public function apiShow(int $id): JsonResponse
    {
        try {
            $generation = FactoryGeneration::with(['creator', 'updater'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $generation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Factory not found',
            ], 404);
        }
    }

    /**
     * API: تحديث Factory
     */
    public function apiUpdate(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'generated_content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $generation = FactoryGeneration::findOrFail($id);
            $generation->update([
                'generated_content' => $request->generated_content,
                'updated_by' => $request->user()->id ?? null,
            ]);

            return response()->json([
                'success' => true,
                'data' => $generation,
                'message' => 'تم تحديث الـ Factory بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: حذف Factory
     */
    public function apiDestroy(int $id): JsonResponse
    {
        try {
            $generation = FactoryGeneration::findOrFail($id);
            $this->service->deleteFactory($generation);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الـ Factory بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: قائمة القوالب
     */
    public function apiTemplates(): JsonResponse
    {
        $templates = FactoryTemplate::public()
            ->orderBy('usage_count', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }
}

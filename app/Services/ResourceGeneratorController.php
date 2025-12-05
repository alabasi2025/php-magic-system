<?php

namespace App\Http\Controllers;

use App\Services\ResourceGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;

/**
 * ResourceGeneratorController
 *
 * وحدة تحكم لإدارة توليد API Resources.
 * Controller for managing API Resource generation.
 *
 * @package App\Http\Controllers
 * @version v3.30.0
 * @author Manus AI
 */
class ResourceGeneratorController extends Controller
{
    /**
     * @var ResourceGeneratorService خدمة توليد الـ Resources.
     */
    protected ResourceGeneratorService $service;

    /**
     * ResourceGeneratorController constructor.
     *
     * @param ResourceGeneratorService $service
     */
    public function __construct(ResourceGeneratorService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of generated resources.
     * عرض قائمة الـ Resources المولدة
     *
     * @return View
     */
    public function index(): View
    {
        $generations = $this->service->getAllGenerations();
        $statistics = \App\Models\ResourceGeneration::getStatistics();

        return view('resource-generator.index', compact('generations', 'statistics'));
    }

    /**
     * Show the form for creating a new resource.
     * عرض نموذج إنشاء Resource جديد
     *
     * @return View
     */
    public function create(): View
    {
        // الحصول على قائمة Models المتاحة
        $models = $this->getAvailableModels();

        return view('resource-generator.create', compact('models'));
    }

    /**
     * Store a newly generated resource.
     * حفظ Resource جديد مولد
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:single,collection,nested',
            'model' => 'nullable|string|max:255',
            'attributes' => 'nullable|array',
            'relations' => 'nullable|array',
            'use_ai' => 'nullable|boolean',
        ]);

        try {
            $generation = $this->service->generateResource(
                $validated['name'],
                $validated['type'],
                [
                    'model' => $validated['model'] ?? null,
                    'attributes' => $validated['attributes'] ?? [],
                    'relations' => $validated['relations'] ?? [],
                    'use_ai' => $validated['use_ai'] ?? false,
                ]
            );

            return redirect()
                ->route('resource-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Resource بنجاح! Resource generated successfully!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'فشل في توليد الـ Resource: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource generation.
     * عرض توليد Resource محدد
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $generation = $this->service->getGeneration($id);

        return view('resource-generator.show', compact('generation'));
    }

    /**
     * Remove the specified resource generation.
     * حذف توليد Resource محدد
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->service->deleteGeneration($id);

            return redirect()
                ->route('resource-generator.index')
                ->with('success', 'تم حذف الـ Resource بنجاح! Resource deleted successfully!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'فشل في حذف الـ Resource: ' . $e->getMessage());
        }
    }

    /**
     * Preview resource generation without saving.
     * معاينة توليد Resource بدون حفظ
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:single,collection,nested',
            'model' => 'nullable|string|max:255',
            'attributes' => 'nullable|array',
            'relations' => 'nullable|array',
            'use_ai' => 'nullable|boolean',
        ]);

        try {
            // توليد مؤقت للمعاينة
            $tempGeneration = $this->service->generateResource(
                $validated['name'],
                $validated['type'],
                array_merge($validated, ['preview' => true])
            );

            return response()->json([
                'success' => true,
                'content' => $tempGeneration->content,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get available models.
     * الحصول على Models المتاحة
     *
     * @return array<int, string>
     */
    protected function getAvailableModels(): array
    {
        $modelPath = app_path('Models');
        $models = [];

        if (is_dir($modelPath)) {
            $files = scandir($modelPath);
            
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $modelName = pathinfo($file, PATHINFO_FILENAME);
                    $models[] = $modelName;
                }
            }
        }

        return $models;
    }

    /**
     * Get model attributes via AJAX.
     * الحصول على خصائص Model عبر AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModelAttributes(Request $request)
    {
        $model = $request->input('model');

        if (!$model) {
            return response()->json(['error' => 'Model name is required'], 400);
        }

        try {
            $modelClass = "App\\Models\\{$model}";
            
            if (!class_exists($modelClass)) {
                return response()->json(['error' => 'Model not found'], 404);
            }

            $instance = new $modelClass();
            $table = $instance->getTable();
            
            $attributes = \Illuminate\Support\Facades\Schema::getColumnListing($table);
            
            // استبعاد الحقول الحساسة
            $excludedFields = ['password', 'remember_token', 'api_token', 'secret'];
            $attributes = array_diff($attributes, $excludedFields);

            return response()->json([
                'success' => true,
                'attributes' => array_values($attributes),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}

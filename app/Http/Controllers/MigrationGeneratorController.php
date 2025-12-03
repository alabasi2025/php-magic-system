<?php

namespace App\Http\Controllers;

use App\Models\MigrationGeneration;
use App\Models\MigrationTemplate;
use App\Services\MigrationGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * 🧬 Controller: MigrationGeneratorController
 * 
 * التحكم في عمليات توليد الـ migrations
 * 
 * @version 1.0.0
 * @since 2025-12-03
 */
class MigrationGeneratorController extends Controller
{
    protected MigrationGeneratorService $service;

    public function __construct(MigrationGeneratorService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض الصفحة الرئيسية
     */
    public function index()
    {
        $generations = $this->service->getAllGenerations();
        
        $stats = [
            'total' => $generations->count(),
            'draft' => $generations->where('status', MigrationGeneration::STATUS_DRAFT)->count(),
            'generated' => $generations->where('status', MigrationGeneration::STATUS_GENERATED)->count(),
            'tested' => $generations->where('status', MigrationGeneration::STATUS_TESTED)->count(),
            'applied' => $generations->where('status', MigrationGeneration::STATUS_APPLIED)->count(),
        ];
        
        return view('migration-generator.index', compact('generations', 'stats'));
    }

    /**
     * عرض صفحة الإنشاء
     */
    public function create()
    {
        $templates = MigrationTemplate::active()->get();
        
        return view('migration-generator.create', compact('templates'));
    }

    /**
     * توليد من نص
     */
    public function generateFromText(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $generation = $this->service->generateFromText(
                $request->description,
                'web',
                Auth::id()
            );

            return redirect()
                ->route('migration-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Migration بنجاح!');
                
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * توليد من JSON
     */
    public function generateFromJson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'json_schema' => 'required|json',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $schema = json_decode($request->json_schema, true);
            
            $generation = $this->service->generateFromJson(
                $schema,
                'json',
                Auth::id()
            );

            return redirect()
                ->route('migration-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Migration من JSON بنجاح!');
                
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * توليد من قالب
     */
    public function generateFromTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:migration_templates,id',
            'variables' => 'required|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $generation = $this->service->generateFromTemplate(
                $request->template_id,
                $request->variables,
                Auth::id()
            );

            return redirect()
                ->route('migration-generator.show', $generation->id)
                ->with('success', 'تم توليد الـ Migration من القالب بنجاح!');
                
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل migration
     */
    public function show($id)
    {
        $generation = MigrationGeneration::with(['creator', 'updater'])->findOrFail($id);
        
        return view('migration-generator.show', compact('generation'));
    }

    /**
     * تحديث محتوى الـ migration
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'generated_content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $generation = MigrationGeneration::findOrFail($id);
        
        $generation->update([
            'generated_content' => $request->generated_content,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم تحديث المحتوى بنجاح!');
    }

    /**
     * حفظ كملف
     */
    public function saveToFile($id)
    {
        try {
            $generation = MigrationGeneration::findOrFail($id);
            
            $filePath = $this->service->saveToFile($generation);
            
            return back()->with('success', "تم حفظ الملف بنجاح في: {$filePath}");
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    /**
     * تحميل الملف
     */
    public function download($id)
    {
        $generation = MigrationGeneration::findOrFail($id);
        
        $fileName = $generation->name . '.php';
        
        return response()->streamDownload(function() use ($generation) {
            echo $generation->generated_content;
        }, $fileName, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * حذف migration
     */
    public function destroy($id)
    {
        try {
            $this->service->deleteGeneration($id);
            
            return redirect()
                ->route('migration-generator.index')
                ->with('success', 'تم حذف الـ Migration بنجاح!');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    /**
     * API: توليد من JSON
     */
    public function apiGenerate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schema' => 'required|array',
            'schema.table_name' => 'required|string',
            'schema.columns' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $generation = $this->service->generateFromJson(
                $request->schema,
                'api',
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $generation->id,
                    'name' => $generation->name,
                    'table_name' => $generation->table_name,
                    'content' => $generation->generated_content,
                    'status' => $generation->status,
                ],
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: الحصول على جميع الـ migrations
     */
    public function apiIndex()
    {
        $generations = $this->service->getAllGenerations();
        
        return response()->json([
            'success' => true,
            'data' => $generations->map(function($gen) {
                return [
                    'id' => $gen->id,
                    'name' => $gen->name,
                    'table_name' => $gen->table_name,
                    'type' => $gen->migration_type,
                    'status' => $gen->status,
                    'created_at' => $gen->created_at->toIso8601String(),
                ];
            }),
        ]);
    }

    /**
     * API: الحصول على migration محدد
     */
    public function apiShow($id)
    {
        $generation = MigrationGeneration::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $generation->id,
                'name' => $generation->name,
                'description' => $generation->description,
                'table_name' => $generation->table_name,
                'type' => $generation->migration_type,
                'content' => $generation->generated_content,
                'status' => $generation->status,
                'input_data' => $generation->input_data,
                'ai_suggestions' => $generation->ai_suggestions,
                'created_at' => $generation->created_at->toIso8601String(),
            ],
        ]);
    }
}

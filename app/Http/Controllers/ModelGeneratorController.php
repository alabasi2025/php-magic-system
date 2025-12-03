<?php

namespace App\Http\Controllers;

use App\Models\ModelGeneration;
use App\Models\ModelTemplate;
use App\Services\ModelGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * 🧬 Controller: ModelGeneratorController
 * 
 * واجهة التحكم في مولد الـ Models
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Controllers
 * @package App\Http\Controllers
 */
class ModelGeneratorController extends Controller
{
    /**
     * Model Generator Service
     */
    protected ModelGeneratorService $generatorService;

    /**
     * Constructor
     */
    public function __construct(ModelGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * عرض الصفحة الرئيسية
     */
    public function index()
    {
        $generations = ModelGeneration::with(['template', 'creator'])
            ->latest()
            ->paginate(20);

        $statistics = $this->generatorService->getStatistics();

        return view('model-generator.index', compact('generations', 'statistics'));
    }

    /**
     * عرض صفحة إنشاء Model جديد
     */
    public function create()
    {
        $templates = ModelTemplate::active()->get();
        
        return view('model-generator.create', compact('templates'));
    }

    /**
     * توليد Model من وصف نصي
     */
    public function generateFromText(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $generation = $this->generatorService->generateFromText(
                $request->description,
                'text',
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'تم توليد Model بنجاح',
                'data' => [
                    'generation' => $generation,
                    'content' => $generation->generated_content,
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
     * توليد Model من JSON Schema
     */
    public function generateFromJson(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'schema' => 'required|array',
            'schema.name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $generation = $this->generatorService->generateFromJson(
                $request->schema,
                'json',
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'تم توليد Model بنجاح',
                'data' => [
                    'generation' => $generation,
                    'content' => $generation->generated_content,
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
     * توليد Model من قاعدة البيانات
     */
    public function generateFromDatabase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'table_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $generation = $this->generatorService->generateFromDatabase(
                $request->table_name,
                'database',
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'تم توليد Model بنجاح',
                'data' => [
                    'generation' => $generation,
                    'content' => $generation->generated_content,
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
     * توليد Model من Migration
     */
    public function generateFromMigration(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'migration_file' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $generation = $this->generatorService->generateFromMigration(
                $request->migration_file,
                'migration',
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'تم توليد Model بنجاح',
                'data' => [
                    'generation' => $generation,
                    'content' => $generation->generated_content,
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
     * توليد Models لجميع الجداول
     */
    public function generateAll(): JsonResponse
    {
        try {
            $results = $this->generatorService->generateAllFromDatabase(auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'تم توليد Models بنجاح',
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * توليد Model من قالب
     */
    public function generateFromTemplate(Request $request, ModelTemplate $template): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'variables' => 'required|array',
            'variables.name' => 'required|string',
            'variables.table_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $generation = $this->generatorService->generateFromTemplate(
                $template,
                $request->variables,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'تم توليد Model من القالب بنجاح',
                'data' => [
                    'generation' => $generation,
                    'content' => $generation->generated_content,
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
     * عرض تفاصيل Generation
     */
    public function show(ModelGeneration $generation)
    {
        $generation->load(['template', 'creator', 'updater']);

        return view('model-generator.show', compact('generation'));
    }

    /**
     * تحديث Generation
     */
    public function update(Request $request, ModelGeneration $generation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'generated_content' => 'sometimes|string',
            'status' => 'sometimes|in:draft,generated,validated,deployed,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $generation->update($request->only([
                'generated_content',
                'status',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث Generation بنجاح',
                'data' => $generation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * حذف Generation
     */
    public function destroy(ModelGeneration $generation): JsonResponse
    {
        try {
            $generation->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف Generation بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * التحقق من صحة Generation
     */
    public function validate(ModelGeneration $generation): JsonResponse
    {
        try {
            $results = $this->generatorService->validate($generation);

            return response()->json([
                'success' => $results['valid'],
                'message' => $results['valid'] ? 'Model صحيح' : 'Model يحتوي على أخطاء',
                'data' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * نشر Generation إلى نظام الملفات
     */
    public function deploy(ModelGeneration $generation): JsonResponse
    {
        try {
            $success = $this->generatorService->deploy($generation);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم نشر Model بنجاح',
                    'data' => [
                        'file_path' => $generation->file_path,
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل نشر Model',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على إحصائيات
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->generatorService->getStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على قائمة الجداول
     */
    public function getTables(): JsonResponse
    {
        try {
            $connection = config('database.default');
            $database = config("database.connections.{$connection}.database");

            $tables = [];
            
            if ($connection === 'mysql') {
                $tables = \DB::select('SHOW TABLES');
                $key = "Tables_in_{$database}";
                $tables = array_map(fn($table) => $table->$key, $tables);
            } elseif ($connection === 'pgsql') {
                $tables = \DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
                $tables = array_map(fn($table) => $table->tablename, $tables);
            }

            // استبعاد جداول Laravel الأساسية
            $excludeTables = ['migrations', 'password_resets', 'password_reset_tokens', 'failed_jobs', 'personal_access_tokens'];
            $tables = array_diff($tables, $excludeTables);

            return response()->json([
                'success' => true,
                'data' => array_values($tables),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على قائمة Migrations
     */
    public function getMigrations(): JsonResponse
    {
        try {
            $migrationsPath = database_path('migrations');
            $files = \File::files($migrationsPath);
            
            $migrations = array_map(function($file) {
                return [
                    'name' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'modified' => $file->getMTime(),
                ];
            }, $files);

            // ترتيب حسب التاريخ
            usort($migrations, fn($a, $b) => $b['modified'] <=> $a['modified']);

            return response()->json([
                'success' => true,
                'data' => $migrations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

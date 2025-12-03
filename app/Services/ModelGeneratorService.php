<?php

namespace App\Services;

use App\Models\ModelGeneration;
use App\Models\ModelTemplate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * 🧬 Service: ModelGeneratorService
 * 
 * خدمة توليد الـ Models بشكل ذكي ومتقدم
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Services
 * @package App\Services
 */
class ModelGeneratorService
{
    /**
     * مسار مجلد الـ Models
     */
    protected string $modelsPath;

    /**
     * Parser Service
     */
    protected ModelParserService $parser;

    /**
     * Builder Service
     */
    protected ModelBuilderService $builder;

    /**
     * Validator Service
     */
    protected ModelValidatorService $validator;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelsPath = app_path('Models');
        $this->parser = new ModelParserService();
        $this->builder = new ModelBuilderService();
        $this->validator = new ModelValidatorService();
    }

    /**
     * توليد Model من وصف نصي
     * 
     * @param string $description الوصف النصي
     * @param string $inputMethod طريقة الإدخال
     * @param int|null $userId معرف المستخدم
     * @return ModelGeneration
     */
    public function generateFromText(
        string $description,
        string $inputMethod = 'text',
        ?int $userId = null
    ): ModelGeneration {
        try {
            // تحليل الوصف النصي
            $parsed = $this->parser->parseTextDescription($description);
            
            // إنشاء سجل في قاعدة البيانات
            $generation = ModelGeneration::create([
                'name' => $parsed['name'],
                'description' => $description,
                'table_name' => $parsed['table_name'],
                'namespace' => $parsed['namespace'] ?? 'App\\Models',
                'extends' => $parsed['extends'] ?? 'Model',
                'input_method' => $inputMethod,
                'input_data' => $parsed,
                'attributes' => $parsed['attributes'] ?? [],
                'fillable' => $parsed['fillable'] ?? [],
                'hidden' => $parsed['hidden'] ?? [],
                'casts' => $parsed['casts'] ?? [],
                'relations' => $parsed['relations'] ?? [],
                'scopes' => $parsed['scopes'] ?? [],
                'traits' => $parsed['traits'] ?? [],
                'has_timestamps' => $parsed['has_timestamps'] ?? true,
                'has_soft_deletes' => $parsed['has_soft_deletes'] ?? false,
                'has_observer' => $parsed['has_observer'] ?? false,
                'has_factory' => $parsed['has_factory'] ?? false,
                'status' => ModelGeneration::STATUS_DRAFT,
                'created_by' => $userId,
            ]);

            // توليد المحتوى
            $content = $this->builder->buildModelContent($generation);
            
            // حفظ المحتوى
            $generation->update([
                'generated_content' => $content,
                'status' => ModelGeneration::STATUS_GENERATED,
            ]);

            return $generation;
        } catch (\Exception $e) {
            throw new \Exception("فشل توليد Model من الوصف النصي: " . $e->getMessage());
        }
    }

    /**
     * توليد Model من JSON Schema
     * 
     * @param array $schema مخطط JSON
     * @param string $inputMethod طريقة الإدخال
     * @param int|null $userId معرف المستخدم
     * @return ModelGeneration
     */
    public function generateFromJson(
        array $schema,
        string $inputMethod = 'json',
        ?int $userId = null
    ): ModelGeneration {
        try {
            // التحقق من صحة الـ schema
            $this->validator->validateJsonSchema($schema);
            
            // إنشاء سجل في قاعدة البيانات
            $generation = ModelGeneration::create([
                'name' => $schema['name'],
                'description' => $schema['description'] ?? null,
                'table_name' => $schema['table'] ?? Str::snake(Str::plural($schema['name'])),
                'namespace' => $schema['namespace'] ?? 'App\\Models',
                'extends' => $schema['extends'] ?? 'Model',
                'input_method' => $inputMethod,
                'input_data' => $schema,
                'attributes' => $schema['attributes'] ?? [],
                'fillable' => $schema['fillable'] ?? [],
                'hidden' => $schema['hidden'] ?? [],
                'casts' => $schema['casts'] ?? [],
                'relations' => $schema['relations'] ?? [],
                'scopes' => $schema['scopes'] ?? [],
                'traits' => $schema['traits'] ?? [],
                'interfaces' => $schema['interfaces'] ?? [],
                'accessors' => $schema['accessors'] ?? [],
                'mutators' => $schema['mutators'] ?? [],
                'has_timestamps' => $schema['timestamps'] ?? true,
                'has_soft_deletes' => $schema['soft_deletes'] ?? false,
                'has_observer' => $schema['observer'] ?? false,
                'has_factory' => $schema['factory'] ?? false,
                'has_seeder' => $schema['seeder'] ?? false,
                'has_policy' => $schema['policy'] ?? false,
                'has_resource' => $schema['resource'] ?? false,
                'use_ai' => $schema['use_ai'] ?? false,
                'ai_provider' => $schema['ai_provider'] ?? null,
                'status' => ModelGeneration::STATUS_DRAFT,
                'created_by' => $userId,
            ]);

            // توليد المحتوى
            $content = $this->builder->buildModelContent($generation);
            
            // حفظ المحتوى
            $generation->update([
                'generated_content' => $content,
                'status' => ModelGeneration::STATUS_GENERATED,
            ]);

            return $generation;
        } catch (\Exception $e) {
            throw new \Exception("فشل توليد Model من JSON Schema: " . $e->getMessage());
        }
    }

    /**
     * توليد Model من قاعدة البيانات (Reverse Engineering)
     * 
     * @param string $tableName اسم الجدول
     * @param string $inputMethod طريقة الإدخال
     * @param int|null $userId معرف المستخدم
     * @return ModelGeneration
     */
    public function generateFromDatabase(
        string $tableName,
        string $inputMethod = 'database',
        ?int $userId = null
    ): ModelGeneration {
        try {
            // التحقق من وجود الجدول
            if (!Schema::hasTable($tableName)) {
                throw new \Exception("الجدول {$tableName} غير موجود في قاعدة البيانات");
            }

            // قراءة بنية الجدول
            $tableData = $this->parser->parseTableStructure($tableName);
            
            // إنشاء سجل في قاعدة البيانات
            $generation = ModelGeneration::create([
                'name' => $tableData['model_name'],
                'description' => "Model مولد من جدول {$tableName}",
                'table_name' => $tableName,
                'namespace' => 'App\\Models',
                'extends' => 'Model',
                'input_method' => $inputMethod,
                'input_data' => $tableData,
                'attributes' => $tableData['attributes'],
                'fillable' => $tableData['fillable'],
                'hidden' => $tableData['hidden'],
                'casts' => $tableData['casts'],
                'dates' => $tableData['dates'],
                'relations' => $tableData['relations'],
                'traits' => $tableData['traits'],
                'has_timestamps' => $tableData['has_timestamps'],
                'has_soft_deletes' => $tableData['has_soft_deletes'],
                'status' => ModelGeneration::STATUS_DRAFT,
                'created_by' => $userId,
            ]);

            // توليد المحتوى
            $content = $this->builder->buildModelContent($generation);
            
            // حفظ المحتوى
            $generation->update([
                'generated_content' => $content,
                'status' => ModelGeneration::STATUS_GENERATED,
            ]);

            return $generation;
        } catch (\Exception $e) {
            throw new \Exception("فشل توليد Model من قاعدة البيانات: " . $e->getMessage());
        }
    }

    /**
     * توليد Model من ملف Migration
     * 
     * @param string $migrationFile اسم ملف الـ migration
     * @param string $inputMethod طريقة الإدخال
     * @param int|null $userId معرف المستخدم
     * @return ModelGeneration
     */
    public function generateFromMigration(
        string $migrationFile,
        string $inputMethod = 'migration',
        ?int $userId = null
    ): ModelGeneration {
        try {
            // قراءة ملف الـ migration
            $migrationData = $this->parser->parseMigrationFile($migrationFile);
            
            // إنشاء سجل في قاعدة البيانات
            $generation = ModelGeneration::create([
                'name' => $migrationData['model_name'],
                'description' => "Model مولد من migration {$migrationFile}",
                'table_name' => $migrationData['table_name'],
                'namespace' => 'App\\Models',
                'extends' => 'Model',
                'input_method' => $inputMethod,
                'input_data' => $migrationData,
                'attributes' => $migrationData['attributes'],
                'fillable' => $migrationData['fillable'],
                'casts' => $migrationData['casts'],
                'relations' => $migrationData['relations'],
                'traits' => $migrationData['traits'],
                'has_timestamps' => $migrationData['has_timestamps'],
                'has_soft_deletes' => $migrationData['has_soft_deletes'],
                'status' => ModelGeneration::STATUS_DRAFT,
                'created_by' => $userId,
            ]);

            // توليد المحتوى
            $content = $this->builder->buildModelContent($generation);
            
            // حفظ المحتوى
            $generation->update([
                'generated_content' => $content,
                'status' => ModelGeneration::STATUS_GENERATED,
            ]);

            return $generation;
        } catch (\Exception $e) {
            throw new \Exception("فشل توليد Model من Migration: " . $e->getMessage());
        }
    }

    /**
     * توليد Models لجميع الجداول في قاعدة البيانات
     * 
     * @param int|null $userId معرف المستخدم
     * @return array
     */
    public function generateAllFromDatabase(?int $userId = null): array
    {
        $results = [];
        $tables = $this->getAllTables();

        foreach ($tables as $table) {
            try {
                $generation = $this->generateFromDatabase($table, 'database', $userId);
                $results[] = [
                    'table' => $table,
                    'status' => 'success',
                    'generation' => $generation,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'table' => $table,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * الحصول على جميع الجداول في قاعدة البيانات
     * 
     * @return array
     */
    protected function getAllTables(): array
    {
        $tables = [];
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        if ($connection === 'mysql') {
            $tables = DB::select('SHOW TABLES');
            $key = "Tables_in_{$database}";
            $tables = array_map(fn($table) => $table->$key, $tables);
        } elseif ($connection === 'pgsql') {
            $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            $tables = array_map(fn($table) => $table->tablename, $tables);
        } elseif ($connection === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $tables = array_map(fn($table) => $table->name, $tables);
        }

        // استبعاد جداول Laravel الأساسية
        $excludeTables = ['migrations', 'password_resets', 'password_reset_tokens', 'failed_jobs', 'personal_access_tokens'];
        $tables = array_diff($tables, $excludeTables);

        return array_values($tables);
    }

    /**
     * التحقق من صحة Model المولد
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    public function validate(ModelGeneration $generation): array
    {
        return $this->validator->validate($generation);
    }

    /**
     * نشر Model إلى نظام الملفات
     * 
     * @param ModelGeneration $generation
     * @return bool
     */
    public function deploy(ModelGeneration $generation): bool
    {
        return $generation->deploy();
    }

    /**
     * توليد Model من قالب
     * 
     * @param ModelTemplate $template
     * @param array $variables
     * @param int|null $userId
     * @return ModelGeneration
     */
    public function generateFromTemplate(
        ModelTemplate $template,
        array $variables,
        ?int $userId = null
    ): ModelGeneration {
        try {
            // زيادة عدد الاستخدامات
            $template->incrementUsage();

            // إنشاء سجل في قاعدة البيانات
            $generation = ModelGeneration::create([
                'name' => $variables['name'],
                'description' => $variables['description'] ?? null,
                'table_name' => $variables['table_name'],
                'namespace' => $variables['namespace'] ?? 'App\\Models',
                'extends' => 'Model',
                'input_method' => 'template',
                'input_data' => $variables,
                'template_id' => $template->id,
                'traits' => $template->default_traits,
                'casts' => $template->default_casts,
                'has_timestamps' => $template->has_timestamps,
                'has_soft_deletes' => $template->has_soft_deletes,
                'has_observer' => $template->generate_observer,
                'has_factory' => $template->generate_factory,
                'has_seeder' => $template->generate_seeder,
                'has_policy' => $template->generate_policy,
                'status' => ModelGeneration::STATUS_DRAFT,
                'created_by' => $userId,
            ]);

            // توليد المحتوى من القالب
            $content = $template->getProcessedContent($variables);
            
            // حفظ المحتوى
            $generation->update([
                'generated_content' => $content,
                'status' => ModelGeneration::STATUS_GENERATED,
            ]);

            // تحديث إحصائيات القالب
            $template->incrementSuccess();

            return $generation;
        } catch (\Exception $e) {
            $template->incrementFailure();
            throw new \Exception("فشل توليد Model من القالب: " . $e->getMessage());
        }
    }

    /**
     * الحصول على إحصائيات التوليد
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total' => ModelGeneration::count(),
            'draft' => ModelGeneration::draft()->count(),
            'generated' => ModelGeneration::generated()->count(),
            'validated' => ModelGeneration::validated()->count(),
            'deployed' => ModelGeneration::deployed()->count(),
            'failed' => ModelGeneration::failed()->count(),
            'with_ai' => ModelGeneration::withAI()->count(),
            'by_input_method' => [
                'text' => ModelGeneration::inputMethod('text')->count(),
                'json' => ModelGeneration::inputMethod('json')->count(),
                'database' => ModelGeneration::inputMethod('database')->count(),
                'migration' => ModelGeneration::inputMethod('migration')->count(),
                'ai' => ModelGeneration::inputMethod('ai')->count(),
            ],
        ];
    }
}

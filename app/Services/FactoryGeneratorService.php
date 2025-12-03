<?php

/**
 * 🧬 Gene: FactoryGeneratorService
 * 
 * خدمة توليد الـ Factories بشكل ذكي
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Services
 * @package App\Services
 */

namespace App\Services;

use App\Models\FactoryGeneration;
use App\Models\FactoryTemplate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FactoryGeneratorService
{
    /**
     * مسار مجلد الـ factories
     */
    protected string $factoriesPath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->factoriesPath = database_path('factories');
    }

    /**
     * توليد factory من وصف نصي
     */
    public function generateFromText(
        string $description,
        string $inputMethod = 'web',
        ?int $userId = null
    ): FactoryGeneration {
        // تحليل الوصف النصي
        $parsed = $this->parseTextDescription($description);
        
        // إنشاء سجل في قاعدة البيانات
        $generation = FactoryGeneration::create([
            'name' => $parsed['name'],
            'description' => $description,
            'model_name' => $parsed['model_name'],
            'table_name' => $parsed['table_name'],
            'input_method' => $inputMethod,
            'input_data' => $parsed,
            'generated_content' => '',
            'use_ai' => $parsed['use_ai'] ?? false,
            'ai_provider' => $parsed['ai_provider'] ?? null,
            'status' => FactoryGeneration::STATUS_DRAFT,
            'created_by' => $userId,
        ]);

        // توليد المحتوى
        $content = $this->buildFactoryContent($parsed);
        
        // حفظ المحتوى
        $generation->update([
            'generated_content' => $content,
            'status' => FactoryGeneration::STATUS_GENERATED,
        ]);

        return $generation;
    }

    /**
     * توليد factory من JSON Schema
     */
    public function generateFromJson(
        array $schema,
        string $inputMethod = 'json',
        ?int $userId = null
    ): FactoryGeneration {
        // التحقق من صحة الـ schema
        $this->validateJsonSchema($schema);
        
        // إنشاء سجل في قاعدة البيانات
        $generation = FactoryGeneration::create([
            'name' => $schema['name'] ?? $this->generateFactoryName($schema['model_name']),
            'description' => $schema['description'] ?? null,
            'model_name' => $schema['model_name'],
            'table_name' => $schema['table_name'] ?? $this->getTableName($schema['model_name']),
            'input_method' => $inputMethod,
            'input_data' => $schema,
            'generated_content' => '',
            'use_ai' => $schema['use_ai'] ?? false,
            'ai_provider' => $schema['ai_provider'] ?? null,
            'status' => FactoryGeneration::STATUS_DRAFT,
            'created_by' => $userId,
        ]);

        // توليد المحتوى
        $content = $this->buildFactoryFromJson($schema);
        
        // حفظ المحتوى
        $generation->update([
            'generated_content' => $content,
            'status' => FactoryGeneration::STATUS_GENERATED,
        ]);

        return $generation;
    }

    /**
     * توليد factory من قالب جاهز
     */
    public function generateFromTemplate(
        int $templateId,
        array $variables = [],
        string $inputMethod = 'template',
        ?int $userId = null
    ): FactoryGeneration {
        // الحصول على القالب
        $template = FactoryTemplate::findOrFail($templateId);
        
        // زيادة عداد الاستخدام
        $template->incrementUsage();
        
        // استخدام schema القالب
        $schema = $template->schema;
        $schema['model_name'] = $variables['model_name'] ?? $template->model_name;
        $schema['table_name'] = $variables['table_name'] ?? $template->table_name;
        
        // دمج المتغيرات الإضافية
        $schema = array_merge($schema, $variables);
        
        // توليد من JSON
        return $this->generateFromJson($schema, $inputMethod, $userId);
    }

    /**
     * توليد factory من Model موجود (Reverse Engineering)
     */
    public function generateFromModel(
        string $modelName,
        string $inputMethod = 'reverse',
        ?int $userId = null
    ): FactoryGeneration {
        // الحصول على اسم الجدول من الـ Model
        $tableName = $this->getTableNameFromModel($modelName);
        
        // الحصول على بنية الجدول
        $columns = $this->getTableColumns($tableName);
        
        // بناء schema من الأعمدة
        $schema = [
            'model_name' => $modelName,
            'table_name' => $tableName,
            'fields' => $this->mapColumnsToSchema($columns),
        ];
        
        // توليد من JSON
        return $this->generateFromJson($schema, $inputMethod, $userId);
    }

    /**
     * حفظ الـ factory كملف
     */
    public function saveToFile(FactoryGeneration $generation): string
    {
        $fileName = $generation->getFileName();
        $filePath = $this->factoriesPath . '/' . $fileName;
        
        // إنشاء المجلد إذا لم يكن موجوداً
        if (!File::exists($this->factoriesPath)) {
            File::makeDirectory($this->factoriesPath, 0755, true);
        }
        
        // حفظ الملف
        File::put($filePath, $generation->generated_content);
        
        // تحديث مسار الملف
        $generation->markAsSaved($filePath);
        
        return $filePath;
    }

    /**
     * تحليل الوصف النصي
     */
    protected function parseTextDescription(string $description): array
    {
        $parsed = [
            'name' => '',
            'model_name' => '',
            'table_name' => '',
            'fields' => [],
            'use_ai' => false,
        ];

        // استخراج اسم الـ Model
        if (preg_match('/موديل\s+(\w+)/u', $description, $matches)) {
            $parsed['model_name'] = $matches[1];
        } elseif (preg_match('/model\s+(\w+)/i', $description, $matches)) {
            $parsed['model_name'] = $matches[1];
        }

        // استخراج اسم الجدول
        if (preg_match('/جدول\s+(\w+)/u', $description, $matches)) {
            $parsed['table_name'] = $matches[1];
        } elseif (preg_match('/table\s+(\w+)/i', $description, $matches)) {
            $parsed['table_name'] = $matches[1];
        }

        // توليد الأسماء
        if ($parsed['model_name']) {
            $parsed['name'] = $this->generateFactoryName($parsed['model_name']);
            if (!$parsed['table_name']) {
                $parsed['table_name'] = $this->getTableName($parsed['model_name']);
            }
        }

        // استخراج الحقول
        $parsed['fields'] = $this->extractFieldsFromText($description);

        return $parsed;
    }

    /**
     * استخراج الحقول من النص
     */
    protected function extractFieldsFromText(string $description): array
    {
        $fields = [];
        
        // أنماط شائعة
        $patterns = [
            'name' => ['اسم', 'name'],
            'email' => ['بريد', 'ايميل', 'email'],
            'phone' => ['هاتف', 'جوال', 'phone'],
            'price' => ['سعر', 'price'],
            'description' => ['وصف', 'description'],
            'image' => ['صورة', 'image'],
            'title' => ['عنوان', 'title'],
            'content' => ['محتوى', 'content'],
            'status' => ['حالة', 'status'],
            'quantity' => ['كمية', 'quantity'],
            'sku' => ['رمز', 'sku'],
        ];

        foreach ($patterns as $field => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($description, $keyword) !== false) {
                    $fields[$field] = $this->getDefaultFieldType($field);
                    break;
                }
            }
        }

        return $fields;
    }

    /**
     * الحصول على نوع الحقل الافتراضي
     */
    protected function getDefaultFieldType(string $field): array
    {
        $types = [
            'name' => ['faker' => 'name'],
            'email' => ['faker' => 'unique()->safeEmail'],
            'phone' => ['faker' => 'phoneNumber'],
            'price' => ['faker' => 'randomFloat(2, 10, 10000)'],
            'description' => ['faker' => 'text(200)'],
            'image' => ['faker' => 'imageUrl(640, 480)'],
            'title' => ['faker' => 'sentence'],
            'content' => ['faker' => 'paragraph'],
            'status' => ['faker' => 'randomElement([\'active\', \'inactive\'])'],
            'quantity' => ['faker' => 'numberBetween(1, 100)'],
            'sku' => ['faker' => 'unique()->bothify(\'???-####\')'],
        ];

        return $types[$field] ?? ['faker' => 'word'];
    }

    /**
     * بناء محتوى الـ Factory
     */
    protected function buildFactoryContent(array $parsed): string
    {
        $modelName = $parsed['model_name'];
        $date = Carbon::now()->format('Y-m-d');
        
        $content = "<?php\n\n";
        $content .= "namespace Database\\Factories;\n\n";
        $content .= "use App\\Models\\{$modelName};\n";
        $content .= "use Illuminate\\Database\\Eloquent\\Factories\\Factory;\n";
        $content .= "use Illuminate\\Support\\Str;\n\n";
        
        $content .= "/**\n";
        $content .= " * 🧬 Factory: {$modelName}Factory\n";
        $content .= " * \n";
        $content .= " * مصنع توليد بيانات وهمية لنموذج {$modelName}\n";
        $content .= " * \n";
        $content .= " * @version 1.0.0\n";
        $content .= " * @since {$date}\n";
        $content .= " * @extends \\Illuminate\\Database\\Eloquent\\Factories\\Factory<\\App\\Models\\{$modelName}>\n";
        $content .= " */\n";
        $content .= "class {$modelName}Factory extends Factory\n";
        $content .= "{\n";
        $content .= "    /**\n";
        $content .= "     * The name of the factory's corresponding model.\n";
        $content .= "     *\n";
        $content .= "     * @var string\n";
        $content .= "     */\n";
        $content .= "    protected \$model = {$modelName}::class;\n\n";
        $content .= "    /**\n";
        $content .= "     * Define the model's default state.\n";
        $content .= "     *\n";
        $content .= "     * @return array<string, mixed>\n";
        $content .= "     */\n";
        $content .= "    public function definition(): array\n";
        $content .= "    {\n";
        $content .= "        return [\n";
        
        // إضافة الحقول
        if (!empty($parsed['fields'])) {
            foreach ($parsed['fields'] as $field => $config) {
                $fakerCode = $this->getFakerCode($field, $config);
                $content .= "            '{$field}' => {$fakerCode},\n";
            }
        } else {
            $content .= "            // أضف الحقول هنا\n";
        }
        
        $content .= "        ];\n";
        $content .= "    }\n";
        
        // إضافة states إضافية
        $content .= "\n    /**\n";
        $content .= "     * Indicate that the model is active.\n";
        $content .= "     */\n";
        $content .= "    public function active(): static\n";
        $content .= "    {\n";
        $content .= "        return \$this->state(fn (array \$attributes) => [\n";
        $content .= "            'status' => 'active',\n";
        $content .= "        ]);\n";
        $content .= "    }\n";
        
        $content .= "\n    /**\n";
        $content .= "     * Indicate that the model is inactive.\n";
        $content .= "     */\n";
        $content .= "    public function inactive(): static\n";
        $content .= "    {\n";
        $content .= "        return \$this->state(fn (array \$attributes) => [\n";
        $content .= "            'status' => 'inactive',\n";
        $content .= "        ]);\n";
        $content .= "    }\n";
        
        $content .= "}\n";

        return $content;
    }

    /**
     * بناء Factory من JSON Schema
     */
    protected function buildFactoryFromJson(array $schema): string
    {
        $modelName = $schema['model_name'];
        $date = Carbon::now()->format('Y-m-d');
        
        $content = "<?php\n\n";
        $content .= "namespace Database\\Factories;\n\n";
        $content .= "use App\\Models\\{$modelName};\n";
        $content .= "use Illuminate\\Database\\Eloquent\\Factories\\Factory;\n";
        $content .= "use Illuminate\\Support\\Str;\n";
        
        // إضافة imports إضافية إذا لزم الأمر
        if (isset($schema['imports'])) {
            foreach ($schema['imports'] as $import) {
                $content .= "use {$import};\n";
            }
        }
        
        $content .= "\n/**\n";
        $content .= " * 🧬 Factory: {$modelName}Factory\n";
        $content .= " * \n";
        $content .= " * " . ($schema['description'] ?? "مصنع توليد بيانات وهمية لنموذج {$modelName}") . "\n";
        $content .= " * \n";
        $content .= " * @version 1.0.0\n";
        $content .= " * @since {$date}\n";
        $content .= " * @extends \\Illuminate\\Database\\Eloquent\\Factories\\Factory<\\App\\Models\\{$modelName}>\n";
        $content .= " */\n";
        $content .= "class {$modelName}Factory extends Factory\n";
        $content .= "{\n";
        $content .= "    /**\n";
        $content .= "     * The name of the factory's corresponding model.\n";
        $content .= "     *\n";
        $content .= "     * @var string\n";
        $content .= "     */\n";
        $content .= "    protected \$model = {$modelName}::class;\n\n";
        $content .= "    /**\n";
        $content .= "     * Define the model's default state.\n";
        $content .= "     *\n";
        $content .= "     * @return array<string, mixed>\n";
        $content .= "     */\n";
        $content .= "    public function definition(): array\n";
        $content .= "    {\n";
        $content .= "        return [\n";
        
        // إضافة الحقول من الـ schema
        if (isset($schema['fields']) && is_array($schema['fields'])) {
            foreach ($schema['fields'] as $field => $config) {
                $fakerCode = $this->getFakerCode($field, $config);
                $comment = $config['comment'] ?? '';
                $content .= "            '{$field}' => {$fakerCode}," . ($comment ? " // {$comment}" : "") . "\n";
            }
        }
        
        $content .= "        ];\n";
        $content .= "    }\n";
        
        // إضافة states من الـ schema
        if (isset($schema['states']) && is_array($schema['states'])) {
            foreach ($schema['states'] as $stateName => $stateConfig) {
                $content .= "\n    /**\n";
                $content .= "     * " . ($stateConfig['description'] ?? "State: {$stateName}") . "\n";
                $content .= "     */\n";
                $content .= "    public function {$stateName}(): static\n";
                $content .= "    {\n";
                $content .= "        return \$this->state(fn (array \$attributes) => [\n";
                
                foreach ($stateConfig['attributes'] as $attr => $value) {
                    $valueStr = is_string($value) ? "'{$value}'" : $value;
                    $content .= "            '{$attr}' => {$valueStr},\n";
                }
                
                $content .= "        ]);\n";
                $content .= "    }\n";
            }
        } else {
            // إضافة states افتراضية
            $content .= "\n    /**\n";
            $content .= "     * Indicate that the model is active.\n";
            $content .= "     */\n";
            $content .= "    public function active(): static\n";
            $content .= "    {\n";
            $content .= "        return \$this->state(fn (array \$attributes) => [\n";
            $content .= "            'status' => 'active',\n";
            $content .= "        ]);\n";
            $content .= "    }\n";
        }
        
        $content .= "}\n";

        return $content;
    }

    /**
     * الحصول على كود Faker للحقل
     */
    protected function getFakerCode(string $field, array $config): string
    {
        if (isset($config['faker'])) {
            return "fake()->" . $config['faker'];
        }
        
        // تحديد تلقائي بناءً على اسم الحقل
        $fakerMethods = [
            'name' => 'name()',
            'first_name' => 'firstName()',
            'last_name' => 'lastName()',
            'email' => 'unique()->safeEmail()',
            'phone' => 'phoneNumber()',
            'address' => 'address()',
            'city' => 'city()',
            'country' => 'country()',
            'zip' => 'postcode()',
            'title' => 'sentence()',
            'description' => 'text(200)',
            'content' => 'paragraph()',
            'price' => 'randomFloat(2, 10, 1000)',
            'quantity' => 'numberBetween(1, 100)',
            'status' => 'randomElement([\'active\', \'inactive\'])',
            'image' => 'imageUrl(640, 480)',
            'url' => 'url()',
            'slug' => 'slug()',
            'sku' => 'unique()->bothify(\'???-####\')',
            'barcode' => 'ean13()',
            'date' => 'date()',
            'datetime' => 'dateTime()',
            'time' => 'time()',
            'boolean' => 'boolean()',
            'is_active' => 'boolean()',
            'is_published' => 'boolean()',
        ];
        
        // البحث عن تطابق جزئي
        foreach ($fakerMethods as $pattern => $method) {
            if (Str::contains($field, $pattern)) {
                return "fake()->" . $method;
            }
        }
        
        return "fake()->word()";
    }

    /**
     * التحقق من صحة JSON Schema
     */
    protected function validateJsonSchema(array $schema): void
    {
        if (!isset($schema['model_name'])) {
            throw new \InvalidArgumentException('model_name is required in JSON schema');
        }
    }

    /**
     * توليد اسم الـ Factory
     */
    protected function generateFactoryName(string $modelName): string
    {
        return $modelName . 'Factory';
    }

    /**
     * الحصول على اسم الجدول من الـ Model
     */
    protected function getTableName(string $modelName): string
    {
        return Str::snake(Str::pluralStudly($modelName));
    }

    /**
     * الحصول على اسم الجدول من الـ Model الفعلي
     */
    protected function getTableNameFromModel(string $modelName): string
    {
        $modelClass = "App\\Models\\{$modelName}";
        
        if (class_exists($modelClass)) {
            $model = new $modelClass;
            return $model->getTable();
        }
        
        return $this->getTableName($modelName);
    }

    /**
     * الحصول على أعمدة الجدول
     */
    protected function getTableColumns(string $tableName): array
    {
        if (!Schema::hasTable($tableName)) {
            throw new \InvalidArgumentException("Table {$tableName} does not exist");
        }
        
        $columns = Schema::getColumnListing($tableName);
        $columnDetails = [];
        
        foreach ($columns as $column) {
            $type = Schema::getColumnType($tableName, $column);
            $columnDetails[$column] = [
                'type' => $type,
                'name' => $column,
            ];
        }
        
        return $columnDetails;
    }

    /**
     * تحويل الأعمدة إلى schema
     */
    protected function mapColumnsToSchema(array $columns): array
    {
        $fields = [];
        
        // استبعاد الأعمدة الافتراضية
        $excludedColumns = ['id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'];
        
        foreach ($columns as $column => $details) {
            if (in_array($column, $excludedColumns)) {
                continue;
            }
            
            $fields[$column] = $this->mapColumnTypeToFaker($column, $details['type']);
        }
        
        return $fields;
    }

    /**
     * تحويل نوع العمود إلى Faker
     */
    protected function mapColumnTypeToFaker(string $columnName, string $columnType): array
    {
        // محاولة التحديد بناءً على الاسم أولاً
        $defaultType = $this->getDefaultFieldType($columnName);
        if ($defaultType['faker'] !== 'word') {
            return $defaultType;
        }
        
        // التحديد بناءً على النوع
        $typeMappings = [
            'string' => ['faker' => 'word()'],
            'text' => ['faker' => 'text(200)'],
            'integer' => ['faker' => 'numberBetween(1, 1000)'],
            'bigint' => ['faker' => 'numberBetween(1, 100000)'],
            'decimal' => ['faker' => 'randomFloat(2, 0, 1000)'],
            'float' => ['faker' => 'randomFloat(2, 0, 1000)'],
            'boolean' => ['faker' => 'boolean()'],
            'date' => ['faker' => 'date()'],
            'datetime' => ['faker' => 'dateTime()'],
            'timestamp' => ['faker' => 'dateTime()'],
            'json' => ['faker' => 'json()'],
        ];
        
        return $typeMappings[$columnType] ?? ['faker' => 'word()'];
    }

    /**
     * الحصول على قائمة الـ Factories المتاحة
     */
    public function listFactories(): array
    {
        $files = File::files($this->factoriesPath);
        $factories = [];
        
        foreach ($files as $file) {
            $factories[] = [
                'name' => $file->getFilename(),
                'path' => $file->getPathname(),
                'size' => $file->getSize(),
                'modified' => $file->getMTime(),
            ];
        }
        
        return $factories;
    }

    /**
     * حذف factory
     */
    public function deleteFactory(FactoryGeneration $generation): bool
    {
        // حذف الملف إذا كان موجوداً
        if ($generation->file_path && File::exists($generation->file_path)) {
            File::delete($generation->file_path);
        }
        
        // حذف السجل
        return $generation->delete();
    }
}

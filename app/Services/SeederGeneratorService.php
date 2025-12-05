<?php

/**
 * 🧬 Gene: SeederGeneratorService
 * 
 * خدمة توليد الـ Seeders بشكل ذكي
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Services
 * @package App\Services
 */

namespace App\Services;

use App\Models\SeederGeneration;
use App\Models\SeederTemplate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SeederGeneratorService
{
    /**
     * مسار مجلد الـ seeders
     */
    protected string $seedersPath;

    /**
     * خدمة الذكاء الاصطناعي
     */
    protected SeederAIService $aiService;

    /**
     * Constructor
     */
    public function __construct(SeederAIService $aiService)
    {
        $this->seedersPath = database_path('seeders');
        $this->aiService = $aiService;
    }

    /**
     * توليد seeder من وصف نصي
     */
    public function generateFromText(
        string $description,
        string $inputMethod = 'web',
        ?int $userId = null
    ): SeederGeneration {
        // تحليل الوصف النصي
        $parsed = $this->parseTextDescription($description);
        
        // إنشاء سجل في قاعدة البيانات
        $generation = SeederGeneration::create([
            'name' => $parsed['name'],
            'description' => $description,
            'table_name' => $parsed['table_name'],
            'model_name' => $parsed['model_name'],
            'count' => $parsed['count'],
            'input_method' => $inputMethod,
            'input_data' => $parsed,
            'generated_content' => '',
            'use_ai' => $parsed['use_ai'] ?? false,
            'ai_provider' => $parsed['ai_provider'] ?? null,
            'status' => SeederGeneration::STATUS_DRAFT,
            'created_by' => $userId,
        ]);

        // توليد المحتوى
        $content = $this->buildSeederContent($parsed);
        
        // حفظ المحتوى
        $generation->update([
            'generated_content' => $content,
            'status' => SeederGeneration::STATUS_GENERATED,
        ]);

        return $generation;
    }

    /**
     * توليد seeder من JSON Schema
     */
    public function generateFromJson(
        array $schema,
        string $inputMethod = 'json',
        ?int $userId = null
    ): SeederGeneration {
        // التحقق من صحة الـ schema
        $this->validateJsonSchema($schema);
        
        // إنشاء سجل في قاعدة البيانات
        $generation = SeederGeneration::create([
            'name' => $schema['name'] ?? $this->generateSeederName($schema['table_name']),
            'description' => $schema['description'] ?? null,
            'table_name' => $schema['table_name'],
            'model_name' => $schema['model_name'] ?? $this->getModelName($schema['table_name']),
            'count' => $schema['count'] ?? 10,
            'input_method' => $inputMethod,
            'input_data' => $schema,
            'generated_content' => '',
            'use_ai' => $schema['use_ai'] ?? false,
            'ai_provider' => $schema['ai_provider'] ?? null,
            'status' => SeederGeneration::STATUS_DRAFT,
            'created_by' => $userId,
        ]);

        // توليد المحتوى
        $content = $this->buildSeederFromJson($schema);
        
        // حفظ المحتوى
        $generation->update([
            'generated_content' => $content,
            'status' => SeederGeneration::STATUS_GENERATED,
        ]);

        return $generation;
    }

    /**
     * توليد seeder من قالب جاهز
     */
    public function generateFromTemplate(
        int $templateId,
        ?int $count = null,
        string $inputMethod = 'template',
        ?int $userId = null
    ): SeederGeneration {
        // الحصول على القالب
        $template = SeederTemplate::findOrFail($templateId);
        
        // زيادة عداد الاستخدام
        $template->incrementUsage();
        
        // استخدام schema القالب
        $schema = $template->schema;
        $schema['count'] = $count ?? $template->default_count;
        $schema['table_name'] = $template->table_name;
        $schema['model_name'] = $template->model_name;
        
        // توليد من JSON
        return $this->generateFromJson($schema, $inputMethod, $userId);
    }

    /**
     * توليد seeder من جدول موجود (Reverse Engineering)
     */
    public function generateFromTable(
        string $tableName,
        int $count = 10,
        string $inputMethod = 'reverse',
        ?int $userId = null
    ): SeederGeneration {
        // الحصول على بنية الجدول
        $columns = $this->getTableColumns($tableName);
        
        // بناء schema من الأعمدة
        $schema = [
            'table_name' => $tableName,
            'model_name' => $this->getModelName($tableName),
            'count' => $count,
            'columns' => $this->mapColumnsToSchema($columns),
        ];
        
        // توليد من JSON
        return $this->generateFromJson($schema, $inputMethod, $userId);
    }

    /**
     * تحليل الوصف النصي
     */
    protected function parseTextDescription(string $description): array
    {
        $parsed = [
            'name' => '',
            'table_name' => '',
            'model_name' => '',
            'count' => 10,
            'columns' => [],
            'use_ai' => false,
        ];

        // استخراج اسم الجدول
        if (preg_match('/جدول\s+(\w+)/u', $description, $matches)) {
            $parsed['table_name'] = $matches[1];
        } elseif (preg_match('/table\s+(\w+)/i', $description, $matches)) {
            $parsed['table_name'] = $matches[1];
        }

        // استخراج العدد
        if (preg_match('/(\d+)\s+(سجل|منتج|مستخدم|طلب|عنصر)/u', $description, $matches)) {
            $parsed['count'] = (int) $matches[1];
        } elseif (preg_match('/(\d+)\s+(record|product|user|order|item)/i', $description, $matches)) {
            $parsed['count'] = (int) $matches[1];
        }

        // توليد الأسماء
        if ($parsed['table_name']) {
            $parsed['name'] = $this->generateSeederName($parsed['table_name']);
            $parsed['model_name'] = $this->getModelName($parsed['table_name']);
        }

        // استخراج الأعمدة (بسيط)
        $parsed['columns'] = $this->extractColumnsFromText($description);

        return $parsed;
    }

    /**
     * استخراج الأعمدة من النص
     */
    protected function extractColumnsFromText(string $description): array
    {
        $columns = [];
        
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
        ];

        foreach ($patterns as $column => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($description, $keyword) !== false) {
                    $columns[$column] = $this->getDefaultColumnType($column);
                    break;
                }
            }
        }

        return $columns;
    }

    /**
     * الحصول على نوع العمود الافتراضي
     */
    protected function getDefaultColumnType(string $column): array
    {
        $types = [
            'name' => ['type' => 'name'],
            'email' => ['type' => 'email', 'unique' => true],
            'phone' => ['type' => 'phone'],
            'price' => ['type' => 'price', 'min' => 10, 'max' => 10000],
            'description' => ['type' => 'text', 'sentences' => 3],
            'image' => ['type' => 'imageUrl'],
            'title' => ['type' => 'sentence'],
            'content' => ['type' => 'paragraph'],
            'status' => ['type' => 'enum', 'values' => ['active', 'inactive']],
        ];

        return $types[$column] ?? ['type' => 'text'];
    }

    /**
     * بناء محتوى الـ Seeder
     */
    protected function buildSeederContent(array $parsed): string
    {
        $className = $this->getSeederClassName($parsed['table_name']);
        $modelName = $parsed['model_name'];
        $tableName = $parsed['table_name'];
        $count = $parsed['count'];
        
        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use App\\Models\\{$modelName};\n";
        $content .= "use Faker\\Factory as Faker;\n";
        
        // إضافة Hash إذا كان هناك password
        if (isset($parsed['columns']['password'])) {
            $content .= "use Illuminate\\Support\\Facades\\Hash;\n";
        }
        
        $content .= "\n/**\n";
        $content .= " * 🧬 Seeder: {$className}\n";
        $content .= " * \n";
        $content .= " * توليد بيانات وهمية لجدول {$tableName}\n";
        $content .= " * \n";
        $content .= " * @version 1.0.0\n";
        $content .= " * @since " . date('Y-m-d') . "\n";
        $content .= " */\n";
        $content .= "class {$className} extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run(): void\n";
        $content .= "    {\n";
        $content .= "        \$faker = Faker::create('ar_SA');\n\n";
        $content .= "        for (\$i = 0; \$i < {$count}; \$i++) {\n";
        $content .= "            {$modelName}::create([\n";
        
        // إضافة الأعمدة
        foreach ($parsed['columns'] as $column => $config) {
            $fakerCode = $this->getFakerCode($column, $config);
            $content .= "                '{$column}' => {$fakerCode}, // {$this->getColumnComment($column)}\n";
        }
        
        $content .= "            ]);\n";
        $content .= "        }\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    /**
     * بناء Seeder من JSON Schema
     */
    protected function buildSeederFromJson(array $schema): string
    {
        $className = $this->getSeederClassName($schema['table_name']);
        $modelName = $schema['model_name'];
        $tableName = $schema['table_name'];
        $count = $schema['count'];
        $columns = $schema['columns'] ?? [];
        
        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use App\\Models\\{$modelName};\n";
        $content .= "use Faker\\Factory as Faker;\n";
        
        // التحقق من الحاجة لـ Hash
        $needsHash = false;
        foreach ($columns as $column => $config) {
            if (($config['type'] ?? '') === 'password') {
                $needsHash = true;
                break;
            }
        }
        
        if ($needsHash) {
            $content .= "use Illuminate\\Support\\Facades\\Hash;\n";
        }
        
        // التحقق من الحاجة لـ Foreign Keys
        $foreignKeys = [];
        foreach ($columns as $column => $config) {
            if (($config['type'] ?? '') === 'foreignKey') {
                $foreignModel = $config['model'] ?? null;
                if ($foreignModel && !in_array($foreignModel, $foreignKeys)) {
                    $foreignKeys[] = $foreignModel;
                }
            }
        }
        
        foreach ($foreignKeys as $foreignModel) {
            $content .= "use App\\Models\\{$foreignModel};\n";
        }
        
        $content .= "\n/**\n";
        $content .= " * 🧬 Seeder: {$className}\n";
        $content .= " * \n";
        $content .= " * توليد بيانات وهمية لجدول {$tableName}\n";
        $content .= " * \n";
        $content .= " * @version 1.0.0\n";
        $content .= " * @since " . date('Y-m-d') . "\n";
        $content .= " */\n";
        $content .= "class {$className} extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run(): void\n";
        $content .= "    {\n";
        
        // Locale
        $locale = $schema['locale'] ?? 'ar_SA';
        $content .= "        \$faker = Faker::create('{$locale}');\n";
        
        // الحصول على IDs للـ Foreign Keys
        foreach ($foreignKeys as $foreignModel) {
            $varName = Str::camel($foreignModel) . 'Ids';
            $content .= "        \${$varName} = {$foreignModel}::pluck('id')->toArray();\n";
        }
        
        $content .= "\n";
        $content .= "        for (\$i = 0; \$i < {$count}; \$i++) {\n";
        $content .= "            {$modelName}::create([\n";
        
        // إضافة الأعمدة
        foreach ($columns as $column => $config) {
            $fakerCode = $this->getFakerCodeFromConfig($column, $config);
            $content .= "                '{$column}' => {$fakerCode}, // {$this->getColumnComment($column)}\n";
        }
        
        $content .= "            ]);\n";
        $content .= "        }\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    /**
     * الحصول على كود Faker من التكوين
     */
    protected function getFakerCodeFromConfig(string $column, array $config): string
    {
        $type = $config['type'] ?? 'text';
        
        switch ($type) {
            case 'name':
                return '$faker->name';
            
            case 'firstName':
                return '$faker->firstName';
            
            case 'lastName':
                return '$faker->lastName';
            
            case 'email':
                $unique = $config['unique'] ?? false;
                return $unique ? '$faker->unique()->safeEmail' : '$faker->safeEmail';
            
            case 'username':
                return '$faker->userName';
            
            case 'password':
                return "Hash::make('password')";
            
            case 'phone':
                $nullable = $config['nullable'] ?? false;
                return $nullable ? '$faker->optional()->phoneNumber' : '$faker->phoneNumber';
            
            case 'address':
                return '$faker->address';
            
            case 'city':
                return '$faker->city';
            
            case 'country':
                return '$faker->country';
            
            case 'number':
                $min = $config['min'] ?? 1;
                $max = $config['max'] ?? 100;
                return "\$faker->numberBetween({$min}, {$max})";
            
            case 'float':
            case 'price':
                $decimals = $config['decimals'] ?? 2;
                $min = $config['min'] ?? 0;
                $max = $config['max'] ?? 1000;
                return "\$faker->randomFloat({$decimals}, {$min}, {$max})";
            
            case 'boolean':
                $default = $config['default'] ?? null;
                if ($default !== null) {
                    return $default ? 'true' : 'false';
                }
                return '$faker->boolean';
            
            case 'date':
                return '$faker->date()';
            
            case 'dateTime':
                return '$faker->dateTime()';
            
            case 'time':
                return '$faker->time()';
            
            case 'text':
                $length = $config['length'] ?? 200;
                return "\$faker->text({$length})";
            
            case 'paragraph':
                return '$faker->paragraph';
            
            case 'sentence':
                return '$faker->sentence';
            
            case 'word':
                return '$faker->word';
            
            case 'slug':
                return '$faker->slug';
            
            case 'url':
                return '$faker->url';
            
            case 'imageUrl':
                $width = $config['width'] ?? 640;
                $height = $config['height'] ?? 480;
                $category = $config['category'] ?? 'products';
                return "\$faker->imageUrl({$width}, {$height}, '{$category}')";
            
            case 'uuid':
                return '$faker->uuid';
            
            case 'enum':
                $values = $config['values'] ?? ['active', 'inactive'];
                $valuesStr = "'" . implode("', '", $values) . "'";
(Content truncated due to size limit. Use page ranges or line ranges to read remaining content)
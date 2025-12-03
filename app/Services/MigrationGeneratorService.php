<?php

namespace App\Services;

use App\Models\MigrationGeneration;
use App\Models\MigrationTemplate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * 🧬 Service: MigrationGeneratorService
 * 
 * خدمة توليد الـ migrations بشكل ذكي
 * 
 * @version 1.0.0
 * @since 2025-12-03
 */
class MigrationGeneratorService
{
    /**
     * مسار مجلد الـ migrations
     */
    protected string $migrationsPath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->migrationsPath = database_path('migrations');
    }

    /**
     * توليد migration من وصف نصي
     */
    public function generateFromText(string $description, string $inputMethod = 'web', ?int $userId = null): MigrationGeneration
    {
        // تحليل الوصف النصي
        $parsed = $this->parseTextDescription($description);
        
        // إنشاء سجل في قاعدة البيانات
        $generation = MigrationGeneration::create([
            'name' => $parsed['name'],
            'description' => $description,
            'table_name' => $parsed['table_name'],
            'migration_type' => $parsed['type'],
            'input_method' => $inputMethod,
            'input_data' => $parsed,
            'generated_content' => '',
            'status' => MigrationGeneration::STATUS_DRAFT,
            'created_by' => $userId,
        ]);

        // توليد المحتوى
        $content = $this->buildMigrationContent($parsed);
        
        // حفظ المحتوى
        $generation->update([
            'generated_content' => $content,
            'status' => MigrationGeneration::STATUS_GENERATED,
        ]);

        return $generation;
    }

    /**
     * توليد migration من JSON Schema
     */
    public function generateFromJson(array $schema, string $inputMethod = 'json', ?int $userId = null): MigrationGeneration
    {
        // التحقق من صحة الـ schema
        $this->validateJsonSchema($schema);
        
        // إنشاء سجل في قاعدة البيانات
        $generation = MigrationGeneration::create([
            'name' => $schema['name'] ?? $this->generateMigrationName($schema['table_name'], $schema['type'] ?? 'create'),
            'description' => $schema['description'] ?? null,
            'table_name' => $schema['table_name'],
            'migration_type' => $schema['type'] ?? 'create',
            'input_method' => $inputMethod,
            'input_data' => $schema,
            'generated_content' => '',
            'status' => MigrationGeneration::STATUS_DRAFT,
            'created_by' => $userId,
        ]);

        // توليد المحتوى
        $content = $this->buildMigrationFromJson($schema);
        
        // حفظ المحتوى
        $generation->update([
            'generated_content' => $content,
            'status' => MigrationGeneration::STATUS_GENERATED,
        ]);

        return $generation;
    }

    /**
     * توليد migration من قالب
     */
    public function generateFromTemplate(int $templateId, array $variables, ?int $userId = null): MigrationGeneration
    {
        $template = MigrationTemplate::findOrFail($templateId);
        
        // استبدال المتغيرات
        $content = $template->render($variables);
        
        // زيادة عداد الاستخدام
        $template->incrementUsage();
        
        // إنشاء سجل
        $generation = MigrationGeneration::create([
            'name' => $variables['name'] ?? 'migration_from_template_' . $templateId,
            'description' => "Generated from template: {$template->name}",
            'table_name' => $variables['table_name'] ?? 'unknown',
            'migration_type' => $variables['type'] ?? 'create',
            'input_method' => 'template',
            'input_data' => $variables,
            'generated_content' => $content,
            'status' => MigrationGeneration::STATUS_GENERATED,
            'created_by' => $userId,
        ]);

        return $generation;
    }

    /**
     * حفظ الـ migration كملف
     */
    public function saveToFile(MigrationGeneration $generation): string
    {
        $timestamp = Carbon::now()->format('Y_m_d_His');
        $fileName = "{$timestamp}_{$generation->name}.php";
        $filePath = $this->migrationsPath . '/' . $fileName;
        
        // حفظ الملف
        File::put($filePath, $generation->generated_content);
        
        // تحديث مسار الملف
        $generation->update(['file_path' => $filePath]);
        
        return $filePath;
    }

    /**
     * تحليل الوصف النصي
     */
    protected function parseTextDescription(string $description): array
    {
        // هذه دالة مبسطة - يمكن تحسينها باستخدام AI
        $lines = explode("\n", $description);
        
        $result = [
            'name' => '',
            'table_name' => '',
            'type' => 'create',
            'columns' => [],
            'indexes' => [],
            'relationships' => [],
        ];
        
        // محاولة استخراج اسم الجدول
        foreach ($lines as $line) {
            if (preg_match('/جدول\s+(\w+)/u', $line, $matches)) {
                $result['table_name'] = Str::snake($matches[1]);
                $result['name'] = 'create_' . $result['table_name'] . '_table';
                break;
            }
        }
        
        // استخراج الأعمدة
        foreach ($lines as $line) {
            if (preg_match('/-\s*(.+)/u', $line, $matches)) {
                $columnDesc = trim($matches[1]);
                $result['columns'][] = $this->parseColumnDescription($columnDesc);
            }
        }
        
        return $result;
    }

    /**
     * تحليل وصف عمود
     */
    protected function parseColumnDescription(string $description): array
    {
        $column = [
            'name' => '',
            'type' => 'string',
            'length' => null,
            'nullable' => false,
            'unique' => false,
            'default' => null,
            'comment' => $description,
        ];
        
        // كلمات مفتاحية للأنواع
        $typeKeywords = [
            'اسم' => 'string',
            'نص' => 'text',
            'رقم' => 'integer',
            'سعر' => 'decimal',
            'مبلغ' => 'decimal',
            'تاريخ' => 'date',
            'وقت' => 'datetime',
            'صورة' => 'string',
            'ملف' => 'string',
            'بريد' => 'string',
            'هاتف' => 'string',
        ];
        
        foreach ($typeKeywords as $keyword => $type) {
            if (Str::contains($description, $keyword)) {
                $column['type'] = $type;
                $column['name'] = Str::snake($description);
                break;
            }
        }
        
        return $column;
    }

    /**
     * التحقق من صحة JSON Schema
     */
    protected function validateJsonSchema(array $schema): void
    {
        if (!isset($schema['table_name'])) {
            throw new \InvalidArgumentException('table_name is required in JSON schema');
        }
        
        if (!isset($schema['columns']) || !is_array($schema['columns'])) {
            throw new \InvalidArgumentException('columns array is required in JSON schema');
        }
    }

    /**
     * بناء محتوى الـ migration من البيانات المحللة
     */
    protected function buildMigrationContent(array $parsed): string
    {
        $tableName = $parsed['table_name'];
        $gene = strtoupper($tableName);
        $date = Carbon::now()->format('Y-m-d');
        
        $content = "<?php\n\n";
        $content .= "use Illuminate\\Database\\Migrations\\Migration;\n";
        $content .= "use Illuminate\\Database\\Schema\\Blueprint;\n";
        $content .= "use Illuminate\\Support\\Facades\\Schema;\n\n";
        $content .= "/**\n";
        $content .= " * 🧬 Gene: {$gene}\n";
        $content .= " * Migration: إنشاء جدول {$tableName}\n";
        $content .= " * \n";
        $content .= " * 💡 الفكرة:\n";
        $content .= " * " . ($parsed['description'] ?? "جدول {$tableName}") . "\n";
        $content .= " * \n";
        $content .= " * @version 1.0.0\n";
        $content .= " * @since {$date}\n";
        $content .= " */\n";
        $content .= "return new class extends Migration\n";
        $content .= "{\n";
        $content .= "    /**\n";
        $content .= "     * Run the migrations.\n";
        $content .= "     */\n";
        $content .= "    public function up(): void\n";
        $content .= "    {\n";
        $content .= "        Schema::create('{$tableName}', function (Blueprint \$table) {\n";
        $content .= "            \$table->id();\n\n";
        
        // إضافة الأعمدة
        if (!empty($parsed['columns'])) {
            $content .= "            // الأعمدة\n";
            foreach ($parsed['columns'] as $column) {
                $content .= $this->buildColumnDefinition($column);
            }
            $content .= "\n";
        }
        
        // إضافة الأعمدة الافتراضية
        $content .= "            // من أنشأ وعدّل\n";
        $content .= "            \$table->foreignId('created_by')->nullable()->constrained('users');\n";
        $content .= "            \$table->foreignId('updated_by')->nullable()->constrained('users');\n\n";
        $content .= "            \$table->timestamps();\n";
        $content .= "            \$table->softDeletes();\n";
        
        $content .= "        });\n";
        $content .= "    }\n\n";
        $content .= "    /**\n";
        $content .= "     * Reverse the migrations.\n";
        $content .= "     */\n";
        $content .= "    public function down(): void\n";
        $content .= "    {\n";
        $content .= "        Schema::dropIfExists('{$tableName}');\n";
        $content .= "    }\n";
        $content .= "};\n";
        
        return $content;
    }

    /**
     * بناء محتوى الـ migration من JSON
     */
    protected function buildMigrationFromJson(array $schema): string
    {
        $tableName = $schema['table_name'];
        $gene = strtoupper($tableName);
        $date = Carbon::now()->format('Y-m-d');
        $type = $schema['type'] ?? 'create';
        
        $content = "<?php\n\n";
        $content .= "use Illuminate\\Database\\Migrations\\Migration;\n";
        $content .= "use Illuminate\\Database\\Schema\\Blueprint;\n";
        $content .= "use Illuminate\\Support\\Facades\\Schema;\n\n";
        $content .= "/**\n";
        $content .= " * 🧬 Gene: {$gene}\n";
        $content .= " * Migration: " . ($schema['description'] ?? "إنشاء جدول {$tableName}") . "\n";
        $content .= " * \n";
        $content .= " * @version 1.0.0\n";
        $content .= " * @since {$date}\n";
        $content .= " */\n";
        $content .= "return new class extends Migration\n";
        $content .= "{\n";
        $content .= "    public function up(): void\n";
        $content .= "    {\n";
        
        if ($type === 'create') {
            $content .= "        Schema::create('{$tableName}', function (Blueprint \$table) {\n";
            $content .= "            \$table->id();\n\n";
            
            // إضافة الأعمدة من JSON
            foreach ($schema['columns'] as $column) {
                $content .= $this->buildColumnFromJson($column);
            }
            
            $content .= "\n            \$table->timestamps();\n";
            $content .= "            \$table->softDeletes();\n";
            
            // إضافة الفهارس
            if (isset($schema['indexes'])) {
                $content .= "\n            // Indexes\n";
                foreach ($schema['indexes'] as $index) {
                    $content .= $this->buildIndexFromJson($index);
                }
            }
            
            $content .= "        });\n";
        }
        
        $content .= "    }\n\n";
        $content .= "    public function down(): void\n";
        $content .= "    {\n";
        $content .= "        Schema::dropIfExists('{$tableName}');\n";
        $content .= "    }\n";
        $content .= "};\n";
        
        return $content;
    }

    /**
     * بناء تعريف عمود
     */
    protected function buildColumnDefinition(array $column): string
    {
        $name = $column['name'];
        $type = $column['type'];
        $comment = $column['comment'] ?? '';
        
        $line = "            \$table->{$type}('{$name}'";
        
        if (isset($column['length'])) {
            $line .= ", {$column['length']}";
        }
        
        $line .= ")";
        
        if ($column['nullable'] ?? false) {
            $line .= "->nullable()";
        }
        
        if ($column['unique'] ?? false) {
            $line .= "->unique()";
        }
        
        if (isset($column['default'])) {
            $default = is_string($column['default']) ? "'{$column['default']}'" : $column['default'];
            $line .= "->default({$default})";
        }
        
        if ($comment) {
            $line .= "->comment('{$comment}')";
        }
        
        $line .= ";\n";
        
        return $line;
    }

    /**
     * بناء عمود من JSON
     */
    protected function buildColumnFromJson(array $column): string
    {
        $name = $column['name'];
        $type = $column['type'];
        $comment = $column['comment'] ?? '';
        
        $line = "            \$table->{$type}('{$name}'";
        
        // إضافة المعاملات حسب النوع
        if ($type === 'string' && isset($column['length'])) {
            $line .= ", {$column['length']}";
        } elseif ($type === 'decimal' && isset($column['precision'])) {
            $line .= ", {$column['precision']}, {$column['scale']}";
        } elseif ($type === 'enum' && isset($column['values'])) {
            $values = array_map(fn($v) => "'{$v}'", $column['values']);
            $line .= ", [" . implode(', ', $values) . "]";
        }
        
        $line .= ")";
        
        // إضافة Modifiers
        if ($column['nullable'] ?? false) {
            $line .= "->nullable()";
        }
        
        if ($column['unique'] ?? false) {
            $line .= "->unique()";
        }
        
        if (isset($column['default'])) {
            $default = is_string($column['default']) ? "'{$column['default']}'" : $column['default'];
            $line .= "->default({$default})";
        }
        
        if ($comment) {
            $line .= "->comment('{$comment}')";
        }
        
        // معالجة Foreign Keys
        if ($type === 'foreignId' && isset($column['references'])) {
            $line .= "->constrained('{$column['references']}')";
            
            if (isset($column['onDelete'])) {
                $line .= "->onDelete('{$column['onDelete']}')";
            }
        }
        
        $line .= ";\n";
        
        return $line;
    }

    /**
     * بناء فهرس من JSON
     */
    protected function buildIndexFromJson(array $index): string
    {
        $columns = is_array($index['columns']) ? $index['columns'] : [$index['columns']];
        $columnsStr = "'" . implode("', '", $columns) . "'";
        
        if ($index['unique'] ?? false) {
            return "            \$table->unique([{$columnsStr}]);\n";
        }
        
        return "            \$table->index([{$columnsStr}]);\n";
    }

    /**
     * توليد اسم الـ migration
     */
    protected function generateMigrationName(string $tableName, string $type): string
    {
        $action = match($type) {
            'create' => 'create',
            'alter' => 'modify',
            'drop' => 'drop',
            default => 'create',
        };
        
        return "{$action}_{$tableName}_table";
    }

    /**
     * الحصول على جميع الـ migrations المولدة
     */
    public function getAllGenerations()
    {
        return MigrationGeneration::with(['creator', 'updater'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * حذف migration
     */
    public function deleteGeneration(int $id): bool
    {
        $generation = MigrationGeneration::findOrFail($id);
        
        // حذف الملف إن وجد
        if ($generation->file_path && File::exists($generation->file_path)) {
            File::delete($generation->file_path);
        }
        
        return $generation->delete();
    }
}

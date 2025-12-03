<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * 🧬 Service: ModelParserService
 * 
 * خدمة تحليل المدخلات لتوليد الـ Models
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Services
 * @package App\Services
 */
class ModelParserService
{
    /**
     * تحليل وصف نصي لـ Model
     * 
     * @param string $description
     * @return array
     */
    public function parseTextDescription(string $description): array
    {
        $result = [
            'name' => null,
            'table_name' => null,
            'namespace' => 'App\\Models',
            'extends' => 'Model',
            'attributes' => [],
            'fillable' => [],
            'hidden' => [],
            'casts' => [],
            'dates' => [],
            'relations' => [],
            'scopes' => [],
            'traits' => [],
            'has_timestamps' => true,
            'has_soft_deletes' => false,
            'has_observer' => false,
            'has_factory' => false,
        ];

        // استخراج اسم الـ Model
        if (preg_match('/(?:Model|model|نموذج)\s+(?:لـ|ل|for)?\s*([A-Za-z\u0600-\u06FF]+)/u', $description, $matches)) {
            $arabicName = $matches[1];
            $result['name'] = $this->translateToEnglish($arabicName);
        } elseif (preg_match('/(?:create|أنشئ|Create)\s+([A-Z][a-zA-Z]+)/u', $description, $matches)) {
            $result['name'] = $matches[1];
        }

        // استخراج اسم الجدول
        if ($result['name']) {
            $result['table_name'] = Str::snake(Str::plural($result['name']));
        }

        // استخراج الخصائص
        $this->extractAttributes($description, $result);

        // استخراج العلاقات
        $this->extractRelations($description, $result);

        // استخراج Traits
        $this->extractTraits($description, $result);

        // استخراج Scopes
        $this->extractScopes($description, $result);

        return $result;
    }

    /**
     * استخراج الخصائص من الوصف
     * 
     * @param string $description
     * @param array &$result
     */
    protected function extractAttributes(string $description, array &$result): void
    {
        // البحث عن الخصائص بنمط: "- الاسم (name) نوع البيانات"
        preg_match_all('/[-•]\s*([^\(]+)\s*\(([^\)]+)\)\s*([^\n]+)?/u', $description, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $arabicName = trim($match[1]);
            $englishName = trim($match[2]);
            $details = isset($match[3]) ? trim($match[3]) : '';

            $attribute = [
                'name' => $englishName,
                'arabic_name' => $arabicName,
                'type' => $this->detectDataType($details),
                'nullable' => $this->isNullable($details),
                'unique' => $this->isUnique($details),
                'default' => $this->extractDefault($details),
            ];

            $result['attributes'][] = $attribute;

            // إضافة إلى fillable
            if (!in_array($englishName, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                $result['fillable'][] = $englishName;
            }

            // إضافة إلى hidden إذا كان password
            if (in_array($englishName, ['password', 'remember_token', 'api_token'])) {
                $result['hidden'][] = $englishName;
            }

            // إضافة إلى casts
            $cast = $this->getCastType($attribute['type'], $details);
            if ($cast) {
                $result['casts'][$englishName] = $cast;
            }
        }

        // التحقق من soft deletes
        if (preg_match('/soft\s*delete|حذف\s*ناعم/iu', $description)) {
            $result['has_soft_deletes'] = true;
            $result['traits'][] = 'SoftDeletes';
        }

        // التحقق من timestamps
        if (preg_match('/no\s*timestamp|بدون\s*timestamp/iu', $description)) {
            $result['has_timestamps'] = false;
        }
    }

    /**
     * استخراج العلاقات من الوصف
     * 
     * @param string $description
     * @param array &$result
     */
    protected function extractRelations(string $description, array &$result): void
    {
        // علاقة hasMany
        if (preg_match_all('/hasMany\s+(?:مع|with)?\s*([A-Za-z\u0600-\u06FF]+)/iu', $description, $matches)) {
            foreach ($matches[1] as $relatedModel) {
                $result['relations'][] = [
                    'type' => 'hasMany',
                    'model' => $this->translateToEnglish($relatedModel),
                    'method' => Str::camel(Str::plural($this->translateToEnglish($relatedModel))),
                ];
            }
        }

        // علاقة belongsTo
        if (preg_match_all('/belongsTo\s+(?:مع|with)?\s*([A-Za-z\u0600-\u06FF]+)/iu', $description, $matches)) {
            foreach ($matches[1] as $relatedModel) {
                $result['relations'][] = [
                    'type' => 'belongsTo',
                    'model' => $this->translateToEnglish($relatedModel),
                    'method' => Str::camel($this->translateToEnglish($relatedModel)),
                ];
            }
        }

        // علاقة belongsToMany
        if (preg_match_all('/belongsToMany\s+(?:مع|with)?\s*([A-Za-z\u0600-\u06FF]+)/iu', $description, $matches)) {
            foreach ($matches[1] as $relatedModel) {
                $result['relations'][] = [
                    'type' => 'belongsToMany',
                    'model' => $this->translateToEnglish($relatedModel),
                    'method' => Str::camel(Str::plural($this->translateToEnglish($relatedModel))),
                ];
            }
        }

        // علاقة hasOne
        if (preg_match_all('/hasOne\s+(?:مع|with)?\s*([A-Za-z\u0600-\u06FF]+)/iu', $description, $matches)) {
            foreach ($matches[1] as $relatedModel) {
                $result['relations'][] = [
                    'type' => 'hasOne',
                    'model' => $this->translateToEnglish($relatedModel),
                    'method' => Str::camel($this->translateToEnglish($relatedModel)),
                ];
            }
        }
    }

    /**
     * استخراج Traits من الوصف
     * 
     * @param string $description
     * @param array &$result
     */
    protected function extractTraits(string $description, array &$result): void
    {
        $traitMappings = [
            'factory' => 'HasFactory',
            'notifiable' => 'Notifiable',
            'uuid' => 'HasUuid',
            'searchable' => 'Searchable',
        ];

        foreach ($traitMappings as $keyword => $trait) {
            if (preg_match("/{$keyword}/i", $description)) {
                if (!in_array($trait, $result['traits'])) {
                    $result['traits'][] = $trait;
                }
            }
        }
    }

    /**
     * استخراج Scopes من الوصف
     * 
     * @param string $description
     * @param array &$result
     */
    protected function extractScopes(string $description, array &$result): void
    {
        // البحث عن Scopes بنمط: "scope: active where is_active = true"
        if (preg_match_all('/scope[:\s]+([a-z_]+)\s+where\s+([^\n]+)/i', $description, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $result['scopes'][] = [
                    'name' => $match[1],
                    'condition' => trim($match[2]),
                ];
            }
        }
    }

    /**
     * تحليل بنية جدول من قاعدة البيانات
     * 
     * @param string $tableName
     * @return array
     */
    public function parseTableStructure(string $tableName): array
    {
        $result = [
            'model_name' => Str::studly(Str::singular($tableName)),
            'table_name' => $tableName,
            'attributes' => [],
            'fillable' => [],
            'hidden' => [],
            'casts' => [],
            'dates' => [],
            'relations' => [],
            'traits' => ['HasFactory'],
            'has_timestamps' => false,
            'has_soft_deletes' => false,
        ];

        // الحصول على الأعمدة
        $columns = $this->getTableColumns($tableName);

        foreach ($columns as $column) {
            $columnName = $column['name'];

            // تخطي الأعمدة الخاصة
            if (in_array($columnName, ['id'])) {
                continue;
            }

            // التحقق من timestamps
            if (in_array($columnName, ['created_at', 'updated_at'])) {
                $result['has_timestamps'] = true;
                continue;
            }

            // التحقق من soft deletes
            if ($columnName === 'deleted_at') {
                $result['has_soft_deletes'] = true;
                $result['traits'][] = 'SoftDeletes';
                continue;
            }

            // إضافة إلى attributes
            $result['attributes'][] = [
                'name' => $columnName,
                'type' => $column['type'],
                'nullable' => $column['nullable'],
            ];

            // إضافة إلى fillable
            $result['fillable'][] = $columnName;

            // إضافة إلى hidden إذا كان password
            if (in_array($columnName, ['password', 'remember_token', 'api_token'])) {
                $result['hidden'][] = $columnName;
            }

            // إضافة إلى casts
            $cast = $this->getDatabaseCastType($column['type']);
            if ($cast) {
                $result['casts'][$columnName] = $cast;
            }
        }

        // محاولة اكتشاف العلاقات من Foreign Keys
        $result['relations'] = $this->detectRelationsFromForeignKeys($tableName);

        return $result;
    }

    /**
     * الحصول على أعمدة الجدول
     * 
     * @param string $tableName
     * @return array
     */
    protected function getTableColumns(string $tableName): array
    {
        $columns = [];
        $connection = config('database.default');

        if ($connection === 'mysql') {
            $rawColumns = DB::select("SHOW COLUMNS FROM {$tableName}");
            foreach ($rawColumns as $column) {
                $columns[] = [
                    'name' => $column->Field,
                    'type' => $column->Type,
                    'nullable' => $column->Null === 'YES',
                ];
            }
        } elseif ($connection === 'pgsql') {
            $rawColumns = DB::select("
                SELECT column_name, data_type, is_nullable
                FROM information_schema.columns
                WHERE table_name = ?
            ", [$tableName]);
            foreach ($rawColumns as $column) {
                $columns[] = [
                    'name' => $column->column_name,
                    'type' => $column->data_type,
                    'nullable' => $column->is_nullable === 'YES',
                ];
            }
        }

        return $columns;
    }

    /**
     * اكتشاف العلاقات من Foreign Keys
     * 
     * @param string $tableName
     * @return array
     */
    protected function detectRelationsFromForeignKeys(string $tableName): array
    {
        $relations = [];

        // البحث عن أعمدة تنتهي بـ _id (belongsTo)
        $columns = $this->getTableColumns($tableName);
        foreach ($columns as $column) {
            if (preg_match('/^(.+)_id$/', $column['name'], $matches)) {
                $relatedModel = Str::studly($matches[1]);
                $relations[] = [
                    'type' => 'belongsTo',
                    'model' => $relatedModel,
                    'method' => Str::camel($relatedModel),
                    'foreign_key' => $column['name'],
                ];
            }
        }

        return $relations;
    }

    /**
     * تحليل ملف Migration
     * 
     * @param string $migrationFile
     * @return array
     */
    public function parseMigrationFile(string $migrationFile): array
    {
        $migrationPath = database_path('migrations/' . $migrationFile);
        
        if (!File::exists($migrationPath)) {
            throw new \Exception("ملف Migration غير موجود: {$migrationFile}");
        }

        $content = File::get($migrationPath);

        // استخراج اسم الجدول
        preg_match('/Schema::create\([\'"]([^\'"]+)[\'"]/i', $content, $matches);
        $tableName = $matches[1] ?? null;

        if (!$tableName) {
            throw new \Exception("لم يتم العثور على اسم الجدول في Migration");
        }

        $result = [
            'model_name' => Str::studly(Str::singular($tableName)),
            'table_name' => $tableName,
            'attributes' => [],
            'fillable' => [],
            'casts' => [],
            'relations' => [],
            'traits' => ['HasFactory'],
            'has_timestamps' => false,
            'has_soft_deletes' => false,
        ];

        // استخراج الأعمدة من المحتوى
        $this->extractColumnsFromMigration($content, $result);

        return $result;
    }

    /**
     * استخراج الأعمدة من محتوى Migration
     * 
     * @param string $content
     * @param array &$result
     */
    protected function extractColumnsFromMigration(string $content, array &$result): void
    {
        // البحث عن timestamps
        if (preg_match('/\$table->timestamps\(\)/', $content)) {
            $result['has_timestamps'] = true;
        }

        // البحث عن softDeletes
        if (preg_match('/\$table->softDeletes\(\)/', $content)) {
            $result['has_soft_deletes'] = true;
            $result['traits'][] = 'SoftDeletes';
        }

        // البحث عن الأعمدة
        preg_match_all('/\$table->([a-z]+)\([\'"]([^\'"]+)[\'"]/', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $type = $match[1];
            $name = $match[2];

            if (in_array($name, ['id'])) {
                continue;
            }

            $result['attributes'][] = [
                'name' => $name,
                'type' => $type,
            ];

            $result['fillable'][] = $name;

            $cast = $this->getMigrationCastType($type);
            if ($cast) {
                $result['casts'][$name] = $cast;
            }
        }
    }

    /**
     * كشف نوع البيانات من الوصف
     * 
     * @param string $details
     * @return string
     */
    protected function detectDataType(string $details): string
    {
        $details = strtolower($details);

        if (preg_match('/string|text|نص/', $details)) return 'string';
        if (preg_match('/integer|int|رقم\s*صحيح/', $details)) return 'integer';
        if (preg_match('/boolean|bool|منطقي/', $details)) return 'boolean';
        if (preg_match('/date|تاريخ/', $details)) return 'date';
        if (preg_match('/datetime|وقت/', $details)) return 'datetime';
        if (preg_match('/decimal|float|عشري/', $details)) return 'decimal';
        if (preg_match('/json/', $details)) return 'json';

        return 'string';
    }

    /**
     * التحقق من nullable
     * 
     * @param string $details
     * @return bool
     */
    protected function isNullable(string $details): bool
    {
        return preg_match('/nullable|اختياري|optional/iu', $details) > 0;
    }

    /**
     * التحقق من unique
     * 
     * @param string $details
     * @return bool
     */
    protected function isUnique(string $details): bool
    {
        return preg_match('/unique|فريد/iu', $details) > 0;
    }

    /**
     * استخراج القيمة الافتراضية
     * 
     * @param string $details
     * @return mixed
     */
    protected function extractDefault(string $details)
    {
        if (preg_match('/default[:\s]+([^\s,]+)/i', $details, $matches)) {
            $value = trim($matches[1]);
            if ($value === 'true') return true;
            if ($value === 'false') return false;
            if (is_numeric($value)) return $value;
            return $value;
        }
        return null;
    }

    /**
     * الحصول على نوع Cast
     * 
     * @param string $type
     * @param string $details
     * @return string|null
     */
    protected function getCastType(string $type, string $details): ?string
    {
        return match($type) {
            'boolean' => 'boolean',
            'integer' => 'integer',
            'decimal', 'float' => 'decimal:2',
            'date' => 'date',
            'datetime' => 'datetime',
            'json' => 'array',
            default => null,
        };
    }

    /**
     * الحصول على نوع Cast من نوع قاعدة البيانات
     * 
     * @param string $dbType
     * @return string|null
     */
    protected function getDatabaseCastType(string $dbType): ?string
    {
        $dbType = strtolower($dbType);

        if (preg_match('/int/', $dbType)) return 'integer';
        if (preg_match('/tinyint\(1\)|boolean/', $dbType)) return 'boolean';
        if (preg_match('/decimal|float|double/', $dbType)) return 'decimal:2';
        if (preg_match('/date/', $dbType)) return 'date';
        if (preg_match('/datetime|timestamp/', $dbType)) return 'datetime';
        if (preg_match('/json/', $dbType)) return 'array';

        return null;
    }

    /**
     * الحصول على نوع Cast من نوع Migration
     * 
     * @param string $migrationType
     * @return string|null
     */
    protected function getMigrationCastType(string $migrationType): ?string
    {
        return match($migrationType) {
            'boolean' => 'boolean',
            'integer', 'bigInteger', 'unsignedBigInteger' => 'integer',
            'decimal', 'float', 'double' => 'decimal:2',
            'date' => 'date',
            'datetime', 'timestamp' => 'datetime',
            'json' => 'array',
            default => null,
        };
    }

    /**
     * ترجمة من العربية إلى الإنجليزية (أسماء شائعة)
     * 
     * @param string $arabic
     * @return string
     */
    protected function translateToEnglish(string $arabic): string
    {
        $translations = [
            'المستخدمين' => 'User',
            'المستخدم' => 'User',
            'الطلبات' => 'Order',
            'الطلب' => 'Order',
            'المنتجات' => 'Product',
            'المنتج' => 'Product',
            'الفئات' => 'Category',
            'الفئة' => 'Category',
            'العملاء' => 'Customer',
            'العميل' => 'Customer',
            'الموردين' => 'Supplier',
            'المورد' => 'Supplier',
            'الموظفين' => 'Employee',
            'الموظف' => 'Employee',
            'الأدوار' => 'Role',
            'الدور' => 'Role',
            'الصلاحيات' => 'Permission',
            'الصلاحية' => 'Permission',
        ];

        return $translations[$arabic] ?? Str::studly($arabic);
    }
}

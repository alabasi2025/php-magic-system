<?php

namespace App\Services;

use App\Models\ModelGeneration;
use Illuminate\Support\Facades\File;

/**
 * 🧬 Service: ModelValidatorService
 * 
 * خدمة التحقق من صحة الـ Models المولدة
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Services
 * @package App\Services
 */
class ModelValidatorService
{
    /**
     * التحقق من صحة Model مولد
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    public function validate(ModelGeneration $generation): array
    {
        $results = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'checks' => [],
        ];

        // التحقق من وجود المحتوى
        $this->checkContent($generation, $results);

        // التحقق من صحة PHP Syntax
        $this->checkPhpSyntax($generation, $results);

        // التحقق من الـ Namespace
        $this->checkNamespace($generation, $results);

        // التحقق من الـ Class Name
        $this->checkClassName($generation, $results);

        // التحقق من الـ Table Name
        $this->checkTableName($generation, $results);

        // التحقق من الـ Fillable
        $this->checkFillable($generation, $results);

        // التحقق من الـ Relations
        $this->checkRelations($generation, $results);

        // التحقق من الـ Traits
        $this->checkTraits($generation, $results);

        // التحقق من الـ Casts
        $this->checkCasts($generation, $results);

        // تحديث حالة التحقق
        $generation->update([
            'is_validated' => $results['valid'],
            'validation_results' => $results,
            'warnings' => $results['warnings'],
        ]);

        if ($results['valid']) {
            $generation->markAsValidated();
        }

        return $results;
    }

    /**
     * التحقق من وجود المحتوى
     * 
     * @param ModelGeneration $generation
     * @param array &$results
     */
    protected function checkContent(ModelGeneration $generation, array &$results): void
    {
        if (empty($generation->generated_content)) {
            $results['valid'] = false;
            $results['errors'][] = 'المحتوى المولد فارغ';
        } else {
            $results['checks'][] = '✓ المحتوى موجود';
        }
    }

    /**
     * التحقق من صحة PHP Syntax
     * 
     * @param ModelGeneration $generation
     * @param array &$results
     */
    protected function checkPhpSyntax(ModelGeneration $generation, array &$results): void
    {
        if (empty($generation->generated_content)) {
            return;
        }

        // حفظ مؤقت للتحقق من الـ syntax
        $tempFile = sys_get_temp_dir() . '/model_' . $generation->id . '.php';
        File::put($tempFile, $generation->generated_content);

        // التحقق من الـ syntax
        $output = [];
        $returnVar = 0;
        exec("php -l {$tempFile} 2>&1", $output, $returnVar);

        File::delete($tempFile);

        if ($returnVar !== 0) {
            $results['valid'] = false;
            $results['errors'][] = 'خطأ في بناء الجملة (Syntax Error): ' . implode("\n", $output);
        } else {
            $results['checks'][] = '✓ بناء الجملة صحيح';
        }
    }

    /**
     * التحقق من الـ Namespace
     * 
     * @param ModelGeneration $generation
     * @param array &$results
     */
    protected function checkNamespace(ModelGeneration $generation, array &$results): void
    {
        if (empty($generation->namespace)) {
            $results['valid'] = false;
            $results['errors'][] = 'Namespace غير محدد';
        } elseif (!preg_match('/^[A-Za-z_\\\\][A-Za-z0-9_\\\\]*$/', $generation->namespace)) {
            $results['valid'] = false;
            $results['errors'][] = 'Namespace غير صالح';
        } else {
            $results['checks'][] = '✓ Namespace صحيح';
        }
    }

    /**
     * التحقق من الـ Class Name
     * 
     * @param ModelGeneration $generation
     * @param array &$results
     */
    protected function checkClassName(ModelGeneration $generation, array &$results): void
    {
        if (empty($generation->name)) {
            $results['valid'] = false;
            $results['errors'][] = 'اسم الـ Model غير محدد';
        } elseif (!preg_match('/^[A-Z][A-Za-z0-9]*$/', $generation->name)) {
            $results['valid'] = false;
            $results['errors'][] = 'اسم الـ Model غير صالح (يجب أن يبدأ بحرف كبير)';
        } else {
            $results['checks'][] = '✓ اسم الـ Model صحيح';
        }

        // التحقق من عدم وجود Model بنفس الاسم
        $modelPath = $generation->default_file_path;
        if (File::exists($modelPath) && !$generation->isDeployed()) {
            $results['warnings'][] = "يوجد Model بنفس الاسم في: {$modelPath}";
        }
    }

    /**
     * التحقق من الـ Table Name
     * 
     * @param ModelGeneration $generation
     * @param array &$results
     */
    protected function checkTableName(ModelGeneration $generation, array &$results): void
    {
        if (empty($generation->table_name)) {
            $results['valid'] = false;
            $results['errors'][] = 'اسم الجدول غير محدد';
        } elseif (!preg_match('/^[a-z_][a-z0-9_]*$/', $generation->table_name)) {
            $results['warnings'][] = 'اسم الجدول يجب أن يكون بأحرف صغيرة و underscores فقط';
        } else {
            $results['checks'][] = '✓ اسم الجدول صحيح';
        }
    }

    /**
     * التحقق من الـ Fillable
     * 
     * @param ModelGeneration $generation
     * @param array &$results
     */
    protected function checkFillable(ModelGeneration $generation, array &$results): void
    {
        if (empty($generation->fillable)) {
            $results['warnings'][] = 'لا يوجد حقول في fillable (قد يكون هذا مقصوداً)';
        } else {
            $results['checks'][] = '✓ Fillable محدد (' . count($generation->fillable) . ' حقل)';

            // التحقق من عدم وجود حقول محمية في fillable
            $protectedFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
            $invalidFields = array_intersect($generation->fillable, $protectedFields);
            
            if (!empty($invalidFields)) {
                $results['warnings'][] = 'حقول محمية في fillable: ' . implode(', ', $invalidFields);
            }
        }
    }

    /**
     * التحقق من الـ Relations
     * 
     * @param ModelGeneration $generation
     * @param array &$results
     */
    protected function checkRelations(ModelGeneration $generation, array &$results): void
    {
        if (empty($generation->relations)) {
            $results['checks'][] = '○ لا توجد علاقات محددة';
            return;
        }

        $validRelationTypes = [
            'hasOne', 'hasMany', 'belongsTo', 'belongsToMany',
            'hasOneThrough', 'hasManyThrough',
            'morphOne', 'morphMany', 'morphTo', 'morphToMany', 'morphedByMany'
        ];

        foreach ($generation->relations as $relation) {
            if (empty($relation['type'])) {
                $results['errors'][] = 'علاقة بدون نوع محدد';
                $results['valid'] = false;
                continue;
            }

            if (!in_array($relation['type'], $validRelationTypes)) {
                $results['errors'][] = "نوع علاقة غير صالح: {$relation['type']}";
                $results['valid'] = false;
            }

            if (empty($relation['model'])) {
                $results['errors'][] = "علاقة {$relation['type']} بدون model محدد";
                $results['valid'] = false;
            }

            if (empty($relation['method'])) {
                $results['warnings'][] = "علاقة {$relation['type']} بدون method name محدد";
            }
        }

        $results['checks'][] = '✓ العلاقات محددة (' . count($generation->relations) . ' علاقة)';
    }

    /**
     * التحقق من الـ Traits
     * 
     * @param ModelGeneration $generation
     * @param array &$results
     */
    protected function checkTraits(ModelGeneration $generation, array &$results): void
    {
        $traits = $generation->traits_list ?? [];

        if (empty($traits)) {
            $results['checks'][] = '○ لا توجد Traits محددة';
            return;
        }

        $validTraits = [
            'HasFactory', 'SoftDeletes', 'Notifiable', 'HasUuid', 'Searchable'
        ];

        foreach ($traits as $trait) {
            if (!in_array($trait, $validTraits)) {
                $results['warnings'][] = "Trait غير معروف: {$trait} (تأكد من إضافة use statement)";
            }
        }

        $results['checks'][] = '✓ Traits محددة (' . count($traits) . ' trait)';
    }

    /**
     * التحقق من الـ Casts
     * 
     * @param ModelGeneration $generation
     * @param array &$results
     */
    protected function checkCasts(ModelGeneration $generation, array &$results): void
    {
        if (empty($generation->casts)) {
            $results['checks'][] = '○ لا توجد Casts محددة';
            return;
        }

        $validCasts = [
            'integer', 'real', 'float', 'double', 'decimal',
            'string', 'boolean', 'object', 'array', 'collection',
            'date', 'datetime', 'immutable_date', 'immutable_datetime',
            'timestamp', 'json', 'encrypted'
        ];

        foreach ($generation->casts as $field => $cast) {
            // التحقق من decimal format
            if (preg_match('/^decimal:\d+$/', $cast)) {
                continue;
            }

            if (!in_array($cast, $validCasts)) {
                $results['warnings'][] = "Cast type غير معروف: {$cast} للحقل {$field}";
            }
        }

        $results['checks'][] = '✓ Casts محددة (' . count($generation->casts) . ' cast)';
    }

    /**
     * التحقق من صحة JSON Schema
     * 
     * @param array $schema
     * @return bool
     * @throws \Exception
     */
    public function validateJsonSchema(array $schema): bool
    {
        $required = ['name'];
        
        foreach ($required as $field) {
            if (!isset($schema[$field]) || empty($schema[$field])) {
                throw new \Exception("الحقل {$field} مطلوب في JSON Schema");
            }
        }

        // التحقق من اسم الـ Model
        if (!preg_match('/^[A-Z][A-Za-z0-9]*$/', $schema['name'])) {
            throw new \Exception('اسم الـ Model غير صالح (يجب أن يبدأ بحرف كبير)');
        }

        // التحقق من الـ attributes إذا وجدت
        if (isset($schema['attributes']) && !is_array($schema['attributes'])) {
            throw new \Exception('attributes يجب أن يكون array');
        }

        // التحقق من الـ relations إذا وجدت
        if (isset($schema['relations']) && !is_array($schema['relations'])) {
            throw new \Exception('relations يجب أن يكون array');
        }

        return true;
    }
}

<?php

namespace App\Services;

use App\Models\ModelGeneration;
use Illuminate\Support\Str;

/**
 * 🧬 Service: ModelBuilderService
 * 
 * خدمة بناء محتوى الـ Models
 * 
 * @version 1.0.0
 * @since 2025-12-03
 * @category Services
 * @package App\Services
 */
class ModelBuilderService
{
    /**
     * بناء محتوى Model كامل
     * 
     * @param ModelGeneration $generation
     * @return string
     */
    public function buildModelContent(ModelGeneration $generation): string
    {
        $content = [];

        // PHP Opening Tag
        $content[] = "<?php";
        $content[] = "";

        // Namespace
        $content[] = "namespace {$generation->namespace};";
        $content[] = "";

        // Use Statements
        $content = array_merge($content, $this->buildUseStatements($generation));
        $content[] = "";

        // PHPDoc
        $content = array_merge($content, $this->buildPhpDoc($generation));

        // Class Declaration
        $content[] = "class {$generation->name} extends {$generation->extends}";
        $content[] = "{";

        // Traits
        $content = array_merge($content, $this->buildTraits($generation));

        // Table Name
        if ($generation->table_name !== Str::snake(Str::plural($generation->name))) {
            $content[] = "    /**";
            $content[] = "     * The table associated with the model.";
            $content[] = "     *";
            $content[] = "     * @var string";
            $content[] = "     */";
            $content[] = "    protected \$table = '{$generation->table_name}';";
            $content[] = "";
        }

        // Fillable
        $content = array_merge($content, $this->buildFillable($generation));

        // Hidden
        if (!empty($generation->hidden)) {
            $content = array_merge($content, $this->buildHidden($generation));
        }

        // Casts
        if (!empty($generation->casts)) {
            $content = array_merge($content, $this->buildCasts($generation));
        }

        // Appends
        if (!empty($generation->appends)) {
            $content = array_merge($content, $this->buildAppends($generation));
        }

        // Timestamps
        if (!$generation->has_timestamps) {
            $content[] = "    /**";
            $content[] = "     * Indicates if the model should be timestamped.";
            $content[] = "     *";
            $content[] = "     * @var bool";
            $content[] = "     */";
            $content[] = "    public \$timestamps = false;";
            $content[] = "";
        }

        // Relations
        if (!empty($generation->relations)) {
            $content = array_merge($content, $this->buildRelations($generation));
        }

        // Scopes
        if (!empty($generation->scopes)) {
            $content = array_merge($content, $this->buildScopes($generation));
        }

        // Accessors
        if (!empty($generation->accessors)) {
            $content = array_merge($content, $this->buildAccessors($generation));
        }

        // Mutators
        if (!empty($generation->mutators)) {
            $content = array_merge($content, $this->buildMutators($generation));
        }

        // Class Closing
        $content[] = "}";

        return implode("\n", $content);
    }

    /**
     * بناء Use Statements
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    protected function buildUseStatements(ModelGeneration $generation): array
    {
        $uses = [];

        // Base Uses
        $uses[] = "use Illuminate\\Database\\Eloquent\\Model;";

        // Traits
        $traits = $generation->traits_list ?? [];
        
        if (in_array('HasFactory', $traits)) {
            $uses[] = "use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;";
        }
        
        if (in_array('SoftDeletes', $traits)) {
            $uses[] = "use Illuminate\\Database\\Eloquent\\SoftDeletes;";
        }

        if (in_array('Notifiable', $traits)) {
            $uses[] = "use Illuminate\\Notifications\\Notifiable;";
        }

        // Relations
        if (!empty($generation->relations)) {
            $relationTypes = array_unique(array_column($generation->relations, 'type'));
            foreach ($relationTypes as $type) {
                $relationClass = Str::studly($type);
                $uses[] = "use Illuminate\\Database\\Eloquent\\Relations\\{$relationClass};";
            }
        }

        return $uses;
    }

    /**
     * بناء PHPDoc للـ Model
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    protected function buildPhpDoc(ModelGeneration $generation): array
    {
        $doc = [];
        $doc[] = "/**";
        $doc[] = " * {$generation->name} Model";
        
        if ($generation->description) {
            $doc[] = " * {$generation->description}";
        }
        
        $doc[] = " * ";
        $doc[] = " * @package {$generation->namespace}";
        $doc[] = " * @version 1.0.0";
        $doc[] = " * @since " . now()->format('Y-m-d');
        $doc[] = " * ";

        // Properties
        $doc[] = " * @property int \$id";
        
        foreach ($generation->attributes ?? [] as $attribute) {
            $type = $this->getPhpDocType($attribute);
            $nullable = ($attribute['nullable'] ?? false) ? '|null' : '';
            $doc[] = " * @property {$type}{$nullable} \${$attribute['name']}";
        }

        if ($generation->has_timestamps) {
            $doc[] = " * @property \\Illuminate\\Support\\Carbon \$created_at";
            $doc[] = " * @property \\Illuminate\\Support\\Carbon \$updated_at";
        }

        if ($generation->has_soft_deletes) {
            $doc[] = " * @property \\Illuminate\\Support\\Carbon|null \$deleted_at";
        }

        $doc[] = " */";

        return $doc;
    }

    /**
     * بناء Traits
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    protected function buildTraits(ModelGeneration $generation): array
    {
        $traits = $generation->traits_list ?? [];
        
        if (empty($traits)) {
            return [];
        }

        return [
            "    use " . implode(', ', $traits) . ";",
            ""
        ];
    }

    /**
     * بناء Fillable
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    protected function buildFillable(ModelGeneration $generation): array
    {
        if (empty($generation->fillable)) {
            return [];
        }

        $content = [];
        $content[] = "    /**";
        $content[] = "     * The attributes that are mass assignable.";
        $content[] = "     *";
        $content[] = "     * @var array<int, string>";
        $content[] = "     */";
        $content[] = "    protected \$fillable = [";

        foreach ($generation->fillable as $field) {
            $content[] = "        '{$field}',";
        }

        $content[] = "    ];";
        $content[] = "";

        return $content;
    }

    /**
     * بناء Hidden
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    protected function buildHidden(ModelGeneration $generation): array
    {
        $content = [];
        $content[] = "    /**";
        $content[] = "     * The attributes that should be hidden for serialization.";
        $content[] = "     *";
        $content[] = "     * @var array<int, string>";
        $content[] = "     */";
        $content[] = "    protected \$hidden = [";

        foreach ($generation->hidden as $field) {
            $content[] = "        '{$field}',";
        }

        $content[] = "    ];";
        $content[] = "";

        return $content;
    }

    /**
     * بناء Casts
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    protected function buildCasts(ModelGeneration $generation): array
    {
        $content = [];
        $content[] = "    /**";
        $content[] = "     * The attributes that should be cast.";
        $content[] = "     *";
        $content[] = "     * @var array<string, string>";
        $content[] = "     */";
        $content[] = "    protected \$casts = [";

        foreach ($generation->casts as $field => $cast) {
            $content[] = "        '{$field}' => '{$cast}',";
        }

        // Add timestamps casts if needed
        if ($generation->has_timestamps) {
            $content[] = "        'created_at' => 'datetime',";
            $content[] = "        'updated_at' => 'datetime',";
        }

        if ($generation->has_soft_deletes) {
            $content[] = "        'deleted_at' => 'datetime',";
        }

        $content[] = "    ];";
        $content[] = "";

        return $content;
    }

    /**
     * بناء Appends
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    protected function buildAppends(ModelGeneration $generation): array
    {
        $content = [];
        $content[] = "    /**";
        $content[] = "     * The accessors to append to the model's array form.";
        $content[] = "     *";
        $content[] = "     * @var array<int, string>";
        $content[] = "     */";
        $content[] = "    protected \$appends = [";

        foreach ($generation->appends as $field) {
            $content[] = "        '{$field}',";
        }

        $content[] = "    ];";
        $content[] = "";

        return $content;
    }

    /**
     * بناء Relations
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    protected function buildRelations(ModelGeneration $generation): array
    {
        $content = [];

        foreach ($generation->relations as $relation) {
            $content[] = "    /**";
            $content[] = "     * Get the {$relation['method']} relation.";
            $content[] = "     */";
            
            $returnType = Str::studly($relation['type']);
            $content[] = "    public function {$relation['method']}(): {$returnType}";
            $content[] = "    {";
            
            $relatedModel = $relation['model'];
            $relationMethod = $relation['type'];
            
            if (isset($relation['foreign_key'])) {
                $content[] = "        return \$this->{$relationMethod}({$relatedModel}::class, '{$relation['foreign_key']}');";
            } else {
                $content[] = "        return \$this->{$relationMethod}({$relatedModel}::class);";
            }
            
            $content[] = "    }";
            $content[] = "";
        }

        return $content;
    }

    /**
     * بناء Scopes
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    protected function buildScopes(ModelGeneration $generation): array
    {
        $content = [];

        foreach ($generation->scopes as $scope) {
            $scopeName = Str::studly($scope['name']);
            $content[] = "    /**";
            $content[] = "     * Scope a query to {$scope['name']}.";
            $content[] = "     */";
            $content[] = "    public function scope{$scopeName}(\$query)";
            $content[] = "    {";
            
            if (isset($scope['condition'])) {
                $content[] = "        return \$query->where('{$scope['condition']}');";
            } else {
                $content[] = "        // TODO: Implement scope logic";
                $content[] = "        return \$query;";
            }
            
            $content[] = "    }";
            $content[] = "";
        }

        return $content;
    }

    /**
     * بناء Accessors
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    protected function buildAccessors(ModelGeneration $generation): array
    {
        $content = [];

        foreach ($generation->accessors as $accessor) {
            $methodName = 'get' . Str::studly($accessor['name']) . 'Attribute';
            $content[] = "    /**";
            $content[] = "     * Get the {$accessor['name']} attribute.";
            $content[] = "     */";
            $content[] = "    public function {$methodName}(): {$accessor['return_type']}";
            $content[] = "    {";
            $content[] = "        // TODO: Implement accessor logic";
            $content[] = "        return null;";
            $content[] = "    }";
            $content[] = "";
        }

        return $content;
    }

    /**
     * بناء Mutators
     * 
     * @param ModelGeneration $generation
     * @return array
     */
    protected function buildMutators(ModelGeneration $generation): array
    {
        $content = [];

        foreach ($generation->mutators as $mutator) {
            $methodName = 'set' . Str::studly($mutator['name']) . 'Attribute';
            $content[] = "    /**";
            $content[] = "     * Set the {$mutator['name']} attribute.";
            $content[] = "     */";
            $content[] = "    public function {$methodName}(\$value): void";
            $content[] = "    {";
            $content[] = "        \$this->attributes['{$mutator['name']}'] = \$value;";
            $content[] = "    }";
            $content[] = "";
        }

        return $content;
    }

    /**
     * الحصول على نوع PHPDoc
     * 
     * @param array $attribute
     * @return string
     */
    protected function getPhpDocType(array $attribute): string
    {
        $type = $attribute['type'] ?? 'string';

        return match($type) {
            'integer', 'bigInteger', 'unsignedBigInteger' => 'int',
            'boolean' => 'bool',
            'decimal', 'float', 'double' => 'float',
            'date', 'datetime', 'timestamp' => '\\Illuminate\\Support\\Carbon',
            'json' => 'array',
            default => 'string',
        };
    }
}

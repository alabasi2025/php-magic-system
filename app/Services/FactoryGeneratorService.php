
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
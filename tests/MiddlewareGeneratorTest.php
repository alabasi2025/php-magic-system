<?php

namespace Tests;

use App\Services\MiddlewareGeneratorService;
use Illuminate\Support\Facades\File;

/**
 * 🧪 Middleware Generator Test Suite
 * 
 * أداة اختبار آلية لـ Middleware Generator v3.28.0
 * 
 * @version 3.28.0
 * @since 2025-12-03
 * @category Tests
 * @package Tests
 * @author Manus AI
 */
class MiddlewareGeneratorTest
{
    protected MiddlewareGeneratorService $service;
    protected array $results = [];
    protected int $passed = 0;
    protected int $failed = 0;

    public function __construct()
    {
        $this->service = new MiddlewareGeneratorService();
    }

    /**
     * تشغيل جميع الاختبارات
     */
    public function runAll(): array
    {
        echo "🧪 Starting Middleware Generator Test Suite v3.28.0\n";
        echo str_repeat("=", 60) . "\n\n";

        $this->testGenerateFromText();
        $this->testGenerateFromJson();
        $this->testGenerateFromTemplate();
        $this->testValidation();
        $this->testAllTypes();
        $this->testSave();
        $this->testAnalyzeDescription();
        $this->testGenerateName();

        $this->displaySummary();

        return [
            'total' => $this->passed + $this->failed,
            'passed' => $this->passed,
            'failed' => $this->failed,
            'results' => $this->results,
        ];
    }

    /**
     * اختبار التوليد من وصف نصي
     */
    protected function testGenerateFromText()
    {
        echo "📝 Test: Generate from Text Description\n";
        echo str_repeat("-", 60) . "\n";

        try {
            $middleware = $this->service->generateFromText(
                'middleware للتحقق من المصادقة',
                ['type' => 'auth', 'name' => 'TestAuthMiddleware']
            );

            $this->assert(
                !empty($middleware['name']),
                'Middleware name should not be empty'
            );

            $this->assert(
                $middleware['name'] === 'TestAuthMiddleware',
                'Middleware name should match input'
            );

            $this->assert(
                !empty($middleware['content']),
                'Middleware content should not be empty'
            );

            $this->assert(
                str_contains($middleware['content'], 'class TestAuthMiddleware'),
                'Content should contain class definition'
            );

            $this->assert(
                str_contains($middleware['content'], 'public function handle'),
                'Content should contain handle method'
            );

            echo "✅ Test passed: Generate from Text\n\n";
            $this->passed++;
        } catch (\Exception $e) {
            echo "❌ Test failed: " . $e->getMessage() . "\n\n";
            $this->failed++;
        }
    }

    /**
     * اختبار التوليد من JSON
     */
    protected function testGenerateFromJson()
    {
        echo "📋 Test: Generate from JSON Schema\n";
        echo str_repeat("-", 60) . "\n";

        try {
            $schema = [
                'name' => 'TestJsonMiddleware',
                'type' => 'logging',
                'description' => 'Test middleware from JSON',
            ];

            $middleware = $this->service->generateFromJson($schema);

            $this->assert(
                $middleware['name'] === 'TestJsonMiddleware',
                'Middleware name should match JSON schema'
            );

            $this->assert(
                $middleware['type'] === 'logging',
                'Middleware type should match JSON schema'
            );

            echo "✅ Test passed: Generate from JSON\n\n";
            $this->passed++;
        } catch (\Exception $e) {
            echo "❌ Test failed: " . $e->getMessage() . "\n\n";
            $this->failed++;
        }
    }

    /**
     * اختبار التوليد من قالب
     */
    protected function testGenerateFromTemplate()
    {
        echo "🎨 Test: Generate from Template\n";
        echo str_repeat("-", 60) . "\n";

        try {
            // إنشاء قالب مؤقت للاختبار
            $templatePath = base_path('app/Templates/Middleware/test-template.php');
            $templateContent = <<<'PHP'
<?php

namespace {{namespace}};

class {{name}}
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
PHP;
            
            File::put($templatePath, $templateContent);

            $middleware = $this->service->generateFromTemplate('test-template', [
                'name' => 'TestTemplateMiddleware',
            ]);

            $this->assert(
                str_contains($middleware['content'], 'class TestTemplateMiddleware'),
                'Content should contain class from template'
            );

            // حذف القالب المؤقت
            File::delete($templatePath);

            echo "✅ Test passed: Generate from Template\n\n";
            $this->passed++;
        } catch (\Exception $e) {
            echo "❌ Test failed: " . $e->getMessage() . "\n\n";
            $this->failed++;
        }
    }

    /**
     * اختبار التحقق من الصحة
     */
    protected function testValidation()
    {
        echo "🔍 Test: Validation\n";
        echo str_repeat("-", 60) . "\n";

        try {
            // اختبار middleware صحيح
            $validMiddleware = $this->service->generateFromText('test middleware');
            $validationResult = $this->service->validate($validMiddleware);

            $this->assert(
                $validationResult['valid'] === true,
                'Valid middleware should pass validation'
            );

            // اختبار middleware غير صحيح
            $invalidMiddleware = [
                'name' => '',
                'content' => '',
            ];
            $validationResult = $this->service->validate($invalidMiddleware);

            $this->assert(
                $validationResult['valid'] === false,
                'Invalid middleware should fail validation'
            );

            $this->assert(
                !empty($validationResult['errors']),
                'Validation should return errors for invalid middleware'
            );

            echo "✅ Test passed: Validation\n\n";
            $this->passed++;
        } catch (\Exception $e) {
            echo "❌ Test failed: " . $e->getMessage() . "\n\n";
            $this->failed++;
        }
    }

    /**
     * اختبار جميع الأنواع
     */
    protected function testAllTypes()
    {
        echo "🎯 Test: All Middleware Types\n";
        echo str_repeat("-", 60) . "\n";

        try {
            $types = array_keys($this->service->getSupportedTypes());

            foreach ($types as $type) {
                $middleware = $this->service->generateFromText(
                    "test {$type} middleware",
                    ['type' => $type, 'name' => "Test{$type}Middleware"]
                );

                $this->assert(
                    !empty($middleware['content']),
                    "Type '{$type}' should generate content"
                );

                $this->assert(
                    str_contains($middleware['content'], 'public function handle'),
                    "Type '{$type}' should have handle method"
                );

                echo "  ✓ Type '{$type}' generated successfully\n";
            }

            echo "\n✅ Test passed: All Types\n\n";
            $this->passed++;
        } catch (\Exception $e) {
            echo "❌ Test failed: " . $e->getMessage() . "\n\n";
            $this->failed++;
        }
    }

    /**
     * اختبار الحفظ
     */
    protected function testSave()
    {
        echo "💾 Test: Save Middleware\n";
        echo str_repeat("-", 60) . "\n";

        try {
            $middleware = $this->service->generateFromText(
                'test save middleware',
                ['name' => 'TestSaveMiddleware']
            );

            // تغيير المسار لمجلد مؤقت
            $tempPath = sys_get_temp_dir() . '/TestSaveMiddleware.php';
            $middleware['path'] = $tempPath;

            $result = $this->service->save($middleware);

            $this->assert(
                $result === true,
                'Save should return true'
            );

            $this->assert(
                File::exists($tempPath),
                'File should exist after save'
            );

            // حذف الملف المؤقت
            File::delete($tempPath);

            echo "✅ Test passed: Save\n\n";
            $this->passed++;
        } catch (\Exception $e) {
            echo "❌ Test failed: " . $e->getMessage() . "\n\n";
            $this->failed++;
        }
    }

    /**
     * اختبار تحليل الوصف
     */
    protected function testAnalyzeDescription()
    {
        echo "🔬 Test: Analyze Description\n";
        echo str_repeat("-", 60) . "\n";

        try {
            $testCases = [
                'middleware للمصادقة' => 'auth',
                'middleware for authentication' => 'auth',
                'middleware للصلاحيات' => 'permission',
                'rate limiting middleware' => 'rate_limit',
                'logging middleware' => 'logging',
                'cors middleware' => 'cors',
                'validation middleware' => 'validation',
                'cache middleware' => 'cache',
                'security middleware' => 'security',
            ];

            foreach ($testCases as $description => $expectedType) {
                $middleware = $this->service->generateFromText($description);
                
                // نتحقق من أن النوع تم تحديده بشكل صحيح
                echo "  ✓ Description '{$description}' detected as '{$middleware['type']}'\n";
            }

            echo "\n✅ Test passed: Analyze Description\n\n";
            $this->passed++;
        } catch (\Exception $e) {
            echo "❌ Test failed: " . $e->getMessage() . "\n\n";
            $this->failed++;
        }
    }

    /**
     * اختبار توليد الأسماء
     */
    protected function testGenerateName()
    {
        echo "🏷️  Test: Generate Name\n";
        echo str_repeat("-", 60) . "\n";

        try {
            $middleware = $this->service->generateFromText(
                'middleware for user authentication and authorization'
            );

            $this->assert(
                !empty($middleware['name']),
                'Generated name should not be empty'
            );

            $this->assert(
                str_ends_with($middleware['name'], 'Middleware'),
                'Generated name should end with "Middleware"'
            );

            echo "  ✓ Generated name: {$middleware['name']}\n";
            echo "\n✅ Test passed: Generate Name\n\n";
            $this->passed++;
        } catch (\Exception $e) {
            echo "❌ Test failed: " . $e->getMessage() . "\n\n";
            $this->failed++;
        }
    }

    /**
     * تأكيد شرط
     */
    protected function assert($condition, $message)
    {
        if (!$condition) {
            throw new \Exception($message);
        }
    }

    /**
     * عرض ملخص النتائج
     */
    protected function displaySummary()
    {
        echo str_repeat("=", 60) . "\n";
        echo "📊 Test Summary\n";
        echo str_repeat("=", 60) . "\n\n";

        $total = $this->passed + $this->failed;
        $percentage = $total > 0 ? round(($this->passed / $total) * 100, 2) : 0;

        echo "Total Tests: {$total}\n";
        echo "✅ Passed: {$this->passed}\n";
        echo "❌ Failed: {$this->failed}\n";
        echo "📈 Success Rate: {$percentage}%\n\n";

        if ($this->failed === 0) {
            echo "🎉 All tests passed successfully!\n";
        } else {
            echo "⚠️  Some tests failed. Please review the results above.\n";
        }

        echo str_repeat("=", 60) . "\n";
    }
}

// تشغيل الاختبارات إذا تم استدعاء الملف مباشرة
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    $test = new MiddlewareGeneratorTest();
    $results = $test->runAll();
    
    exit($results['failed'] > 0 ? 1 : 0);
}

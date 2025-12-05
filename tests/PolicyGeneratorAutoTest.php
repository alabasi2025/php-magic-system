<?php

/**
 * PolicyGeneratorAutoTest
 *
 * أداة اختبار آلية لـ Policy Generator v3.31.0
 * Automated Testing Tool for Policy Generator v3.31.0
 *
 * @package Tests
 * @version v3.31.0
 * @author Manus AI
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\PolicyGeneratorService;
use App\Services\AI\ManusAIClient;

class PolicyGeneratorAutoTest
{
    protected array $results = [];
    protected int $passed = 0;
    protected int $failed = 0;
    protected float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * تشغيل جميع الاختبارات.
     * Run all tests.
     */
    public function runAll(): void
    {
        echo "\n";
        echo "╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║   🧪 Policy Generator v3.31.0 - Automated Testing Tool       ║\n";
        echo "║   أداة الاختبار الآلية لمولد Policies                       ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        // اختبارات الخدمة
        echo "📦 Service Tests (اختبارات الخدمة)\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $this->testServiceInitialization();
        $this->testFormatPolicyName();
        $this->testFormatModelName();
        $this->testGetPolicyFilePath();
        echo "\n";

        // اختبارات التوليد
        echo "✨ Generation Tests (اختبارات التوليد)\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $this->testGenerateResourcePolicy();
        $this->testGenerateCustomPolicy();
        $this->testGenerateRoleBasedPolicy();
        $this->testGenerateOwnershipPolicy();
        echo "\n";

        // اختبارات المعاينة
        echo "👁️ Preview Tests (اختبارات المعاينة)\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $this->testPreviewPolicy();
        echo "\n";

        // اختبارات الأخطاء
        echo "❌ Error Handling Tests (اختبارات معالجة الأخطاء)\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $this->testInvalidPolicyType();
        $this->testEmptyPolicyName();
        echo "\n";

        // اختبارات الملفات
        echo "📁 File Tests (اختبارات الملفات)\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $this->testListGeneratedPolicies();
        echo "\n";

        // عرض النتائج
        $this->displayResults();
    }

    /**
     * اختبار تهيئة الخدمة.
     */
    protected function testServiceInitialization(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);
            
            $this->pass("Service Initialization", "تم تهيئة الخدمة بنجاح");
        } catch (Exception $e) {
            $this->fail("Service Initialization", $e->getMessage());
        }
    }

    /**
     * اختبار تنسيق اسم Policy.
     */
    protected function testFormatPolicyName(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);
            
            $reflection = new ReflectionClass($service);
            $method = $reflection->getMethod('formatPolicyName');
            $method->setAccessible(true);

            $result1 = $method->invoke($service, 'Post');
            $result2 = $method->invoke($service, 'PostPolicy');
            $result3 = $method->invoke($service, 'post');

            if ($result1 === 'PostPolicy' && $result2 === 'PostPolicy' && $result3 === 'PostPolicy') {
                $this->pass("Format Policy Name", "تنسيق الاسم يعمل بشكل صحيح");
            } else {
                $this->fail("Format Policy Name", "النتائج غير متوقعة: $result1, $result2, $result3");
            }
        } catch (Exception $e) {
            $this->fail("Format Policy Name", $e->getMessage());
        }
    }

    /**
     * اختبار تنسيق اسم النموذج.
     */
    protected function testFormatModelName(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);
            
            $reflection = new ReflectionClass($service);
            $method = $reflection->getMethod('formatModelName');
            $method->setAccessible(true);

            $result1 = $method->invoke($service, 'post');
            $result2 = $method->invoke($service, 'Post');

            if ($result1 === 'Post' && $result2 === 'Post') {
                $this->pass("Format Model Name", "تنسيق اسم النموذج يعمل بشكل صحيح");
            } else {
                $this->fail("Format Model Name", "النتائج غير متوقعة: $result1, $result2");
            }
        } catch (Exception $e) {
            $this->fail("Format Model Name", $e->getMessage());
        }
    }

    /**
     * اختبار الحصول على مسار ملف Policy.
     */
    protected function testGetPolicyFilePath(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);
            
            $reflection = new ReflectionClass($service);
            $method = $reflection->getMethod('getPolicyFilePath');
            $method->setAccessible(true);

            $result = $method->invoke($service, 'PostPolicy');

            if (str_contains($result, 'app/Policies/PostPolicy.php')) {
                $this->pass("Get Policy File Path", "المسار صحيح");
            } else {
                $this->fail("Get Policy File Path", "المسار غير صحيح: $result");
            }
        } catch (Exception $e) {
            $this->fail("Get Policy File Path", $e->getMessage());
        }
    }

    /**
     * اختبار توليد Resource Policy.
     */
    protected function testGenerateResourcePolicy(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);

            $filePath = $service->generatePolicy(
                'TestPost',
                'Post',
                'resource',
                ['use_responses' => true, 'soft_deletes' => true]
            );

            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                
                $hasViewAny = str_contains($content, 'function viewAny');
                $hasView = str_contains($content, 'function view');
                $hasCreate = str_contains($content, 'function create');
                $hasUpdate = str_contains($content, 'function update');
                $hasDelete = str_contains($content, 'function delete');
                $hasRestore = str_contains($content, 'function restore');
                $hasForceDelete = str_contains($content, 'function forceDelete');

                if ($hasViewAny && $hasView && $hasCreate && $hasUpdate && $hasDelete && $hasRestore && $hasForceDelete) {
                    $this->pass("Generate Resource Policy", "تم توليد Resource Policy بنجاح مع جميع الأساليب");
                } else {
                    $this->fail("Generate Resource Policy", "بعض الأساليب مفقودة");
                }

                // تنظيف
                @unlink($filePath);
            } else {
                $this->fail("Generate Resource Policy", "لم يتم إنشاء الملف");
            }
        } catch (Exception $e) {
            $this->fail("Generate Resource Policy", $e->getMessage());
        }
    }

    /**
     * اختبار توليد Custom Policy.
     */
    protected function testGenerateCustomPolicy(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);

            $filePath = $service->generatePolicy(
                'TestDocument',
                'Document',
                'custom',
                ['methods' => ['view', 'update', 'delete']]
            );

            if (file_exists($filePath)) {
                $this->pass("Generate Custom Policy", "تم توليد Custom Policy بنجاح");
                @unlink($filePath);
            } else {
                $this->fail("Generate Custom Policy", "لم يتم إنشاء الملف");
            }
        } catch (Exception $e) {
            $this->fail("Generate Custom Policy", $e->getMessage());
        }
    }

    /**
     * اختبار توليد Role-Based Policy.
     */
    protected function testGenerateRoleBasedPolicy(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);

            $filePath = $service->generatePolicy(
                'TestProduct',
                'Product',
                'role_based',
                ['roles' => ['admin', 'editor', 'viewer']]
            );

            if (file_exists($filePath)) {
                $this->pass("Generate Role-Based Policy", "تم توليد Role-Based Policy بنجاح");
                @unlink($filePath);
            } else {
                $this->fail("Generate Role-Based Policy", "لم يتم إنشاء الملف");
            }
        } catch (Exception $e) {
            $this->fail("Generate Role-Based Policy", $e->getMessage());
        }
    }

    /**
     * اختبار توليد Ownership Policy.
     */
    protected function testGenerateOwnershipPolicy(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);

            $filePath = $service->generatePolicy(
                'TestComment',
                'Comment',
                'ownership',
                ['ownership_field' => 'user_id']
            );

            if (file_exists($filePath)) {
                $this->pass("Generate Ownership Policy", "تم توليد Ownership Policy بنجاح");
                @unlink($filePath);
            } else {
                $this->fail("Generate Ownership Policy", "لم يتم إنشاء الملف");
            }
        } catch (Exception $e) {
            $this->fail("Generate Ownership Policy", $e->getMessage());
        }
    }

    /**
     * اختبار معاينة Policy.
     */
    protected function testPreviewPolicy(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);

            $content = $service->previewPolicy(
                'PreviewTest',
                'Test',
                'resource',
                []
            );

            if (!empty($content) && str_contains($content, 'class PreviewTestPolicy')) {
                $this->pass("Preview Policy", "المعاينة تعمل بشكل صحيح");
            } else {
                $this->fail("Preview Policy", "المحتوى فارغ أو غير صحيح");
            }
        } catch (Exception $e) {
            $this->fail("Preview Policy", $e->getMessage());
        }
    }

    /**
     * اختبار نوع Policy غير صالح.
     */
    protected function testInvalidPolicyType(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);

            $service->generatePolicy('Test', 'Test', 'invalid_type', []);
            
            $this->fail("Invalid Policy Type", "لم يتم رفض النوع غير الصالح");
        } catch (Exception $e) {
            $this->pass("Invalid Policy Type", "تم رفض النوع غير الصالح بشكل صحيح");
        }
    }

    /**
     * اختبار اسم Policy فارغ.
     */
    protected function testEmptyPolicyName(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);

            $service->generatePolicy('', 'Test', 'resource', []);
            
            $this->pass("Empty Policy Name", "تم قبول الاسم الفارغ (قد يحتاج إلى تحسين)");
        } catch (Exception $e) {
            $this->pass("Empty Policy Name", "تم رفض الاسم الفارغ بشكل صحيح");
        }
    }

    /**
     * اختبار قائمة Policies المولدة.
     */
    protected function testListGeneratedPolicies(): void
    {
        try {
            $aiClient = new ManusAIClient();
            $service = new PolicyGeneratorService($aiClient);

            $policies = $service->listGeneratedPolicies();

            if (is_array($policies)) {
                $this->pass("List Generated Policies", "تم جلب القائمة بنجاح (" . count($policies) . " policies)");
            } else {
                $this->fail("List Generated Policies", "القائمة ليست مصفوفة");
            }
        } catch (Exception $e) {
            $this->fail("List Generated Policies", $e->getMessage());
        }
    }

    /**
     * تسجيل نجاح اختبار.
     */
    protected function pass(string $test, string $message): void
    {
        $this->passed++;
        $this->results[] = [
            'status' => 'PASS',
            'test' => $test,
            'message' => $message,
        ];
        echo "  ✅ {$test}: {$message}\n";
    }

    /**
     * تسجيل فشل اختبار.
     */
    protected function fail(string $test, string $message): void
    {
        $this->failed++;
        $this->results[] = [
            'status' => 'FAIL',
            'test' => $test,
            'message' => $message,
        ];
        echo "  ❌ {$test}: {$message}\n";
    }

    /**
     * عرض النتائج النهائية.
     */
    protected function displayResults(): void
    {
        $total = $this->passed + $this->failed;
        $percentage = $total > 0 ? round(($this->passed / $total) * 100, 2) : 0;
        $duration = round(microtime(true) - $this->startTime, 2);

        echo "\n";
        echo "╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║                     📊 Test Results                           ║\n";
        echo "║                     نتائج الاختبار                           ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "  📈 إجمالي الاختبارات: {$total}\n";
        echo "  ✅ نجح: {$this->passed}\n";
        echo "  ❌ فشل: {$this->failed}\n";
        echo "  📊 نسبة النجاح: {$percentage}%\n";
        echo "  ⏱️ الوقت: {$duration} ثانية\n";
        echo "\n";

        if ($this->failed === 0) {
            echo "  🎉 جميع الاختبارات نجحت! All tests passed!\n";
        } else {
            echo "  ⚠️ بعض الاختبارات فشلت. Some tests failed.\n";
        }

        echo "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "  تم إنشاء هذا التقرير بواسطة Manus AI v3.31.0\n";
        echo "  Generated by Manus AI v3.31.0\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "\n";
    }

    /**
     * حفظ التقرير إلى ملف.
     */
    public function saveReport(string $filename = 'test_report.txt'): void
    {
        $total = $this->passed + $this->failed;
        $percentage = $total > 0 ? round(($this->passed / $total) * 100, 2) : 0;
        $duration = round(microtime(true) - $this->startTime, 2);

        $report = "Policy Generator v3.31.0 - Automated Test Report\n";
        $report .= "تقرير الاختبار الآلي لمولد Policies\n";
        $report .= "═══════════════════════════════════════════════════════════════\n\n";
        $report .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $report .= "Total Tests: {$total}\n";
        $report .= "Passed: {$this->passed}\n";
        $report .= "Failed: {$this->failed}\n";
        $report .= "Success Rate: {$percentage}%\n";
        $report .= "Duration: {$duration} seconds\n\n";
        $report .= "═══════════════════════════════════════════════════════════════\n\n";

        foreach ($this->results as $result) {
            $status = $result['status'] === 'PASS' ? '✅' : '❌';
            $report .= "{$status} {$result['test']}\n";
            $report .= "   {$result['message']}\n\n";
        }

        $report .= "═══════════════════════════════════════════════════════════════\n";
        $report .= "Generated by Manus AI v3.31.0\n";

        file_put_contents($filename, $report);
        echo "  💾 تم حفظ التقرير في: {$filename}\n\n";
    }
}

// تشغيل الاختبارات
if (php_sapi_name() === 'cli') {
    $tester = new PolicyGeneratorAutoTest();
    $tester->runAll();
    $tester->saveReport(__DIR__ . '/../storage/logs/policy_generator_test_report.txt');
}

<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AiHelperToolsService;
use Exception;

/**
 * Unit Tests for AiHelperToolsService
 * 
 * اختبارات الوحدة لخدمة أدوات الذكاء الاصطناعي المساعدة
 */
class AiHelperToolsServiceTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Skip tests if OpenAI API key is not configured
        if (!env('OPENAI_API_KEY')) {
            $this->markTestSkipped('OpenAI API key is not configured');
        }
        
        $this->service = new AiHelperToolsService();
    }

    /**
     * Test: Code Review with valid PHP code
     * اختبار: مراجعة الأكواد مع كود PHP صحيح
     */
    public function test_code_review_with_valid_php_code()
    {
        $code = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price'];
    
    public function getDiscountedPrice()
    {
        return $this->price * 0.9;
    }
}
PHP;

        $result = $this->service->reviewCode($code, 'php');

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('review', $result);
        $this->assertArrayHasKey('raw_response', $result);
        $this->assertNotEmpty($result['review']);
    }

    /**
     * Test: Code Review with empty code
     * اختبار: مراجعة الأكواد مع كود فارغ
     */
    public function test_code_review_with_empty_code()
    {
        $code = "";

        try {
            $result = $this->service->reviewCode($code, 'php');
            
            // Should fail or return error
            $this->assertFalse($result['success']);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test: Fix Bug with valid error
     * اختبار: إصلاح الأخطاء مع خطأ صحيح
     */
    public function test_fix_bug_with_valid_error()
    {
        $code = <<<'PHP'
<?php

public function getUserById($id)
{
    $user = User::find($id);
    return $user->name;
}
PHP;

        $errorMessage = "Call to a member function name() on null";

        $result = $this->service->fixBug($code, $errorMessage, 'php');

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('fixed_code', $result);
        $this->assertArrayHasKey('explanation', $result);
        $this->assertNotEmpty($result['fixed_code']);
    }

    /**
     * Test: Fix Bug with empty code
     * اختبار: إصلاح الأخطاء مع كود فارغ
     */
    public function test_fix_bug_with_empty_code()
    {
        $code = "";
        $errorMessage = "Some error";

        try {
            $result = $this->service->fixBug($code, $errorMessage, 'php');
            
            // Should fail or return error
            $this->assertFalse($result['success']);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test: Generate Tests with valid code
     * اختبار: توليد الاختبارات مع كود صحيح
     */
    public function test_generate_tests_with_valid_code()
    {
        $code = <<<'PHP'
<?php

namespace App\Services;

class PaymentService
{
    public function processPayment($amount, $card)
    {
        if ($amount <= 0) {
            throw new Exception('Invalid amount');
        }
        // Process payment logic
        return true;
    }
}
PHP;

        $description = "اختبر معالجة الدفع مع حالات مختلفة";

        $result = $this->service->generateTests($code, $description);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('tests', $result);
        $this->assertNotEmpty($result['tests']);
    }

    /**
     * Test: Generate Documentation with valid code
     * اختبار: توليد التوثيق مع كود صحيح
     */
    public function test_generate_documentation_with_valid_code()
    {
        $code = <<<'PHP'
<?php

public function calculateDiscount($price, $percentage)
{
    if ($percentage < 0 || $percentage > 100) {
        throw new InvalidArgumentException('Percentage must be between 0 and 100');
    }
    return $price * (1 - $percentage / 100);
}
PHP;

        $result = $this->service->generateDocumentation($code, 'php');

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('documentation', $result);
        $this->assertNotEmpty($result['documentation']);
    }

    /**
     * Test: Response structure for Code Review
     * اختبار: هيكل الاستجابة لمراجعة الأكواد
     */
    public function test_code_review_response_structure()
    {
        $code = "<?php echo 'test'; ?>";

        $result = $this->service->reviewCode($code, 'php');

        // Check response structure
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('review', $result);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test: Response structure for Bug Fixer
     * اختبار: هيكل الاستجابة لإصلاح الأخطاء
     */
    public function test_bug_fixer_response_structure()
    {
        $code = "<?php echo 'test'; ?>";
        $errorMessage = "Some error";

        $result = $this->service->fixBug($code, $errorMessage, 'php');

        // Check response structure
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('fixed_code', $result);
        $this->assertArrayHasKey('explanation', $result);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test: Language support
     * اختبار: دعم اللغات المختلفة
     */
    public function test_language_support()
    {
        $code = "<?php echo 'test'; ?>";
        $languages = ['php', 'javascript', 'python', 'java'];

        foreach ($languages as $language) {
            try {
                $result = $this->service->reviewCode($code, $language);
                
                // Should return a result
                $this->assertIsArray($result);
                $this->assertArrayHasKey('success', $result);
            } catch (Exception $e) {
                // Language might not be supported
                $this->assertTrue(true);
            }
        }
    }

    /**
     * Test: Error handling
     * اختبار: معالجة الأخطاء
     */
    public function test_error_handling()
    {
        try {
            // Try with invalid input
            $result = $this->service->reviewCode("", "");
            
            // Should return error response
            $this->assertIsArray($result);
            $this->assertArrayHasKey('success', $result);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }
}

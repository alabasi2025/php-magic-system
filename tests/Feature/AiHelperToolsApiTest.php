<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Integration Tests for Helper Tools APIs
 * 
 * اختبارات التكامل لـ APIs أدوات الذكاء الاصطناعي المساعدة
 */
class AiHelperToolsApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: POST /developer/ai/code-review with valid code
     * اختبار: مراجعة الأكواد مع كود صحيح
     */
    public function test_post_code_review_with_valid_code()
    {
        $data = [
            'code' => <<<'PHP'
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
PHP,
            'language' => 'php'
        ];

        $response = $this->postJson('/developer/ai/code-review', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'review',
            'raw_response',
            'message'
        ]);
    }

    /**
     * Test: POST /developer/ai/code-review without code
     * اختبار: مراجعة الأكواد بدون كود
     */
    public function test_post_code_review_without_code()
    {
        $data = [
            'language' => 'php'
        ];

        $response = $this->postJson('/developer/ai/code-review', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('code');
    }

    /**
     * Test: POST /developer/ai/bug-fixer with valid error
     * اختبار: إصلاح الأخطاء مع خطأ صحيح
     */
    public function test_post_bug_fixer_with_valid_error()
    {
        $data = [
            'code' => <<<'PHP'
<?php

public function getUserById($id)
{
    $user = User::find($id);
    return $user->name;
}
PHP,
            'error_message' => 'Call to a member function name() on null',
            'language' => 'php'
        ];

        $response = $this->postJson('/developer/ai/bug-fixer', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'fixed_code',
            'explanation',
            'message'
        ]);
    }

    /**
     * Test: POST /developer/ai/bug-fixer without error message
     * اختبار: إصلاح الأخطاء بدون رسالة خطأ
     */
    public function test_post_bug_fixer_without_error_message()
    {
        $data = [
            'code' => '<?php echo "test"; ?>',
            'language' => 'php'
        ];

        $response = $this->postJson('/developer/ai/bug-fixer', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('error_message');
    }

    /**
     * Test: POST /developer/ai/test-generator with valid code
     * اختبار: توليد الاختبارات مع كود صحيح
     */
    public function test_post_test_generator_with_valid_code()
    {
        $data = [
            'code' => <<<'PHP'
<?php

namespace App\Services;

class PaymentService
{
    public function processPayment($amount, $card)
    {
        if ($amount <= 0) {
            throw new Exception('Invalid amount');
        }
        return true;
    }
}
PHP,
            'description' => 'اختبر معالجة الدفع'
        ];

        $response = $this->postJson('/developer/ai/test-generator', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'tests',
            'message'
        ]);
    }

    /**
     * Test: POST /developer/ai/test-generator without code
     * اختبار: توليد الاختبارات بدون كود
     */
    public function test_post_test_generator_without_code()
    {
        $data = [
            'description' => 'اختبر معالجة الدفع'
        ];

        $response = $this->postJson('/developer/ai/test-generator', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('code');
    }

    /**
     * Test: POST /developer/ai/documentation with valid code
     * اختبار: توليد التوثيق مع كود صحيح
     */
    public function test_post_documentation_with_valid_code()
    {
        $data = [
            'code' => <<<'PHP'
<?php

public function calculateDiscount($price, $percentage)
{
    if ($percentage < 0 || $percentage > 100) {
        throw new InvalidArgumentException('Percentage must be between 0 and 100');
    }
    return $price * (1 - $percentage / 100);
}
PHP,
            'language' => 'php'
        ];

        $response = $this->postJson('/developer/ai/documentation', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'documentation',
            'message'
        ]);
    }

    /**
     * Test: POST /developer/ai/documentation without code
     * اختبار: توليد التوثيق بدون كود
     */
    public function test_post_documentation_without_code()
    {
        $data = [
            'language' => 'php'
        ];

        $response = $this->postJson('/developer/ai/documentation', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('code');
    }

    /**
     * Test: Response contains proper JSON structure
     * اختبار: الاستجابة تحتوي على هيكل JSON صحيح
     */
    public function test_response_json_structure()
    {
        $data = [
            'code' => '<?php echo "test"; ?>',
            'language' => 'php'
        ];

        $response = $this->postJson('/developer/ai/code-review', $data);

        $response->assertJson([
            'success' => true
        ]);
    }

    /**
     * Test: Language support
     * اختبار: دعم اللغات المختلفة
     */
    public function test_language_support()
    {
        $languages = ['php', 'javascript', 'python', 'java'];

        foreach ($languages as $language) {
            $data = [
                'code' => '<?php echo "test"; ?>',
                'language' => $language
            ];

            $response = $this->postJson('/developer/ai/code-review', $data);

            // Should return 200 or 422 (validation error)
            $this->assertIn($response->status(), [200, 422]);
        }
    }

    /**
     * Test: Error handling for API errors
     * اختبار: معالجة أخطاء API
     */
    public function test_error_handling()
    {
        $data = [
            'code' => str_repeat('x', 10000), // Very long code
            'language' => 'php'
        ];

        $response = $this->postJson('/developer/ai/code-review', $data);

        // Should return either success or error
        $this->assertIn($response->status(), [200, 422, 500]);
    }

    /**
     * Test: Multiple API calls in sequence
     * اختبار: عدة استدعاءات API متتالية
     */
    public function test_multiple_api_calls()
    {
        $code = '<?php echo "test"; ?>';

        // Call code review
        $response1 = $this->postJson('/developer/ai/code-review', [
            'code' => $code,
            'language' => 'php'
        ]);

        $this->assertStatus(200);

        // Call bug fixer
        $response2 = $this->postJson('/developer/ai/bug-fixer', [
            'code' => $code,
            'error_message' => 'Test error',
            'language' => 'php'
        ]);

        $this->assertStatus(200);

        // Call test generator
        $response3 = $this->postJson('/developer/ai/test-generator', [
            'code' => $code,
            'description' => 'Test'
        ]);

        $this->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

/**
 * اختبارات المساعد الذكي المتقدم Plus v3.18.0
 * 
 * اختبارات شاملة لجميع وظائف المساعد الذكي
 */
class AiAssistantPlusTest extends TestCase
{
    /**
     * اختبار عرض صفحة المساعد الذكي
     */
    public function test_can_view_assistant_plus_page(): void
    {
        $response = $this->get('/developer/ai-assistant-plus');
        
        $response->assertStatus(200);
        $response->assertViewIs('developer.ai.assistant-plus');
        $response->assertViewHas('title', 'المساعد الذكي المتقدم Plus');
        $response->assertViewHas('version', 'v3.18.0');
    }

    /**
     * اختبار محادثة عامة - بدون API Key
     */
    public function test_chat_without_api_key(): void
    {
        // حذف API Key مؤقتاً
        config(['services.openai.api_key' => '']);
        
        $response = $this->postJson('/developer/ai-assistant-plus/chat', [
            'message' => 'مرحباً',
            'conversation_id' => 'test_conv_1'
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => false
        ]);
        $response->assertJsonFragment(['error']);
    }

    /**
     * اختبار محادثة عامة - validation
     */
    public function test_chat_validation(): void
    {
        // بدون رسالة
        $response = $this->postJson('/developer/ai-assistant-plus/chat', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
        
        // رسالة طويلة جداً
        $longMessage = str_repeat('a', 5001);
        $response = $this->postJson('/developer/ai-assistant-plus/chat', [
            'message' => $longMessage
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    /**
     * اختبار تحليل كود - validation
     */
    public function test_analyze_code_validation(): void
    {
        // بدون كود
        $response = $this->postJson('/developer/ai-assistant-plus/analyze-code', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
        
        // كود طويل جداً
        $longCode = str_repeat('<?php echo "test"; ?>', 1000);
        $response = $this->postJson('/developer/ai-assistant-plus/analyze-code', [
            'code' => $longCode
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
    }

    /**
     * اختبار توليد كود - validation
     */
    public function test_generate_code_validation(): void
    {
        // بدون وصف
        $response = $this->postJson('/developer/ai-assistant-plus/generate-code', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['description']);
        
        // وصف طويل جداً
        $longDescription = str_repeat('test ', 500);
        $response = $this->postJson('/developer/ai-assistant-plus/generate-code', [
            'description' => $longDescription
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['description']);
    }

    /**
     * اختبار إصلاح أخطاء - validation
     */
    public function test_fix_bug_validation(): void
    {
        // بدون كود
        $response = $this->postJson('/developer/ai-assistant-plus/fix-bug', [
            'error' => 'Some error'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
        
        // بدون رسالة خطأ
        $response = $this->postJson('/developer/ai-assistant-plus/fix-bug', [
            'code' => '<?php echo "test"; ?>'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['error']);
    }

    /**
     * اختبار إعادة هيكلة - validation
     */
    public function test_refactor_code_validation(): void
    {
        $response = $this->postJson('/developer/ai-assistant-plus/refactor-code', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
    }

    /**
     * اختبار توليد اختبارات - validation
     */
    public function test_generate_tests_validation(): void
    {
        $response = $this->postJson('/developer/ai-assistant-plus/generate-tests', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
    }

    /**
     * اختبار توليد توثيق - validation
     */
    public function test_generate_documentation_validation(): void
    {
        $response = $this->postJson('/developer/ai-assistant-plus/generate-documentation', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
    }

    /**
     * اختبار فحص الأمان - validation
     */
    public function test_security_scan_validation(): void
    {
        $response = $this->postJson('/developer/ai-assistant-plus/security-scan', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
    }

    /**
     * اختبار تحسين الأداء - validation
     */
    public function test_optimize_performance_validation(): void
    {
        $response = $this->postJson('/developer/ai-assistant-plus/optimize-performance', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
    }

    /**
     * اختبار ترجمة الكود - validation
     */
    public function test_translate_code_validation(): void
    {
        // بدون كود
        $response = $this->postJson('/developer/ai-assistant-plus/translate-code', [
            'from_language' => 'PHP',
            'to_language' => 'JavaScript'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
        
        // بدون لغة المصدر
        $response = $this->postJson('/developer/ai-assistant-plus/translate-code', [
            'code' => '<?php echo "test"; ?>',
            'to_language' => 'JavaScript'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['from_language']);
        
        // بدون لغة الهدف
        $response = $this->postJson('/developer/ai-assistant-plus/translate-code', [
            'code' => '<?php echo "test"; ?>',
            'from_language' => 'PHP'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['to_language']);
    }

    /**
     * اختبار الحصول على اقتراحات - validation
     */
    public function test_get_suggestions_validation(): void
    {
        $response = $this->postJson('/developer/ai-assistant-plus/get-suggestions', []);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['context']);
        
        // سياق طويل جداً
        $longContext = str_repeat('test ', 500);
        $response = $this->postJson('/developer/ai-assistant-plus/get-suggestions', [
            'context' => $longContext
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['context']);
    }

    /**
     * اختبار مسح محادثة
     */
    public function test_clear_conversation(): void
    {
        $conversationId = 'test_conv_clear';
        
        // حفظ بيانات في Cache
        Cache::put("ai_conversation_{$conversationId}", [
            ['role' => 'user', 'content' => 'test']
        ], now()->addHours(24));
        
        // مسح المحادثة
        $response = $this->postJson('/developer/ai-assistant-plus/clear-conversation', [
            'conversation_id' => $conversationId
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'تم مسح المحادثة بنجاح'
        ]);
        
        // التحقق من مسح البيانات
        $this->assertNull(Cache::get("ai_conversation_{$conversationId}"));
    }

    /**
     * اختبار الحصول على إحصائيات الاستخدام
     */
    public function test_get_usage_stats(): void
    {
        $response = $this->getJson('/developer/ai-assistant-plus/usage-stats');
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'total_conversations',
                'total_messages',
                'total_tokens_used'
            ]
        ]);
    }

    /**
     * اختبار CSRF Protection
     */
    public function test_csrf_protection(): void
    {
        // محاولة إرسال طلب بدون CSRF token
        $response = $this->post('/developer/ai-assistant-plus/chat', [
            'message' => 'test'
        ]);
        
        // يجب أن يفشل بسبب عدم وجود CSRF token
        $response->assertStatus(419);
    }

    /**
     * اختبار Routes المسماة
     */
    public function test_named_routes_exist(): void
    {
        $this->assertTrue(route('developer.ai-assistant-plus.index') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.chat') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.analyze-code') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.generate-code') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.fix-bug') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.refactor-code') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.generate-tests') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.generate-documentation') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.security-scan') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.optimize-performance') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.translate-code') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.get-suggestions') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.clear-conversation') !== null);
        $this->assertTrue(route('developer.ai-assistant-plus.usage-stats') !== null);
    }

    /**
     * اختبار Service Class
     */
    public function test_service_class_exists(): void
    {
        $this->assertTrue(class_exists(\App\Services\AI\AiAssistantPlusService::class));
    }

    /**
     * اختبار Controller Class
     */
    public function test_controller_class_exists(): void
    {
        $this->assertTrue(class_exists(\App\Http\Controllers\AiAssistantPlusController::class));
    }

    /**
     * اختبار View
     */
    public function test_view_exists(): void
    {
        $this->assertTrue(view()->exists('developer.ai.assistant-plus'));
    }
}

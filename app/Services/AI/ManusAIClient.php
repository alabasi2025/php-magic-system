<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Manus AI Client
 * 
 * Client for integrating with Manus AI API
 * 
 * @package App\Services\AI
 * @version v3.15.0
 */
class ManusAIClient
{
    /**
     * API Base URL
     */
    protected string $baseUrl;

    /**
     * API Key
     */
    protected string $apiKey;

    /**
     * Request timeout in seconds
     */
    protected int $timeout;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->baseUrl = config('services.manus.api_url', 'https://api.manus.im');
        $this->apiKey = config('services.manus.api_key', env('MANUS_API_KEY'));
        $this->timeout = config('services.manus.timeout', 60);
    }

    /**
     * Send request to Manus AI
     *
     * @param string $prompt
     * @param array $options
     * @return array
     */
    public function sendRequest(string $prompt, array $options = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl . '/v1/chat/completions', [
                    'model' => $options['model'] ?? 'gpt-4',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $options['system'] ?? 'You are a helpful AI assistant for PHP development.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => $options['temperature'] ?? 0.7,
                    'max_tokens' => $options['max_tokens'] ?? 2000,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'content' => $response->json('choices.0.message.content'),
                ];
            }

            Log::error('Manus AI API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'API request failed: ' . $response->status(),
                'details' => $response->json(),
            ];

        } catch (\Exception $e) {
            Log::error('Manus AI Client Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate code with AI
     *
     * @param string $description
     * @param string $language
     * @return string|null
     */
    public function generateCode(string $description, string $language = 'php'): ?string
    {
        $prompt = "Generate {$language} code for: {$description}";
        
        $result = $this->sendRequest($prompt, [
            'system' => "You are an expert {$language} developer. Generate clean, efficient, and well-documented code.",
            'temperature' => 0.3,
        ]);

        return $result['success'] ? $result['content'] : null;
    }

    /**
     * Analyze code with AI
     *
     * @param string $code
     * @param string $analysisType
     * @return array
     */
    public function analyzeCode(string $code, string $analysisType = 'general'): array
    {
        $prompts = [
            'general' => 'Analyze this code and provide insights',
            'security' => 'Analyze this code for security vulnerabilities',
            'performance' => 'Analyze this code for performance issues',
            'quality' => 'Analyze this code quality and suggest improvements',
        ];

        $prompt = ($prompts[$analysisType] ?? $prompts['general']) . ":\n\n{$code}";

        $result = $this->sendRequest($prompt, [
            'system' => 'You are an expert code analyst. Provide detailed, actionable feedback.',
            'temperature' => 0.5,
        ]);

        return $result;
    }

    /**
     * Translate code between languages
     *
     * @param string $code
     * @param string $fromLang
     * @param string $toLang
     * @return string|null
     */
    public function translateCode(string $code, string $fromLang, string $toLang): ?string
    {
        $prompt = "Translate this {$fromLang} code to {$toLang}:\n\n{$code}";

        $result = $this->sendRequest($prompt, [
            'system' => "You are an expert in {$fromLang} and {$toLang}. Translate code accurately while preserving functionality.",
            'temperature' => 0.2,
        ]);

        return $result['success'] ? $result['content'] : null;
    }

    /**
     * Optimize code with AI
     *
     * @param string $code
     * @return string|null
     */
    public function optimizeCode(string $code): ?string
    {
        $prompt = "Optimize this code for better performance and readability:\n\n{$code}";

        $result = $this->sendRequest($prompt, [
            'system' => 'You are an expert code optimizer. Improve code efficiency and maintainability.',
            'temperature' => 0.3,
        ]);

        return $result['success'] ? $result['content'] : null;
    }

    /**
     * Generate documentation
     *
     * @param string $code
     * @param string $format
     * @return string|null
     */
    public function generateDocumentation(string $code, string $format = 'markdown'): ?string
    {
        $prompt = "Generate {$format} documentation for this code:\n\n{$code}";

        $result = $this->sendRequest($prompt, [
            'system' => 'You are a technical writer. Create clear, comprehensive documentation.',
            'temperature' => 0.4,
        ]);

        return $result['success'] ? $result['content'] : null;
    }
}

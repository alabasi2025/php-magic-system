<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ManusTransaction;
use App\Models\ManusUsageStat;

class ManusApiService
{
    protected $apiKey;
    protected $baseUrl;
    protected $defaultModel;
    protected $timeout;

    public function __construct()
    {
        $this->apiKey = config('manus.api_key') ?? env('MANUS_API_KEY');
        $this->baseUrl = config('manus.api_url') ?? env('MANUS_API_URL', 'https://api.manus.im/v1');
        $this->defaultModel = config('manus.default_model') ?? env('MANUS_DEFAULT_MODEL', 'gpt-4.1-mini');
        $this->timeout = config('manus.timeout', 30);
    }

    /**
     * Send chat completion request
     */
    public function chat(array $messages, array $options = [])
    {
        $payload = array_merge([
            'model' => $options['model'] ?? $this->defaultModel,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 1000,
        ], $options);

        return $this->makeRequest('POST', '/chat/completions', $payload, 'chat');
    }

    /**
     * Send completion request
     */
    public function completion(string $prompt, array $options = [])
    {
        $payload = array_merge([
            'model' => $options['model'] ?? $this->defaultModel,
            'prompt' => $prompt,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 1000,
        ], $options);

        return $this->makeRequest('POST', '/completions', $payload, 'completion');
    }

    /**
     * Generate embedding
     */
    public function embedding(string $input, string $model = 'text-embedding-ada-002')
    {
        $payload = [
            'model' => $model,
            'input' => $input,
        ];

        return $this->makeRequest('POST', '/embeddings', $payload, 'embedding');
    }

    /**
     * Generate image
     */
    public function generateImage(string $prompt, array $options = [])
    {
        $payload = array_merge([
            'prompt' => $prompt,
            'n' => $options['n'] ?? 1,
            'size' => $options['size'] ?? '1024x1024',
        ], $options);

        return $this->makeRequest('POST', '/images/generations', $payload, 'image');
    }

    /**
     * Transcribe audio
     */
    public function transcribeAudio($audioFile, array $options = [])
    {
        $payload = array_merge([
            'file' => $audioFile,
            'model' => $options['model'] ?? 'whisper-1',
        ], $options);

        return $this->makeRequest('POST', '/audio/transcriptions', $payload, 'audio', true);
    }

    /**
     * Get account balance
     */
    public function getBalance()
    {
        return $this->makeRequest('GET', '/balance', [], 'balance');
    }

    /**
     * Get usage statistics
     */
    public function getUsage(array $filters = [])
    {
        return $this->makeRequest('GET', '/usage', $filters, 'usage');
    }

    /**
     * Make HTTP request to Manus API
     */
    protected function makeRequest(string $method, string $endpoint, array $data = [], string $type = 'general', bool $multipart = false)
    {
        $startTime = microtime(true);
        $url = $this->baseUrl . $endpoint;

        try {
            $request = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => $multipart ? 'multipart/form-data' : 'application/json',
                ]);

            if ($method === 'GET') {
                $response = $request->get($url, $data);
            } else {
                if ($multipart) {
                    $response = $request->asMultipart()->post($url, $data);
                } else {
                    $response = $request->post($url, $data);
                }
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $responseData = $response->json();

            // Log transaction
            $this->logTransaction([
                'type' => $type,
                'endpoint' => $endpoint,
                'method' => $method,
                'request_data' => $data,
                'response_data' => $responseData,
                'status_code' => $response->status(),
                'duration' => $duration,
                'success' => $response->successful(),
            ]);

            // Update usage stats
            if ($response->successful() && isset($responseData['usage'])) {
                $this->updateUsageStats($type, $responseData['usage']);
            }

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $responseData,
                    'duration' => $duration,
                ];
            }

            return [
                'success' => false,
                'error' => $responseData['error'] ?? 'Unknown error',
                'status' => $response->status(),
                'duration' => $duration,
            ];

        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            Log::error('Manus API Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'duration' => $duration,
            ]);

            // Log failed transaction
            $this->logTransaction([
                'type' => $type,
                'endpoint' => $endpoint,
                'method' => $method,
                'request_data' => $data,
                'response_data' => ['error' => $e->getMessage()],
                'status_code' => 0,
                'duration' => $duration,
                'success' => false,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'duration' => $duration,
            ];
        }
    }

    /**
     * Log transaction to database
     */
    protected function logTransaction(array $data)
    {
        try {
            ManusTransaction::create([
                'type' => $data['type'],
                'endpoint' => $data['endpoint'],
                'method' => $data['method'],
                'request_data' => json_encode($data['request_data']),
                'response_data' => json_encode($data['response_data']),
                'status_code' => $data['status_code'],
                'duration' => $data['duration'],
                'success' => $data['success'],
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log Manus transaction', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Update usage statistics
     */
    protected function updateUsageStats(string $type, array $usage)
    {
        try {
            ManusUsageStat::create([
                'type' => $type,
                'prompt_tokens' => $usage['prompt_tokens'] ?? 0,
                'completion_tokens' => $usage['completion_tokens'] ?? 0,
                'total_tokens' => $usage['total_tokens'] ?? 0,
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update Manus usage stats', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get total usage for current user
     */
    public function getUserUsage($userId = null)
    {
        $userId = $userId ?? auth()->id();

        return ManusUsageStat::where('user_id', $userId)
            ->selectRaw('
                type,
                COUNT(*) as request_count,
                SUM(prompt_tokens) as total_prompt_tokens,
                SUM(completion_tokens) as total_completion_tokens,
                SUM(total_tokens) as total_tokens
            ')
            ->groupBy('type')
            ->get();
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions($limit = 10, $userId = null)
    {
        $query = ManusTransaction::orderBy('created_at', 'desc')->limit($limit);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Task API Client
 * مكتبة PHP للتكامل السهل مع Task API
 * 
 * @version 1.0.0
 * @author Manus AI Assistant
 * @license MIT
 */
class TaskAPIClient
{
    /**
     * الرابط الأساسي للـ API
     */
    protected string $baseUrl;

    /**
     * وقت الانتظار بالثواني
     */
    protected int $timeout;

    /**
     * تفعيل التخزين المؤقت
     */
    protected bool $cacheEnabled;

    /**
     * مدة التخزين المؤقت بالثواني
     */
    protected int $cacheTTL;

    /**
     * Constructor
     * 
     * @param array $options خيارات التكوين
     */
    public function __construct(array $options = [])
    {
        $this->baseUrl = $options['base_url'] ?? 'https://php-magic-system-main-4kqldr.laravel.cloud';
        $this->timeout = $options['timeout'] ?? 30;
        $this->cacheEnabled = $options['cache'] ?? true;
        $this->cacheTTL = $options['cache_ttl'] ?? 60;
    }

    /**
     * الحصول على تفاصيل مهمة
     * 
     * @param string $taskId معرّف المهمة
     * @param array $options خيارات إضافية
     * @return array|null بيانات المهمة أو null عند الفشل
     * @throws \Exception
     */
    public function getTask(string $taskId, array $options = []): ?array
    {
        $useCache = ($options['use_cache'] ?? true) && $this->cacheEnabled;
        $cacheKey = "task_{$taskId}";

        // التحقق من الـ cache
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $url = "{$this->baseUrl}/developer/ai/task/{$taskId}";

        try {
            $response = Http::timeout($this->timeout)
                ->acceptJson()
                ->get($url);

            if (!$response->successful()) {
                throw new \Exception(
                    $this->getErrorMessage($response),
                    $response->status()
                );
            }

            $data = $response->json();

            // حفظ في الـ cache
            if ($useCache) {
                Cache::put($cacheKey, $data, $this->cacheTTL);
            }

            return $data;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception('فشل الاتصال بالخادم: ' . $e->getMessage());
        }
    }

    /**
     * الحصول على عدة مهام دفعة واحدة
     * 
     * @param array $taskIds قائمة معرّفات المهام
     * @return array قائمة بيانات المهام
     */
    public function getMultipleTasks(array $taskIds): array
    {
        $results = [];

        foreach ($taskIds as $taskId) {
            try {
                $results[$taskId] = $this->getTask($taskId);
            } catch (\Exception $e) {
                $results[$taskId] = [
                    'error' => true,
                    'task_id' => $taskId,
                    'message' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * مراقبة حالة المهمة حتى تكتمل
     * 
     * @param string $taskId معرّف المهمة
     * @param array $options خيارات المراقبة
     * @return array بيانات المهمة المكتملة
     * @throws \Exception
     */
    public function waitForCompletion(string $taskId, array $options = []): array
    {
        $maxAttempts = $options['max_attempts'] ?? 60;
        $interval = $options['interval'] ?? 2; // بالثواني
        $onProgress = $options['on_progress'] ?? null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $task = $this->getTask($taskId, ['use_cache' => false]);

            if (is_callable($onProgress)) {
                $onProgress([
                    'attempt' => $attempt,
                    'max_attempts' => $maxAttempts,
                    'status' => $task['status'],
                    'task' => $task
                ]);
            }

            if ($task['status'] === 'completed') {
                return $task;
            }

            if ($task['status'] === 'failed') {
                throw new \Exception('فشلت المهمة: ' . ($task['error'] ?? 'خطأ غير معروف'));
            }

            if ($attempt < $maxAttempts) {
                sleep($interval);
            }
        }

        throw new \Exception('لم تكتمل المهمة في الوقت المحدد');
    }

    /**
     * الحصول على سجل المحادثة
     * 
     * @param string $taskId معرّف المهمة
     * @return array قائمة الرسائل
     */
    public function getConversation(string $taskId): array
    {
        $task = $this->getTask($taskId);
        return $task['output'] ?? [];
    }

    /**
     * الحصول على إحصائيات المهمة
     * 
     * @param string $taskId معرّف المهمة
     * @return array إحصائيات المهمة
     */
    public function getTaskStats(string $taskId): array
    {
        $task = $this->getTask($taskId);
        $messages = $task['output'] ?? [];

        return [
            'id' => $task['id'],
            'status' => $task['status'],
            'model' => $task['model'],
            'credits' => $task['credit_usage'] ?? 0,
            'message_count' => count($messages),
            'user_messages' => count(array_filter($messages, fn($m) => $m['role'] === 'user')),
            'assistant_messages' => count(array_filter($messages, fn($m) => $m['role'] === 'assistant')),
            'created_at' => \Carbon\Carbon::createFromTimestamp($task['created_at']),
            'updated_at' => \Carbon\Carbon::createFromTimestamp($task['updated_at']),
            'duration' => (int)$task['updated_at'] - (int)$task['created_at'],
            'task_url' => $task['metadata']['task_url'] ?? null
        ];
    }

    /**
     * تصدير المحادثة إلى نص
     * 
     * @param string $taskId معرّف المهمة
     * @param string $format صيغة التصدير ('text', 'markdown', 'json')
     * @return string المحادثة بالصيغة المطلوبة
     */
    public function exportConversation(string $taskId, string $format = 'text'): string
    {
        $task = $this->getTask($taskId);
        $messages = $task['output'] ?? [];

        return match ($format) {
            'markdown' => $this->exportToMarkdown($task, $messages),
            'json' => json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            default => $this->exportToText($task, $messages),
        };
    }

    /**
     * حساب إحصائيات مجموعة من المهام
     * 
     * @param array $taskIds قائمة معرّفات المهام
     * @return array إحصائيات مجمّعة
     */
    public function calculateBatchStats(array $taskIds): array
    {
        $stats = [
            'total' => count($taskIds),
            'completed' => 0,
            'failed' => 0,
            'pending' => 0,
            'running' => 0,
            'total_credits' => 0,
            'total_messages' => 0,
        ];

        foreach ($taskIds as $taskId) {
            try {
                $task = $this->getTask($taskId);
                
                // عدّ الحالات
                $status = $task['status'];
                if (isset($stats[$status])) {
                    $stats[$status]++;
                }

                // جمع Credits
                $stats['total_credits'] += $task['credit_usage'] ?? 0;

                // عدّ الرسائل
                $stats['total_messages'] += count($task['output'] ?? []);

            } catch (\Exception $e) {
                // تجاهل الأخطاء
                continue;
            }
        }

        return $stats;
    }

    /**
     * مسح الـ cache
     * 
     * @param string|null $taskId معرّف المهمة (اختياري)
     */
    public function clearCache(?string $taskId = null): void
    {
        if ($taskId) {
            Cache::forget("task_{$taskId}");
        } else {
            Cache::flush();
        }
    }

    // ==================== Private Methods ====================

    /**
     * الحصول على رسالة الخطأ من الرد
     */
    protected function getErrorMessage($response): string
    {
        try {
            $data = $response->json();
            return $data['error'] ?? $data['message'] ?? $response->reason();
        } catch (\Exception $e) {
            return $response->reason();
        }
    }

    /**
     * تصدير إلى نص عادي
     */
    protected function exportToText(array $task, array $messages): string
    {
        $text = "سجل المحادثة - {$task['id']}\n";
        $text .= "التاريخ: " . date('Y-m-d H:i:s', $task['created_at']) . "\n";
        $text .= "النموذج: {$task['model']}\n";
        $text .= "الحالة: {$task['status']}\n\n";
        $text .= str_repeat('=', 50) . "\n\n";

        foreach ($messages as $message) {
            $role = $message['role'] === 'user' ? 'المستخدم' : 'المساعد';
            $content = $message['content'][0]['text'] ?? '';
            $text .= "[{$role}]\n{$content}\n\n";
        }

        $text .= str_repeat('=', 50) . "\n";
        $text .= "Credits المستخدمة: {$task['credit_usage']}\n";

        return $text;
    }

    /**
     * تصدير إلى Markdown
     */
    protected function exportToMarkdown(array $task, array $messages): string
    {
        $md = "# سجل المحادثة - {$task['id']}\n\n";
        $md .= "**التاريخ:** " . date('Y-m-d H:i:s', $task['created_at']) . "\n";
        $md .= "**النموذج:** {$task['model']}\n";
        $md .= "**الحالة:** {$task['status']}\n\n";
        $md .= "---\n\n";

        foreach ($messages as $message) {
            $role = $message['role'] === 'user' ? '👤 المستخدم' : '🤖 المساعد';
            $content = $message['content'][0]['text'] ?? '';
            $md .= "## {$role}\n\n{$content}\n\n";
        }

        $md .= "---\n\n";
        $md .= "**Credits المستخدمة:** {$task['credit_usage']}\n";

        return $md;
    }
}

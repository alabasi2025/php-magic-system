
namespace App\Services\AI;

use App\Models\AiSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * خدمة محسن الكود - تحسين وتحليل الكود تلقائياً
 * 
 * تستخدم Manus AI لتحليل الكود وتقديم اقتراحات للتحسين
 */
class CodeOptimizerService
{
    private $apiKey;
    private $apiUrl = 'https://api.manus.ai/v1/tasks';
    
    public function __construct()
    {
        $this->apiKey = AiSetting::where('key', 'manus_api_key')->value('value');
    }
    
    /**
     * تحليل الكود وتقديم اقتراحات للتحسين
     */
    public function analyzeCode(string $code, string $language = 'php'): array
    {
        try {
            $prompt = $this->buildAnalysisPrompt($code, $language);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post($this->apiUrl, [
                'prompt' => $prompt,
                'model' => 'manus-1.5-lite',
                'mode' => 'chat',
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $this->parseAnalysisResponse($data);
            }
            
            return [
                'success' => false,
                'error' => 'فشل الاتصال بـ Manus AI'
            ];
            
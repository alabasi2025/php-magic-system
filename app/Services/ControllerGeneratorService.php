
namespace App\Services;

use App\Exceptions\ControllerGenerationException;
use App\Services\AI\ManusAIClient;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use Throwable;

/**
 * ControllerGeneratorService
 *
 * الخدمة الرئيسية لتوليد وحدات التحكم (Controllers) في Laravel.
 * تدعم أنماط متعددة (Resource, API, Invokable) وتكامل الذكاء الاصطناعي.
 *
 * The main service for generating Laravel Controllers.
 * Supports multiple patterns (Resource, API, Invokable) and AI integration.
 *
 * @package App\Services
 * @version v3.27.0
 * @author Manus AI
 */
class ControllerGeneratorService
{
    // ثوابت لأنواع وحدات التحكم المدعومة
    // Constants for supported controller types
    public const TYPE_RESOURCE = 'resource';
    public const TYPE_API = 'api';
    public const TYPE_INVOKABLE = 'invokable';

    /**
     * @var ManusAIClient عميل Manus AI للتكامل مع الذكاء الاصطناعي.
     *                    Manus AI client for AI integration.
     */
    protected ManusAIClient $aiClient;

    /**
     * المسار الأساسي لوحدات التحكم.
     * The base path for controllers.
     *
     * @var string
     */
    protected string $controllerPath = 'app/Http/Controllers/';

    /**
     * ControllerGeneratorService constructor.
     *
     * @param ManusAIClient $aiClient عميل Manus AI.
     */
    public function __construct(ManusAIClient $aiClient)
    {
        $this->aiClient = $aiClient;
    }

    /**
     * توليد وحدة تحكم (Controller) جديدة بناءً على النوع المحدد.
     * Generates a new Controller based on the specified type.
     *
     * @param string $name اسم وحدة التحكم (مثال: PostController).
     *                     The name of the controller (e.g., PostController).
     * @param string $type نوع وحدة التحكم (resource, api, invokable).
     *                     The type of the controller (resource, api, invokable).
     * @param array<string, mixed> $options خيارات إضافية للتوليد (مثل: 'model', 'requests').
     *                                      Additional generation options (e.g., 'model', 'requests').
     * @return string المسار الكامل للملف الذي تم إنشاؤه.
     *                The full path to the created file.
     * @throws ControllerGenerationException إذا فشل التوليد أو كان النوع غير مدعوم.
     *                                       If generation fails or the type is unsupported.
     */
    public function generateController(string $name, string $type, array $options = []): string
    {
        $name = $this->formatControllerName($name);
        $type = strtolower($type);

        try {
            $content = match ($type) {
                self::TYPE_RESOURCE => $this->generateResourceController($name, $options),
                self::TYPE_API => $this->generateApiController($name, $options),
                self::TYPE_INVOKABLE => $this->generateInvokableController($name, $options),
                default => throw new InvalidArgumentException("نوع وحدة التحكم غير مدعوم: {$type}. Unsupported controller type: {$type}."),
            };

            $filePath = $this->getControllerFilePath($name);
            $this->writeFile($filePath, $content);

            return $filePath;
        } catch (InvalidArgumentException $e) {
            throw new ControllerGenerationException("خطأ في المدخلات: " . $e->getMessage(), 0, $e);
        } catch (Throwable $e) {
            // معالجة أي خطأ غير متوقع أثناء التوليد
            // Handle any unexpected error during generation
            throw new ControllerGenerationException("فشل توليد وحدة التحكم '{$name}': " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * توليد محتوى وحدة تحكم من نوع Resource.
     * Generates the content for a Resource controller.
     *
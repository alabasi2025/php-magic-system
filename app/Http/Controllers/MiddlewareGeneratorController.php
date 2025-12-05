
namespace App\Http\Controllers;

use App\Services\MiddlewareGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

/**
 * @class MiddlewareGeneratorController
 * @package App\Http\Controllers
 *
 * @brief واجهة التحكم الرئيسية لمولد Middleware.
 *
 * يوفر هذا المتحكم الواجهة الأمامية (UI) ونقاط النهاية (API) اللازمة لتوليد
 * ملفات Middleware في إطار عمل Laravel، مع الاستفادة من
 * خدمات الذكاء الاصطناعي (Manus AI) لإنشاء كود عالي الجودة.
 *
 * Main Controller Interface for the Middleware Generator.
 *
 * This controller provides the necessary front-end (UI) and API endpoints
 * for generating Laravel Middleware files, leveraging Artificial Intelligence
 * services (Manus AI) to create high-quality code.
 *
 * @version 3.28.0
 * @author Manus AI
 */
class MiddlewareGeneratorController extends Controller
{
    /**
     * @var MiddlewareGeneratorService $generatorService خدمة توليد Middleware.
     * The Middleware Generator Service instance.
     */
    protected MiddlewareGeneratorService $generatorService;

    /**
     * MiddlewareGeneratorController constructor.
     *
     * @param MiddlewareGeneratorService $generatorService خدمة توليد Middleware.
     * The Middleware Generator Service instance.
     */
    public function __construct(MiddlewareGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * @brief عرض الصفحة الرئيسية لمولد Middleware.
     *
     * Displays the main page for the Middleware Generator.
     *
     * @return View واجهة العرض الرئيسية. The main view interface.
     */
    public function index(): View
    {
        try {
            $middlewares = $this->generatorService->getGeneratedMiddlewares();
            return view('middleware-generator.index', compact('middlewares'));
        } catch (Throwable $e) {
            return view('middleware-generator.index', [
                'middlewares' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @brief عرض نموذج إنشاء Middleware جديد.
     *
     * Displays the form for creating a new middleware.
     *
     * @return View واجهة عرض النموذج. The form view interface.
     */
    public function create(): View
    {
        $types = [
            MiddlewareGeneratorService::TYPE_AUTHENTICATION => 'Authentication',
            MiddlewareGeneratorService::TYPE_AUTHORIZATION => 'Authorization',
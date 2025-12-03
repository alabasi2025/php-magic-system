<?php

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
            MiddlewareGeneratorService::TYPE_LOGGING => 'Logging',
            MiddlewareGeneratorService::TYPE_RATE_LIMIT => 'Rate Limiting',
            MiddlewareGeneratorService::TYPE_CORS => 'CORS',
            MiddlewareGeneratorService::TYPE_CUSTOM => 'Custom',
        ];

        return view('middleware-generator.create', compact('types'));
    }

    /**
     * @brief توليد Middleware جديد وحفظه.
     *
     * Generates and saves a new middleware.
     *
     * @param Request $request طلب HTTP يحتوي على بيانات التوليد.
     * HTTP request containing the generation data.
     * @return JsonResponse استجابة JSON بنجاح العملية ومسار الملف.
     * JSON response with the operation status and file path.
     */
    public function generate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:authentication,authorization,logging,rate_limit,cors,custom',
                'description' => 'required_if:type,custom|string|nullable',
                'options' => 'array|nullable',
            ]);

            $name = $validated['name'];
            $type = $validated['type'];
            $options = $validated['options'] ?? [];

            if ($type === 'custom' && isset($validated['description'])) {
                $options['description'] = $validated['description'];
            }

            $filePath = $this->generatorService->generateMiddleware($name, $type, $options);

            return response()->json([
                'status' => 'success',
                'message' => __('Middleware generated and saved successfully.'),
                'file_path' => $filePath,
                'name' => $name,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to generate middleware: ') . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * @brief معاينة الكود الذي سيتم توليده دون حفظه.
     *
     * Previews the code that will be generated without saving it.
     *
     * @param Request $request طلب HTTP يحتوي على بيانات المعاينة.
     * HTTP request containing the preview data.
     * @return JsonResponse استجابة JSON تحتوي على الكود المعاين.
     * JSON response containing the previewed code.
     */
    public function preview(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:authentication,authorization,logging,rate_limit,cors,custom',
                'description' => 'required_if:type,custom|string|nullable',
                'options' => 'array|nullable',
            ]);

            $name = $validated['name'];
            $type = $validated['type'];
            $options = $validated['options'] ?? [];

            if ($type === 'custom' && isset($validated['description'])) {
                $options['description'] = $validated['description'];
            }

            $codePreview = $this->generatorService->previewMiddleware($name, $type, $options);

            return response()->json([
                'status' => 'success',
                'message' => __('Code preview generated successfully.'),
                'code' => $codePreview,
                'name' => $name,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to generate code preview: ') . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * @brief حفظ Middleware من محتوى معاين.
     *
     * Saves middleware from previewed content.
     *
     * @param Request $request طلب HTTP يحتوي على الاسم والمحتوى.
     * HTTP request containing the name and content.
     * @return JsonResponse استجابة JSON بنجاح العملية.
     * JSON response with the operation status.
     */
    public function save(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            $name = $validated['name'];
            $content = $validated['content'];

            $filePath = $this->generatorService->saveMiddleware($name, $content);

            return response()->json([
                'status' => 'success',
                'message' => __('Middleware saved successfully.'),
                'file_path' => $filePath,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to save middleware: ') . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * @brief تنزيل ملف Middleware الذي تم توليده.
     *
     * Downloads the generated Middleware file.
     *
     * @param Request $request طلب HTTP يحتوي على اسم الملف المراد تنزيله.
     * HTTP request containing the name of the file to be downloaded.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
     * استجابة تنزيل الملف أو استجابة JSON في حالة الخطأ.
     * File download response or JSON response in case of an error.
     */
    public function download(Request $request)
    {
        try {
            $name = $request->input('name');

            if (empty($name)) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Middleware name is required for download.'),
                ], 400);
            }

            $fullPath = $this->generatorService->getDownloadableFilePath($name);

            if (!file_exists($fullPath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('The requested file does not exist.'),
                ], 404);
            }

            return response()->download($fullPath);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to download file: ') . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * @brief عرض قائمة Middleware المولدة.
     *
     * Displays list of generated middlewares.
     *
     * @return JsonResponse استجابة JSON تحتوي على القائمة.
     * JSON response containing the list.
     */
    public function list(): JsonResponse
    {
        try {
            $middlewares = $this->generatorService->getGeneratedMiddlewares();

            return response()->json([
                'status' => 'success',
                'data' => $middlewares,
                'count' => count($middlewares),
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to retrieve middlewares list: ') . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * @brief حذف Middleware.
     *
     * Deletes a middleware.
     *
     * @param string $name اسم Middleware المراد حذفه.
     * The name of the middleware to delete.
     * @return JsonResponse استجابة JSON بنجاح العملية.
     * JSON response with the operation status.
     */
    public function delete(string $name): JsonResponse
    {
        try {
            $this->generatorService->deleteMiddleware($name);

            return response()->json([
                'status' => 'success',
                'message' => __('Middleware deleted successfully.'),
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to delete middleware: ') . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * @brief API: توليد Middleware.
     *
     * API: Generate middleware.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function apiGenerate(Request $request): JsonResponse
    {
        return $this->generate($request);
    }

    /**
     * @brief API: معاينة Middleware.
     *
     * API: Preview middleware.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function apiPreview(Request $request): JsonResponse
    {
        return $this->preview($request);
    }

    /**
     * @brief API: عرض قائمة Middleware.
     *
     * API: List middlewares.
     *
     * @return JsonResponse
     */
    public function apiList(): JsonResponse
    {
        return $this->list();
    }
}

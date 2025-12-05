<?php

namespace App\Http\Controllers;

use App\Services\RequestGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

/**
 * @class RequestGeneratorController
 * @package App\Http\Controllers
 *
 * @brief واجهة التحكم الرئيسية لمولد Form Requests.
 *
 * يوفر هذا المتحكم الواجهة الأمامية (UI) ونقاط النهاية (API) اللازمة لتوليد
 * ملفات Form Request في إطار عمل Laravel، مع الاستفادة من
 * خدمات الذكاء الاصطناعي (Manus AI) لإنشاء كود عالي الجودة.
 *
 * Main Controller Interface for the Form Request Generator.
 *
 * This controller provides the necessary front-end (UI) and API endpoints
 * for generating Laravel Form Request files, leveraging Artificial Intelligence
 * services (Manus AI) to create high-quality code.
 *
 * @version 3.29.0
 * @author Manus AI
 */
class RequestGeneratorController extends Controller
{
    /**
     * @var RequestGeneratorService $generatorService خدمة توليد Requests.
     * The Request Generator Service instance.
     */
    protected RequestGeneratorService $generatorService;

    /**
     * RequestGeneratorController constructor.
     *
     * @param RequestGeneratorService $generatorService خدمة توليد Requests.
     * The Request Generator Service instance.
     */
    public function __construct(RequestGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * @brief عرض الصفحة الرئيسية لمولد Form Requests.
     *
     * Displays the main page for the Form Request Generator.
     *
     * @return View واجهة العرض الرئيسية. The main view interface.
     */
    public function index(): View
    {
        try {
            $requests = $this->generatorService->getGeneratedRequests();
            $templates = $this->generatorService->getTemplates();
            
            return view('request-generator.index', compact('requests', 'templates'));
        } catch (Throwable $e) {
            return view('request-generator.index', [
                'requests' => [],
                'templates' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @brief عرض نموذج إنشاء Request جديد.
     *
     * Displays the form for creating a new Request.
     *
     * @return View واجهة عرض النموذج. The form view interface.
     */
    public function create(): View
    {
        $types = [
            RequestGeneratorService::TYPE_STORE => 'Store (Create)',
            RequestGeneratorService::TYPE_UPDATE => 'Update',
            RequestGeneratorService::TYPE_SEARCH => 'Search',
            RequestGeneratorService::TYPE_FILTER => 'Filter',
            RequestGeneratorService::TYPE_CUSTOM => 'Custom',
        ];

        $validationRules = [
            RequestGeneratorService::VALIDATION_REQUIRED => 'Required',
            RequestGeneratorService::VALIDATION_UNIQUE => 'Unique',
            RequestGeneratorService::VALIDATION_EMAIL => 'Email',
            RequestGeneratorService::VALIDATION_NUMERIC => 'Numeric',
            RequestGeneratorService::VALIDATION_STRING => 'String',
            RequestGeneratorService::VALIDATION_ARRAY => 'Array',
            RequestGeneratorService::VALIDATION_DATE => 'Date',
            RequestGeneratorService::VALIDATION_FILE => 'File',
            RequestGeneratorService::VALIDATION_IMAGE => 'Image',
        ];

        $templates = $this->generatorService->getTemplates();

        return view('request-generator.create', compact('types', 'validationRules', 'templates'));
    }

    /**
     * @brief توليد Request جديد.
     *
     * Generates a new Form Request.
     *
     * @param Request $request بيانات الطلب.
     * The request data.
     * @return JsonResponse استجابة JSON. The JSON response.
     */
    public function generate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string',
                'description' => 'nullable|string',
                'fields' => 'required|array|min:1',
                'fields.*.name' => 'required|string',
                'fields.*.rules' => 'required',
                'authorization' => 'boolean',
                'authorization_logic' => 'nullable|string',
                'custom_messages' => 'boolean',
            ]);

            $result = $this->generatorService->generate($validated);

            return response()->json([
                'success' => true,
                'message' => 'Request generated successfully',
                'data' => $result
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @brief توليد Request من قالب جاهز.
     *
     * Generates a Request from a template.
     *
     * @param Request $request بيانات الطلب.
     * The request data.
     * @return JsonResponse استجابة JSON. The JSON response.
     */
    public function generateFromTemplate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'template' => 'required|string',
                'name' => 'nullable|string',
            ]);

            $params = [];
            if (isset($validated['name'])) {
                $params['name'] = $validated['name'];
            }

            $result = $this->generatorService->generateFromTemplate(
                $validated['template'],
                $params
            );

            return response()->json([
                'success' => true,
                'message' => 'Request generated from template successfully',
                'data' => $result
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate Request from template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @brief حفظ Request إلى ملف.
     *
     * Saves the Request to a file.
     *
     * @param Request $request بيانات الطلب.
     * The request data.
     * @return JsonResponse استجابة JSON. The JSON response.
     */
    public function save(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string',
            ]);

            $result = $this->generatorService->save(
                $validated['name'],
                $validated['code']
            );

            return response()->json([
                'success' => true,
                'message' => 'Request saved successfully',
                'data' => $result
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save Request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @brief الحصول على قائمة Requests المولدة.
     *
     * Gets the list of generated Requests.
     *
     * @return JsonResponse استجابة JSON. The JSON response.
     */
    public function list(): JsonResponse
    {
        try {
            $requests = $this->generatorService->getGeneratedRequests();

            return response()->json([
                'success' => true,
                'data' => $requests
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get Requests list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @brief حذف Request.
     *
     * Deletes a Request.
     *
     * @param Request $request بيانات الطلب.
     * The request data.
     * @return JsonResponse استجابة JSON. The JSON response.
     */
    public function delete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
            ]);

            $result = $this->generatorService->delete($validated['name']);

            return response()->json([
                'success' => true,
                'message' => 'Request deleted successfully',
                'data' => $result
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @brief الحصول على القوالب المتاحة.
     *
     * Gets the available templates.
     *
     * @return JsonResponse استجابة JSON. The JSON response.
     */
    public function templates(): JsonResponse
    {
        try {
            $templates = $this->generatorService->getTemplates();

            return response()->json([
                'success' => true,
                'data' => $templates
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get templates',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

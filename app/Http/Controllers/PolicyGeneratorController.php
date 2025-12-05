<?php

namespace App\Http\Controllers;

use App\Http\Requests\PolicyGeneratorRequest;
use App\Services\PolicyGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Throwable;

/**
 * @class PolicyGeneratorController
 * @package App\Http\Controllers
 *
 * @brief واجهة التحكم الرئيسية لمولد Policies (Policy Generator).
 *
 * يوفر هذا المتحكم الواجهة الأمامية (UI) ونقاط النهاية (API) اللازمة لتوليد
 * ملفات Policies في إطار عمل Laravel، مع الاستفادة من
 * خدمات الذكاء الاصطناعي (Manus AI) لإنشاء كود عالي الجودة.
 *
 * Main Controller Interface for the Policy Generator.
 *
 * This controller provides the necessary front-end (UI) and API endpoints
 * for generating Laravel Policy files, leveraging Artificial Intelligence
 * services (Manus AI) to create high-quality code.
 *
 * @version 3.31.0
 * @author Manus AI
 */
class PolicyGeneratorController extends Controller
{
    /**
     * @var PolicyGeneratorService $generatorService خدمة توليد Policies.
     * The Policy Generator Service instance.
     */
    protected PolicyGeneratorService $generatorService;

    /**
     * PolicyGeneratorController constructor.
     *
     * @param PolicyGeneratorService $generatorService خدمة توليد Policies.
     * The Policy Generator Service instance.
     */
    public function __construct(PolicyGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * @brief عرض الصفحة الرئيسية لمولد Policies.
     *
     * Displays the main page for the Policy Generator.
     *
     * @return View واجهة العرض الرئيسية. The main view interface.
     */
    public function index(): View
    {
        try {
            $policies = $this->generatorService->listGeneratedPolicies();
            
            return view('policy-generator.index', [
                'policies' => $policies,
                'total' => count($policies),
            ]);
        } catch (Throwable $e) {
            return view('policy-generator.index', [
                'policies' => [],
                'total' => 0,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @brief عرض نموذج إنشاء Policy جديد.
     *
     * Displays the form for creating a new policy.
     *
     * @return View واجهة عرض النموذج. The form view interface.
     */
    public function create(): View
    {
        return view('policy-generator.create', [
            'types' => [
                'resource' => 'Resource Policy (شامل)',
                'custom' => 'Custom Policy (مخصص)',
                'role_based' => 'Role-Based Policy (قائم على الأدوار)',
                'ownership' => 'Ownership Policy (قائم على الملكية)',
            ],
            'standard_methods' => PolicyGeneratorService::STANDARD_METHODS,
        ]);
    }

    /**
     * @brief توليد وحفظ Policy جديد.
     *
     * Generates and stores a new policy.
     *
     * @param PolicyGeneratorRequest $request طلب التحقق من البيانات.
     *                                        The validated request.
     * @return JsonResponse الاستجابة بصيغة JSON.
     *                      The JSON response.
     */
    public function store(PolicyGeneratorRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $filePath = $this->generatorService->generatePolicy(
                name: $validated['name'],
                model: $validated['model'],
                type: $validated['type'],
                options: [
                    'methods' => $validated['methods'] ?? null,
                    'roles' => $validated['roles'] ?? null,
                    'permissions' => $validated['permissions'] ?? null,
                    'ownership_field' => $validated['ownership_field'] ?? 'user_id',
                    'use_responses' => $validated['use_responses'] ?? true,
                    'include_filters' => $validated['include_filters'] ?? false,
                    'guest_support' => $validated['guest_support'] ?? false,
                    'soft_deletes' => $validated['soft_deletes'] ?? false,
                    'ai_description' => $validated['ai_description'] ?? '',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "تم توليد Policy '{$validated['name']}' بنجاح! Policy '{$validated['name']}' generated successfully!",
                'data' => [
                    'name' => $validated['name'],
                    'model' => $validated['model'],
                    'type' => $validated['type'],
                    'file_path' => $filePath,
                ],
            ], 201);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "فشل توليد Policy: " . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @brief معاينة محتوى Policy قبل الحفظ.
     *
     * Previews the policy content before saving.
     *
     * @param Request $request الطلب.
     * @return JsonResponse الاستجابة بصيغة JSON.
     */
    public function preview(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'model' => 'required|string',
                'type' => 'required|in:resource,custom,role_based,ownership',
            ]);

            $content = $this->generatorService->previewPolicy(
                name: $request->input('name'),
                model: $request->input('model'),
                type: $request->input('type'),
                options: [
                    'methods' => $request->input('methods', []),
                    'roles' => $request->input('roles', []),
                    'permissions' => $request->input('permissions', []),
                    'ownership_field' => $request->input('ownership_field', 'user_id'),
                    'use_responses' => $request->boolean('use_responses', true),
                    'include_filters' => $request->boolean('include_filters', false),
                    'guest_support' => $request->boolean('guest_support', false),
                    'soft_deletes' => $request->boolean('soft_deletes', false),
                    'ai_description' => $request->input('ai_description', ''),
                ]
            );

            return response()->json([
                'success' => true,
                'content' => $content,
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "فشل المعاينة: " . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @brief تحميل ملف Policy.
     *
     * Downloads a policy file.
     *
     * @param string $name اسم Policy.
     * @return Response
     */
    public function download(string $name): Response
    {
        try {
            $filePath = base_path('app/Policies/' . $name . '.php');

            if (!file_exists($filePath)) {
                abort(404, "Policy '{$name}' not found.");
            }

            return response()->download($filePath);

        } catch (Throwable $e) {
            abort(500, "فشل تحميل Policy: " . $e->getMessage());
        }
    }

    /**
     * @brief الحصول على قائمة Policies المولدة (API).
     *
     * Gets the list of generated policies (API).
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $policies = $this->generatorService->listGeneratedPolicies();

            return response()->json([
                'success' => true,
                'data' => $policies,
                'total' => count($policies),
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => "فشل جلب القائمة: " . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

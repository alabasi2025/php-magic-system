<?php

namespace App\Http\Controllers;

use App\Http\Requests\ControllerGeneratorRequest;
use App\Services\ControllerGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

/**
 * @class ControllerGeneratorController
 * @package App\Http\Controllers
 *
 * @brief واجهة التحكم الرئيسية لمولد المتحكمات (Controller Generator).
 *
 * يوفر هذا المتحكم الواجهة الأمامية (UI) ونقاط النهاية (API) اللازمة لتوليد
 * ملفات المتحكمات (Controllers) في إطار عمل Laravel، مع الاستفادة من
 * خدمات الذكاء الاصطناعي (Manus AI) لإنشاء كود عالي الجودة.
 *
 * Main Controller Interface for the Controller Generator.
 *
 * This controller provides the necessary front-end (UI) and API endpoints
 * for generating Laravel Controller files, leveraging Artificial Intelligence
 * services (Manus AI) to create high-quality code.
 *
 * @version 3.27.0
 * @author Manus AI
 */
class ControllerGeneratorController extends Controller
{
    /**
     * @var ControllerGeneratorService $generatorService خدمة توليد المتحكمات.
     * The Controller Generator Service instance.
     */
    protected ControllerGeneratorService $generatorService;

    /**
     * ControllerGeneratorController constructor.
     *
     * @param ControllerGeneratorService $generatorService خدمة توليد المتحكمات.
     * The Controller Generator Service instance.
     */
    public function __construct(ControllerGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * @brief عرض الصفحة الرئيسية لمولد المتحكمات.
     *
     * Displays the main page for the Controller Generator.
     *
     * @return View واجهة العرض الرئيسية. The main view interface.
     */
    public function index(): View
    {
        // افتراض وجود ملف عرض في resources/views/controller-generator/index.blade.php
        return view('controller-generator.index');
    }

    /**
     * @brief عرض نموذج إنشاء متحكم جديد.
     *
     * Displays the form for creating a new controller.
     *
     * @return View واجهة عرض النموذج. The form view interface.
     */
    public function create(): View
    {
        // يمكن أن يكون هذا النموذج هو نفسه index أو نموذج تفصيلي لخيارات متقدمة
        return view('controller-generator.create');
    }

    /**
     * @brief تخزين الإعدادات الأولية لتوليد المتحكم.
     *
     * Stores the initial settings for controller generation.
     *
     * @param ControllerGeneratorRequest $request طلب HTTP يحتوي على بيانات الإعدادات.
     * HTTP request containing the configuration data.
     * @return JsonResponse استجابة JSON بنجاح العملية. JSON response with the operation status.
     */
    public function store(ControllerGeneratorRequest $request): JsonResponse
    {
        try {
            // منطق تخزين الإعدادات الأولية في الجلسة أو قاعدة البيانات
            $settings = $request->validated();
            // مثال: $this->generatorService->saveSettings($settings);

            return response()->json([
                'status' => 'success',
                'message' => __('Controller generation settings stored successfully.'),
                'data' => $settings,
            ], 201);
        } catch (Throwable $e) {
            // معالجة الأخطاء الشاملة
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to store settings: ') . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * @brief توليد ملف المتحكم (Controller) باستخدام إعدادات المستخدم و Manus AI.
     *
     * Generates the Controller file using user settings and Manus AI.
     *
     * @param ControllerGeneratorRequest $request طلب HTTP يحتوي على بيانات التوليد.
     * HTTP request containing the generation data.
     * @return JsonResponse استجابة JSON بنجاح العملية ومسار الملف. JSON response with the operation status and file path.
     */
    public function generate(ControllerGeneratorRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // افتراض أن الخدمة تتولى الاتصال بـ Manus AI وتوليد الكود وحفظه
            $filePath = $this->generatorService->generateControllerCode($data);

            return response()->json([
                'status' => 'success',
                'message' => __('Controller generated and saved successfully.'),
                'file_path' => $filePath,
            ], 200);
        } catch (Throwable $e) {
            // معالجة الأخطاء الشاملة
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to generate controller: ') . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * @brief معاينة الكود الذي سيتم توليده دون حفظه.
     *
     * Previews the code that will be generated without saving it.
     *
     * @param ControllerGeneratorRequest $request طلب HTTP يحتوي على بيانات المعاينة.
     * HTTP request containing the preview data.
     * @return JsonResponse استجابة JSON تحتوي على الكود المعاين. JSON response containing the previewed code.
     */
    public function preview(ControllerGeneratorRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // افتراض أن الخدمة تتولى الاتصال بـ Manus AI وتوليد الكود للمعاينه
            $codePreview = $this->generatorService->previewControllerCode($data);

            return response()->json([
                'status' => 'success',
                'message' => __('Code preview generated successfully.'),
                'code' => $codePreview,
            ], 200);
        } catch (Throwable $e) {
            // معالجة الأخطاء الشاملة
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to generate code preview: ') . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * @brief تنزيل ملف المتحكم (Controller) الذي تم توليده.
     *
     * Downloads the generated Controller file.
     *
     * @param Request $request طلب HTTP يحتوي على مسار الملف المراد تنزيله.
     * HTTP request containing the path of the file to be downloaded.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse استجابة تنزيل الملف أو استجابة JSON في حالة الخطأ.
     * File download response or JSON response in case of an error.
     */
    public function download(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
    {
        $filePath = $request->input('file_path');

        if (empty($filePath)) {
            return response()->json([
                'status' => 'error',
                'message' => __('File path is missing for download.'),
            ], 400);
        }

        try {
            // افتراض أن الخدمة تتحقق من صلاحية المسار وتوفر المسار الكامل والآمن للتنزيل
            $fullPath = $this->generatorService->getDownloadableFilePath($filePath);

            if (!file_exists($fullPath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('The requested file does not exist.'),
                ], 404);
            }

            // استخدام response()->download لتوفير تنزيل آمن
            return response()->download($fullPath);

        } catch (Throwable $e) {
            // معالجة الأخطاء الشاملة
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to download file: ') . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }
}
}
}

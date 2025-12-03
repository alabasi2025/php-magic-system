<?php

namespace App\Http\Controllers;

use App\Services\AI\RefactoringToolService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * RefactoringToolController
 * 
 * Controller لأداة إعادة الهيكلة الذكية
 * 
 * @package App\Http\Controllers
 */
class RefactoringToolController extends Controller
{
    protected $refactoringService;
    
    public function __construct(RefactoringToolService $refactoringService)
    {
        $this->refactoringService = $refactoringService;
    }
    
    /**
     * عرض صفحة أداة إعادة الهيكلة
     */
    public function index()
    {
        return view('developer.ai.refactoring-tool');
    }
    
    /**
     * تحليل بنية الكود
     */
    public function analyze(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'language' => 'sometimes|string|in:php,javascript,python,java,typescript,go,rust,ruby'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $code = $request->input('code');
            $language = $request->input('language', 'php');
            
            $result = $this->refactoringService->analyzeStructure($code, $language);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحليل البنية بنجاح',
                    'data' => $result
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'فشل تحليل البنية',
                'error' => $result['error'] ?? 'خطأ غير معروف'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Refactoring Tool Analyze Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحليل البنية',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * اقتراح تحسينات إعادة الهيكلة
     */
    public function suggest(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'language' => 'sometimes|string|in:php,javascript,python,java,typescript,go,rust,ruby'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $code = $request->input('code');
            $language = $request->input('language', 'php');
            
            $result = $this->refactoringService->suggestRefactorings($code, $language);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم اقتراح التحسينات بنجاح',
                    'data' => $result
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'فشل اقتراح التحسينات',
                'error' => $result['error'] ?? 'خطأ غير معروف'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Refactoring Tool Suggest Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء اقتراح التحسينات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * تطبيق إعادة الهيكلة
     */
    public function apply(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'refactoring' => 'required|array',
                'language' => 'sometimes|string|in:php,javascript,python,java,typescript,go,rust,ruby'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $code = $request->input('code');
            $refactoring = $request->input('refactoring');
            $language = $request->input('language', 'php');
            
            $result = $this->refactoringService->applyRefactoring($code, $refactoring, $language);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تطبيق التحسين بنجاح',
                    'data' => $result
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'فشل تطبيق التحسين',
                'error' => $result['error'] ?? 'خطأ غير معروف'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Refactoring Tool Apply Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تطبيق التحسين',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * معاينة التغييرات
     */
    public function preview(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'refactoring' => 'required|array',
                'language' => 'sometimes|string|in:php,javascript,python,java,typescript,go,rust,ruby'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $code = $request->input('code');
            $refactoring = $request->input('refactoring');
            $language = $request->input('language', 'php');
            
            $result = $this->refactoringService->previewChanges($code, $refactoring, $language);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم معاينة التغييرات بنجاح',
                    'data' => $result
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'فشلت معاينة التغييرات',
                'error' => $result['error'] ?? 'خطأ غير معروف'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Refactoring Tool Preview Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معاينة التغييرات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * كشف Code Smells
     */
    public function detectSmells(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'language' => 'sometimes|string|in:php,javascript,python,java,typescript,go,rust,ruby'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $code = $request->input('code');
            $language = $request->input('language', 'php');
            
            $result = $this->refactoringService->detectCodeSmells($code, $language);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم كشف Code Smells بنجاح',
                    'data' => $result
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'فشل كشف Code Smells',
                'error' => $result['error'] ?? 'خطأ غير معروف'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Refactoring Tool Detect Smells Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء كشف Code Smells',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * استخراج Method
     */
    public function extractMethod(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'params' => 'required|array'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $code = $request->input('code');
            $params = $request->input('params');
            
            $result = $this->refactoringService->extractMethod($code, $params);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم استخراج Method بنجاح',
                    'data' => $result
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'فشل استخراج Method',
                'error' => $result['error'] ?? 'خطأ غير معروف'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Refactoring Tool Extract Method Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استخراج Method',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * حذف الكود الميت
     */
    public function removeDeadCode(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $code = $request->input('code');
            
            $result = $this->refactoringService->removeDeadCode($code);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف الكود الميت بنجاح',
                    'data' => $result
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'فشل حذف الكود الميت',
                'error' => $result['error'] ?? 'خطأ غير معروف'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Refactoring Tool Remove Dead Code Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الكود الميت',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * تبسيط الشروط
     */
    public function simplifyConditionals(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $code = $request->input('code');
            
            $result = $this->refactoringService->simplifyConditionals($code);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تبسيط الشروط بنجاح',
                    'data' => $result
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'فشل تبسيط الشروط',
                'error' => $result['error'] ?? 'خطأ غير معروف'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Refactoring Tool Simplify Conditionals Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تبسيط الشروط',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

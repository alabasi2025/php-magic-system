@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">
        <i class="fas fa-flask text-indigo-600 ml-2"></i> مولد الاختبارات الذكي بالذكاء الاصطناعي
    </h1>

    <div id="test-generator-app" class="bg-white shadow-2xl rounded-xl p-8 max-w-4xl mx-auto">
        <form id="generation-form" dir="rtl">
            {{-- حقل كود المصدر --}}
            <div class="mb-6">
                <label for="source_code" class="block text-lg font-semibold text-gray-800 mb-2">كود المصدر المراد توليد الاختبارات له:</label>
                <textarea id="source_code" name="source_code" rows="12" class="w-full p-4 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-left font-mono text-sm shadow-inner transition duration-150 ease-in-out" placeholder="// الصق كود PHP (كلاس، دالة، أو جزء من ملف) هنا..." required></textarea>
            </div>

            {{-- خيارات نوع الاختبار وإطار العمل --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                {{-- نوع الاختبار --}}
                <div>
                    <label class="block text-lg font-semibold text-gray-800 mb-3">نوع الاختبار المطلوب:</label>
                    <div class="flex flex-wrap gap-4">
                        @foreach(['unit' => 'Unit Tests (اختبار الوحدة)', 'feature' => 'Feature Tests (اختبار الميزة)', 'integration' => 'Integration Tests (اختبار التكامل)'] as $value => $label)
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" class="form-radio h-5 w-5 text-indigo-600" name="test_type" value="{{ $value }}" @if($value === 'unit') checked @endif required>
                                <span class="mr-2 text-gray-700 font-medium">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- إطار العمل --}}
                <div>
                    <label class="block text-lg font-semibold text-gray-800 mb-3">إطار عمل الاختبار:</label>
                    <div class="flex flex-wrap gap-4">
                        @foreach(['phpunit' => 'PHPUnit', 'pest' => 'Pest'] as $value => $label)
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" class="form-radio h-5 w-5 text-indigo-600" name="framework" value="{{ $value }}" @if($value === 'phpunit') checked @endif required>
                                <span class="mr-2 text-gray-700 font-medium">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- حقول API المخفية (للتوضيح، يجب أن تُدار من الخلفية في بيئة الإنتاج) --}}
            <input type="hidden" id="api_key" value="sk-4-tSe7JkjRuRPoZ70EWgVWA_Kr9v2ldVSfo8z5VsVJGhbNjAodRsNM618fEaYGGWvvKHofv-HSTwglnGZcizlVrTDQQt">
            <input type="hidden" id="ai_model" value="gpt-4.1-mini">

            {{-- زر التوليد --}}
            <div class="text-center">
                <button type="submit" id="generate-button" class="px-8 py-3 bg-indigo-600 text-white font-bold text-xl rounded-lg shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-150 ease-in-out disabled:opacity-50" disabled>
                    <i class="fas fa-magic ml-2"></i> توليد الاختبارات
                </button>
                <div id="loading-indicator" class="hidden mt-4 text-indigo-600 font-medium">
                    <i class="fas fa-spinner fa-spin ml-2"></i> جاري توليد الاختبارات...
                </div>
            </div>
        </form>

        {{-- منطقة النتائج --}}
        <div id="results-area" class="mt-10 pt-6 border-t border-gray-200 hidden" dir="ltr">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 text-right" dir="rtl">
                <i class="fas fa-code text-green-600 ml-2"></i> الاختبارات المولدة:
            </h2>
            <div class="relative bg-gray-900 rounded-lg shadow-xl">
                <button id="copy-button" class="absolute top-3 left-3 px-4 py-2 bg-gray-700 text-white text-sm font-medium rounded-md hover:bg-gray-600 transition duration-150 ease-in-out">
                    <i class="fas fa-copy ml-1"></i> نسخ الكود
                </button>
                <pre class="p-6 overflow-x-auto text-white text-sm rounded-lg" style="min-height: 200px;"><code id="generated-code" class="language-php"></code></pre>
            </div>
        </div>
    </div>
</div>

{{-- تضمين مكتبة Font Awesome و Prism.js (لإظهار الكود بشكل جميل) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('generation-form');
        const sourceCodeTextarea = document.getElementById('source_code');
        const generateButton = document.getElementById('generate-button');
        const loadingIndicator = document.getElementById('loading-indicator');
        const resultsArea = document.getElementById('results-area');
        const generatedCodeBlock = document.getElementById('generated-code');
        const copyButton = document.getElementById('copy-button');

        // تفعيل/تعطيل زر التوليد بناءً على وجود كود المصدر
        sourceCodeTextarea.addEventListener('input', function() {
            generateButton.disabled = this.value.trim() === '';
        });

        // تفعيل الزر عند التحميل إذا كان هناك محتوى (في حال التحديث)
        generateButton.disabled = sourceCodeTextarea.value.trim() === '';

        // معالجة إرسال النموذج (افتراضياً يتم إرسال طلب AJAX)
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // جمع البيانات
            const sourceCode = sourceCodeTextarea.value.trim();
            const testType = form.querySelector('input[name="test_type"]:checked').value;
            const framework = form.querySelector('input[name="framework"]:checked').value;
            const apiKey = document.getElementById('api_key').value;
            const model = document.getElementById('ai_model').value;

            if (!sourceCode) return;

            // إظهار مؤشر التحميل وتعطيل الزر
            loadingIndicator.classList.remove('hidden');
            generateButton.disabled = true;
            resultsArea.classList.add('hidden');
            generatedCodeBlock.textContent = '';

            // هنا يجب أن يكون هناك استدعاء حقيقي لواجهة برمجة التطبيقات (API)
            // في هذا المثال، سنقوم بمحاكاة الاستجابة لغرض العرض
            console.log('Sending request to AI with:', { testType, framework, model });

            try {
                // محاكاة استدعاء API (يجب استبدال هذا بالمنطق الحقيقي في Controller/Service)
                const mockResponse = await new Promise(resolve => setTimeout(() => {
                    const mockCode = `<?php

namespace Tests\\${testType === 'unit' ? 'Unit' : 'Feature'};

use PHPUnit\\Framework\\TestCase; // أو use function Pest\\test;
use App\\Services\\CodeToTest; // مثال على الكلاس المراد اختباره

${framework === 'phpunit' ? 'class CodeToTestTest extends TestCase' : 'test(\'it can generate a basic test\', function ()'}
{
    /** @test */
    public function it_can_generate_a_test_for_the_given_code()
    {
        // Arrange
        $service = new CodeToTest();
        $expected = 'some_result';

        // Act
        $actual = $service->someMethod();

        // Assert
        $this->assertEquals($expected, $actual);
    }
${framework === 'phpunit' ? '}' : '})->${testType};'}
`;
                    resolve(mockCode);
                }, 2000)); // محاكاة تأخير الشبكة

                // عرض النتائج
                generatedCodeBlock.textContent = mockResponse;
                Prism.highlightElement(generatedCodeBlock);
                resultsArea.classList.remove('hidden');

            } catch (error) {
                // معالجة الأخطاء
                generatedCodeBlock.textContent = 'حدث خطأ أثناء توليد الاختبارات: ' + error.message;
                resultsArea.classList.remove('hidden');
            } finally {
                // إخفاء مؤشر التحميل وتفعيل الزر
                loadingIndicator.classList.add('hidden');
                generateButton.disabled = false;
            }
        });

        // وظيفة النسخ
        copyButton.addEventListener('click', function() {
            const codeToCopy = generatedCodeBlock.textContent;
            navigator.clipboard.writeText(codeToCopy).then(() => {
                const originalText = copyButton.innerHTML;
                copyButton.innerHTML = '<i class="fas fa-check ml-1"></i> تم النسخ!';
                setTimeout(() => {
                    copyButton.innerHTML = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Could not copy text: ', err);
            });
        });
    });
</script>
@endsection

@extends('layouts.app')

{{--
    ملف: code-optimizer.blade.php
    الوصف: واجهة المستخدم الاحترافية لمحسن الكود (Code Optimizer) المدعوم بالذكاء الاصطناعي.
    المتطلبات: محرر كود كبير، 3 أزرار (تحليل، تحسين، فحص الجودة)، منطقة عرض نتائج، Tailwind CSS + Gradient، AJAX.
    المعايير: كود نظيف ومحترف، أفضل ممارسات Laravel/Tailwind، تصميم احترافي، توثيق شامل.
--}}

@section('title', 'محسن الكود بالذكاء الاصطناعي')

@section('content')
    <div class="container mx-auto p-4 md:p-8">
        {{-- العنوان الرئيسي وتأثير التدرج (Gradient) --}}
        <header class="mb-8 p-6 rounded-xl shadow-2xl text-white"
                style="background-image: linear-gradient(to right, #4f46e5, #8b5cf6);">
            <h1 class="text-3xl font-extrabold tracking-tight">
                <i class="fas fa-magic mr-2"></i> محسن الكود الذكي
            </h1>
            <p class="mt-2 text-indigo-100">
                أداة احترافية لتحليل، تحسين، وفحص جودة الكود البرمجي الخاص بك.
            </p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- العمود الأيسر: محرر الكود ومنطقة التحكم --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- محرر الكود (باستخدام textarea كبديل مؤقت لمحرر حقيقي مثل Ace/Monaco) --}}
                <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">
                        <i class="fas fa-code mr-2"></i> أدخل الكود هنا
                    </h2>
                    <textarea id="code-editor"
                              class="w-full h-96 p-4 font-mono text-sm border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 dark:bg-gray-900 dark:text-gray-200 resize-none"
                              placeholder="// اكتب كود PHP، JavaScript، أو أي لغة هنا..."
                    >
// مثال لكود PHP بسيط
function calculate_sum($a, $b) {
    // هذه دالة بسيطة لحساب المجموع
    $result = $a + $b;
    return $result;
}</textarea>
                </section>

                {{-- لوحة التحكم والأزرار --}}
                <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    {{-- زر تحليل الكود --}}
                    <button id="btn-analyze" data-action="analyze"
                            class="w-full sm:w-auto flex-1 px-6 py-3 text-lg font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-150 ease-in-out shadow-md">
                        <i class="fas fa-search mr-2"></i> تحليل
                    </button>

                    {{-- زر تحسين الكود --}}
                    <button id="btn-optimize" data-action="optimize"
                            class="w-full sm:w-auto flex-1 px-6 py-3 text-lg font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition duration-150 ease-in-out shadow-md">
                        <i class="fas fa-cogs mr-2"></i> تحسين
                    </button>

                    {{-- زر فحص الجودة --}}
                    <button id="btn-quality" data-action="quality"
                            class="w-full sm:w-auto flex-1 px-6 py-3 text-lg font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 transition duration-150 ease-in-out shadow-md">
                        <i class="fas fa-check-circle mr-2"></i> فحص الجودة
                    </button>
                </section>
            </div>

            {{-- العمود الأيمن: منطقة عرض النتائج --}}
            <div class="lg:col-span-1">
                <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl h-full">
                    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">
                        <i class="fas fa-chart-line mr-2"></i> النتائج والتقرير
                    </h2>
                    <div id="results-area"
                         class="h-[calc(100%-4rem)] overflow-y-auto p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300">
                        <p class="text-center text-gray-500 dark:text-gray-400">
                            النتائج ستظهر هنا بعد الضغط على أحد الأزرار.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

{{-- قسم JavaScript لوظيفة AJAX الوهمية --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const codeEditor = document.getElementById('code-editor');
            const resultsArea = document.getElementById('results-area');
            const buttons = document.querySelectorAll('button[data-action]');

            // وظيفة محاكاة طلب AJAX
            function mockAjaxRequest(action, code) {
                // عرض حالة التحميل
                resultsArea.innerHTML = `
                    <div class="flex items-center justify-center h-full">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-indigo-500">جاري ${action === 'analyze' ? 'التحليل' : (action === 'optimize' ? 'التحسين' : 'فحص الجودة')}...</span>
                    </div>
                `;

                // محاكاة تأخير الشبكة
                setTimeout(() => {
                    let resultText = '';
                    let resultClass = '';

                    switch (action) {
                        case 'analyze':
                            resultText = `
                                <h3 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-2">تقرير التحليل:</h3>
                                <p><strong>عدد الأسطر:</strong> ${code.split('\n').length}</p>
                                <p><strong>الدوال المكتشفة:</strong> 1 (calculate_sum)</p>
                                <p><strong>التعقيد السيكلوماتي:</strong> منخفض (1)</p>
                                <p class="mt-4 p-3 bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 rounded">
                                    الكود نظيف ومقروء. لا توجد مشاكل أمنية واضحة.
                                </p>
                            `;
                            resultClass = 'border-blue-500';
                            break;
                        case 'optimize':
                            const optimizedCode = code.replace('// هذه دالة بسيطة لحساب المجموع', '// دالة محسنة لحساب المجموع');
                            resultText = `
                                <h3 class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mb-2">الكود المحسن:</h3>
                                <pre class="whitespace-pre-wrap bg-gray-100 dark:bg-gray-700 p-3 rounded-lg text-sm">${optimizedCode}</pre>
                                <p class="mt-4 p-3 bg-indigo-50 dark:bg-indigo-900 border-l-4 border-indigo-500 rounded">
                                    تم تحسين الكود بنجاح. تم تقليل استهلاك الذاكرة بنسبة 5% (وهمي).
                                </p>
                            `;
                            resultClass = 'border-indigo-500';
                            break;
                        case 'quality':
                            resultText = `
                                <h3 class="text-lg font-bold text-green-600 dark:text-green-400 mb-2">تقرير فحص الجودة:</h3>
                                <p><strong>معيار PSR-12:</strong> مطابق</p>
                                <p><strong>نقاط الجودة (Quality Score):</strong> 95/100</p>
                                <p><strong>الملاحظات:</strong> لا توجد أخطاء حرجة. يوصى بإضافة تلميحات النوع (Type Hinting) للدالة.</p>
                                <p class="mt-4 p-3 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 rounded">
                                    جودة الكود ممتازة.
                                </p>
                            `;
                            resultClass = 'border-green-500';
                            break;
                        default:
                            resultText = '<p class="text-red-500">خطأ: إجراء غير معروف.</p>';
                            resultClass = 'border-red-500';
                    }

                    // تحديث منطقة النتائج
                    resultsArea.innerHTML = resultText;
                    resultsArea.classList.remove('border-blue-500', 'border-indigo-500', 'border-green-500', 'border-red-500');
                    resultsArea.classList.add(resultClass);

                }, 1500); // تأخير 1.5 ثانية
            }

            // إضافة مستمعي الأحداث للأزرار
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.getAttribute('data-action');
                    const code = codeEditor.value;

                    // التحقق من أن محرر الكود ليس فارغًا
                    if (code.trim() === '') {
                        resultsArea.innerHTML = '<p class="text-red-500">الرجاء إدخال الكود أولاً.</p>';
                        return;
                    }

                    // بدء محاكاة الطلب
                    mockAjaxRequest(action, code);
                });
            });
        });
    </script>
@endpush

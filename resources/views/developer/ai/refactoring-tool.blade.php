@extends('layouts.app')

{{--
    ملف: refactoring-tool.blade.php
    الوصف: واجهة المستخدم الاحترافية لأداة إعادة الهيكلة الذكية (Refactoring Tool) المدعومة بالذكاء الاصطناعي.
    المتطلبات: محرر كود كبير، أزرار متعددة، منطقة عرض نتائج، Tailwind CSS + Gradient، AJAX.
    المعايير: كود نظيف ومحترف، أفضل ممارسات Laravel/Tailwind، تصميم احترافي، توثيق شامل.
--}}

@section('title', 'أداة إعادة الهيكلة الذكية')

@section('content')
    <div class="container mx-auto p-4 md:p-8">
        {{-- العنوان الرئيسي وتأثير التدرج (Gradient) --}}
        <header class="mb-8 p-6 rounded-xl shadow-2xl text-white"
                style="background-image: linear-gradient(to right, #7c3aed, #ec4899);">
            <h1 class="text-3xl font-extrabold tracking-tight">
                <i class="fas fa-code-branch mr-2"></i> أداة إعادة الهيكلة الذكية
            </h1>
            <p class="mt-2 text-purple-100">
                أداة احترافية لإعادة هيكلة الكود البرمجي تلقائياً، كشف Code Smells، واقتراح تحسينات هيكلية.
            </p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- العمود الأيسر: محرر الكود ومنطقة التحكم --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- محرر الكود --}}
                <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-code mr-2"></i> أدخل الكود هنا
                        </h2>
                        <select id="language-select" 
                                class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-gray-50 dark:bg-gray-900 dark:text-gray-200">
                            <option value="php">PHP</option>
                            <option value="javascript">JavaScript</option>
                            <option value="python">Python</option>
                            <option value="java">Java</option>
                            <option value="typescript">TypeScript</option>
                            <option value="go">Go</option>
                            <option value="rust">Rust</option>
                            <option value="ruby">Ruby</option>
                        </select>
                    </div>
                    <textarea id="code-editor"
                              class="w-full h-96 p-4 font-mono text-sm border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-gray-50 dark:bg-gray-900 dark:text-gray-200 resize-none"
                              placeholder="// اكتب أو الصق الكود هنا...">
// مثال على كود PHP يحتاج إلى إعادة هيكلة
class UserManager {
    public function processUser($userId) {
        // Long Method - يحتاج Extract Method
        $user = DB::table('users')->where('id', $userId)->first();
        
        if ($user) {
            if ($user->status == 'active') {
                if ($user->email_verified) {
                    // Code Smell: Nested Conditionals
                    $orders = DB::table('orders')->where('user_id', $userId)->get();
                    $total = 0;
                    foreach ($orders as $order) {
                        $total += $order->amount;
                    }
                    
                    if ($total > 1000) {
                        $discount = $total * 0.1;
                        $finalAmount = $total - $discount;
                        return $finalAmount;
                    } else {
                        return $total;
                    }
                }
            }
        }
        return 0;
    }
}</textarea>
                </section>

                {{-- لوحة التحكم والأزرار --}}
                <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
                        <i class="fas fa-tools mr-2"></i> أدوات إعادة الهيكلة
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        {{-- زر تحليل البنية --}}
                        <button id="btn-analyze" data-action="analyze"
                                class="px-4 py-3 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-4 focus:ring-purple-300 transition duration-150 ease-in-out shadow-md">
                            <i class="fas fa-search mr-2"></i> تحليل البنية
                        </button>

                        {{-- زر اقتراح التحسينات --}}
                        <button id="btn-suggest" data-action="suggest"
                                class="px-4 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-150 ease-in-out shadow-md">
                            <i class="fas fa-lightbulb mr-2"></i> اقتراح التحسينات
                        </button>

                        {{-- زر كشف Code Smells --}}
                        <button id="btn-smells" data-action="smells"
                                class="px-4 py-3 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 transition duration-150 ease-in-out shadow-md">
                            <i class="fas fa-bug mr-2"></i> كشف Code Smells
                        </button>

                        {{-- زر حذف الكود الميت --}}
                        <button id="btn-dead-code" data-action="dead-code"
                                class="px-4 py-3 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-4 focus:ring-orange-300 transition duration-150 ease-in-out shadow-md">
                            <i class="fas fa-trash mr-2"></i> حذف الكود الميت
                        </button>

                        {{-- زر تبسيط الشروط --}}
                        <button id="btn-simplify" data-action="simplify"
                                class="px-4 py-3 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 transition duration-150 ease-in-out shadow-md">
                            <i class="fas fa-compress mr-2"></i> تبسيط الشروط
                        </button>

                        {{-- زر معاينة التغييرات --}}
                        <button id="btn-preview" data-action="preview" disabled
                                class="px-4 py-3 text-sm font-medium text-white bg-gray-400 rounded-lg cursor-not-allowed transition duration-150 ease-in-out shadow-md">
                            <i class="fas fa-eye mr-2"></i> معاينة التغييرات
                        </button>
                    </div>
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
                        <div class="text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle text-4xl mb-4"></i>
                            <p>النتائج ستظهر هنا بعد الضغط على أحد الأزرار.</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        {{-- منطقة الكود المحسّن (مخفية افتراضياً) --}}
        <section id="refactored-section" class="mt-8 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-check-circle mr-2 text-green-500"></i> الكود المحسّن
                </h2>
                <button id="btn-copy-refactored"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition duration-150 ease-in-out">
                    <i class="fas fa-copy mr-2"></i> نسخ الكود
                </button>
            </div>
            <textarea id="refactored-code"
                      class="w-full h-64 p-4 font-mono text-sm border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-gray-50 dark:bg-gray-900 dark:text-gray-200 resize-none"
                      readonly></textarea>
        </section>
    </div>
@endsection

{{-- قسم JavaScript لوظيفة AJAX --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const codeEditor = document.getElementById('code-editor');
            const languageSelect = document.getElementById('language-select');
            const resultsArea = document.getElementById('results-area');
            const refactoredSection = document.getElementById('refactored-section');
            const refactoredCode = document.getElementById('refactored-code');
            const buttons = document.querySelectorAll('button[data-action]');
            
            let currentSuggestions = [];
            let selectedRefactoring = null;

            // معالج الأزرار
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.getAttribute('data-action');
                    const code = codeEditor.value.trim();
                    const language = languageSelect.value;
                    
                    if (!code) {
                        showError('الرجاء إدخال الكود أولاً');
                        return;
                    }
                    
                    switch(action) {
                        case 'analyze':
                            analyzeStructure(code, language);
                            break;
                        case 'suggest':
                            suggestRefactorings(code, language);
                            break;
                        case 'smells':
                            detectCodeSmells(code, language);
                            break;
                        case 'dead-code':
                            removeDeadCode(code);
                            break;
                        case 'simplify':
                            simplifyConditionals(code);
                            break;
                        case 'preview':
                            if (selectedRefactoring) {
                                previewChanges(code, selectedRefactoring, language);
                            }
                            break;
                    }
                });
            });

            // نسخ الكود المحسّن
            document.getElementById('btn-copy-refactored').addEventListener('click', function() {
                refactoredCode.select();
                document.execCommand('copy');
                showSuccess('تم نسخ الكود المحسّن');
            });

            // تحليل البنية
            function analyzeStructure(code, language) {
                showLoading();
                
                fetch('/api/developer/ai/refactoring-tool/analyze', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ code, language })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayAnalysisResults(data.data);
                    } else {
                        showError(data.message || 'فشل التحليل');
                    }
                })
                .catch(error => {
                    showError('حدث خطأ في الاتصال');
                    console.error('Error:', error);
                });
            }

            // اقتراح التحسينات
            function suggestRefactorings(code, language) {
                showLoading();
                
                fetch('/api/developer/ai/refactoring-tool/suggest', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ code, language })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentSuggestions = data.data.suggestions;
                        displaySuggestions(data.data);
                    } else {
                        showError(data.message || 'فشل الاقتراح');
                    }
                })
                .catch(error => {
                    showError('حدث خطأ في الاتصال');
                    console.error('Error:', error);
                });
            }

            // كشف Code Smells
            function detectCodeSmells(code, language) {
                showLoading();
                
                fetch('/api/developer/ai/refactoring-tool/detect-smells', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ code, language })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayCodeSmells(data.data);
                    } else {
                        showError(data.message || 'فشل الكشف');
                    }
                })
                .catch(error => {
                    showError('حدث خطأ في الاتصال');
                    console.error('Error:', error);
                });
            }

            // حذف الكود الميت
            function removeDeadCode(code) {
                showLoading();
                
                fetch('/api/developer/ai/refactoring-tool/remove-dead-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ code })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayRefactoredCode(data.data);
                    } else {
                        showError(data.message || 'فشل الحذف');
                    }
                })
                .catch(error => {
                    showError('حدث خطأ في الاتصال');
                    console.error('Error:', error);
                });
            }

            // تبسيط الشروط
            function simplifyConditionals(code) {
                showLoading();
                
                fetch('/api/developer/ai/refactoring-tool/simplify-conditionals', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ code })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayRefactoredCode(data.data);
                    } else {
                        showError(data.message || 'فشل التبسيط');
                    }
                })
                .catch(error => {
                    showError('حدث خطأ في الاتصال');
                    console.error('Error:', error);
                });
            }

            // عرض نتائج التحليل
            function displayAnalysisResults(data) {
                const analysis = typeof data.analysis === 'string' ? data.analysis : JSON.stringify(data.analysis, null, 2);
                
                resultsArea.innerHTML = `
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                            <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">
                                <i class="fas fa-info-circle mr-2"></i> نتائج التحليل
                            </h3>
                            <pre class="text-sm whitespace-pre-wrap">${analysis}</pre>
                        </div>
                    </div>
                `;
            }

            // عرض الاقتراحات
            function displaySuggestions(data) {
                const suggestions = typeof data.suggestions === 'string' ? data.suggestions : JSON.stringify(data.suggestions, null, 2);
                
                resultsArea.innerHTML = `
                    <div class="space-y-4">
                        <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                            <h3 class="font-semibold text-green-900 dark:text-green-100 mb-2">
                                <i class="fas fa-lightbulb mr-2"></i> اقتراحات التحسين
                            </h3>
                            <pre class="text-sm whitespace-pre-wrap">${suggestions}</pre>
                        </div>
                    </div>
                `;
            }

            // عرض Code Smells
            function displayCodeSmells(data) {
                const smells = typeof data.smells === 'string' ? data.smells : JSON.stringify(data.smells, null, 2);
                
                resultsArea.innerHTML = `
                    <div class="space-y-4">
                        <div class="p-4 bg-red-50 dark:bg-red-900 rounded-lg">
                            <h3 class="font-semibold text-red-900 dark:text-red-100 mb-2">
                                <i class="fas fa-bug mr-2"></i> Code Smells المكتشفة
                            </h3>
                            <pre class="text-sm whitespace-pre-wrap">${smells}</pre>
                        </div>
                    </div>
                `;
            }

            // عرض الكود المحسّن
            function displayRefactoredCode(data) {
                const code = data.refactored_code || '';
                const explanation = data.explanation || '';
                
                refactoredCode.value = code;
                refactoredSection.classList.remove('hidden');
                
                resultsArea.innerHTML = `
                    <div class="space-y-4">
                        <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                            <h3 class="font-semibold text-green-900 dark:text-green-100 mb-2">
                                <i class="fas fa-check-circle mr-2"></i> تم التحسين بنجاح
                            </h3>
                            <p class="text-sm">تم عرض الكود المحسّن في الأسفل</p>
                        </div>
                    </div>
                `;
                
                // Scroll to refactored section
                refactoredSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            // عرض حالة التحميل
            function showLoading() {
                resultsArea.innerHTML = `
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <svg class="animate-spin h-12 w-12 text-purple-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400">جاري المعالجة...</p>
                        </div>
                    </div>
                `;
            }

            // عرض رسالة خطأ
            function showError(message) {
                resultsArea.innerHTML = `
                    <div class="p-4 bg-red-50 dark:bg-red-900 rounded-lg">
                        <h3 class="font-semibold text-red-900 dark:text-red-100 mb-2">
                            <i class="fas fa-exclamation-circle mr-2"></i> خطأ
                        </h3>
                        <p class="text-sm">${message}</p>
                    </div>
                `;
            }

            // عرض رسالة نجاح
            function showSuccess(message) {
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                toast.innerHTML = `<i class="fas fa-check-circle mr-2"></i> ${message}`;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        });
    </script>
@endpush

@extends('layouts.app')

{{--
    ملف: code-translator.blade.php
    الوصف: واجهة المستخدم الاحترافية لمترجم الأكواد (Code Translator) المدعوم بالذكاء الاصطناعي v3.15.0
    الميزات: ترجمة PHP ↔ Python/JavaScript/Java/C#، كشف تلقائي، عرض جنباً إلى جنب
    المعايير: كود نظيف ومحترف، أفضل ممارسات Laravel/Tailwind، تصميم احترافي، توثيق شامل
--}}

@section('title', 'مترجم الأكواد بالذكاء الاصطناعي - Code Translator v3.15.0')

@section('content')
    <div class="container mx-auto p-4 md:p-8">
        {{-- العنوان الرئيسي وتأثير التدرج (Gradient) --}}
        <header class="mb-8 p-6 rounded-xl shadow-2xl text-white"
                style="background-image: linear-gradient(to right, #8b5cf6, #ec4899);">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight">
                        <i class="fas fa-language mr-2"></i> مترجم الأكواد الذكي
                    </h1>
                    <p class="mt-2 text-purple-100">
                        ترجمة الأكواد بين PHP و Python/JavaScript/Java/C# باستخدام الذكاء الاصطناعي
                    </p>
                </div>
                <div class="text-right">
                    <span class="bg-white/20 px-4 py-2 rounded-lg text-sm font-semibold">
                        {{ $version ?? 'v3.15.0' }}
                    </span>
                </div>
            </div>
        </header>

        {{-- شريط التحكم باللغات --}}
        <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl mb-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                {{-- اختيار اللغة المصدر --}}
                <div class="md:col-span-5">
                    <label for="from-language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-code mr-1"></i> من اللغة
                    </label>
                    <select id="from-language" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        <option value="php">PHP</option>
                        <option value="python">Python</option>
                        <option value="javascript">JavaScript</option>
                        <option value="java">Java</option>
                        <option value="csharp">C#</option>
                        <option value="typescript">TypeScript</option>
                    </select>
                </div>

                {{-- زر التبديل --}}
                <div class="md:col-span-2 flex justify-center">
                    <button id="btn-swap" 
                            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg transform hover:scale-105">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>

                {{-- اختيار اللغة الهدف --}}
                <div class="md:col-span-5">
                    <label for="to-language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-code mr-1"></i> إلى اللغة
                    </label>
                    <select id="to-language" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        <option value="python">Python</option>
                        <option value="php">PHP</option>
                        <option value="javascript">JavaScript</option>
                        <option value="java">Java</option>
                        <option value="csharp">C#</option>
                        <option value="typescript">TypeScript</option>
                    </select>
                </div>
            </div>

            {{-- أزرار الإجراءات --}}
            <div class="mt-6 flex flex-wrap gap-3">
                <button id="btn-translate" 
                        class="flex-1 min-w-[200px] px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg font-semibold">
                    <i class="fas fa-sync-alt mr-2"></i> ترجمة الكود
                </button>
                <button id="btn-detect" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                    <i class="fas fa-search mr-2"></i> كشف تلقائي
                </button>
                <button id="btn-validate" 
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-md">
                    <i class="fas fa-check-circle mr-2"></i> التحقق من الصحة
                </button>
            </div>
        </section>

        {{-- محررات الكود (Split View) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- الكود الأصلي --}}
            <section class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-4 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-white">
                        <i class="fas fa-file-code mr-2"></i> الكود الأصلي
                    </h2>
                    <div class="flex gap-2">
                        <button id="btn-clear-original" 
                                class="px-3 py-1 bg-white/20 hover:bg-white/30 text-white rounded text-sm transition-colors"
                                title="مسح">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button id="btn-copy-original" 
                                class="px-3 py-1 bg-white/20 hover:bg-white/30 text-white rounded text-sm transition-colors"
                                title="نسخ">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <textarea id="original-code"
                              class="w-full h-96 p-4 font-mono text-sm border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-gray-50 dark:bg-gray-900 dark:text-gray-200 resize-none"
                              placeholder="// أدخل الكود هنا...

// مثال PHP:
function calculateSum($a, $b) {
    return $a + $b;
}

// مثال Python:
def calculate_sum(a, b):
    return a + b

// مثال JavaScript:
function calculateSum(a, b) {
    return a + b;
}"
                    ></textarea>
                </div>
            </section>

            {{-- الكود المترجم --}}
            <section class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-pink-600 to-pink-700 p-4 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-white">
                        <i class="fas fa-file-code mr-2"></i> الكود المترجم
                    </h2>
                    <div class="flex gap-2">
                        <button id="btn-download" 
                                class="px-3 py-1 bg-white/20 hover:bg-white/30 text-white rounded text-sm transition-colors"
                                title="تحميل">
                            <i class="fas fa-download"></i>
                        </button>
                        <button id="btn-copy-translated" 
                                class="px-3 py-1 bg-white/20 hover:bg-white/30 text-white rounded text-sm transition-colors"
                                title="نسخ">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <textarea id="translated-code"
                              class="w-full h-96 p-4 font-mono text-sm border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-pink-500 focus:border-pink-500 bg-gray-50 dark:bg-gray-900 dark:text-gray-200 resize-none"
                              placeholder="// الكود المترجم سيظهر هنا..."
                              readonly
                    ></textarea>
                </div>
            </section>
        </div>

        {{-- منطقة الملاحظات والنتائج --}}
        <section class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">
                <i class="fas fa-info-circle mr-2"></i> ملاحظات الترجمة والنتائج
            </h2>
            <div id="results-area"
                 class="min-h-[200px] p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900">
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <i class="fas fa-info-circle text-4xl mb-3"></i>
                    <p>النتائج والملاحظات ستظهر هنا بعد الترجمة</p>
                </div>
            </div>
        </section>

        {{-- معلومات إضافية --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 p-4 rounded-lg">
                <div class="flex items-center mb-2">
                    <i class="fas fa-check-circle text-purple-600 text-xl mr-2"></i>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">دقة عالية</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">ترجمة دقيقة مع الحفاظ على المنطق البرمجي</p>
            </div>
            
            <div class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-900/20 dark:to-pink-800/20 p-4 rounded-lg">
                <div class="flex items-center mb-2">
                    <i class="fas fa-bolt text-pink-600 text-xl mr-2"></i>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">سرعة فائقة</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">ترجمة فورية باستخدام الذكاء الاصطناعي</p>
            </div>
            
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-4 rounded-lg">
                <div class="flex items-center mb-2">
                    <i class="fas fa-code text-blue-600 text-xl mr-2"></i>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">6 لغات مدعومة</h3>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">PHP, Python, JavaScript, Java, C#, TypeScript</p>
            </div>
        </div>
    </div>
@endsection

{{-- قسم JavaScript للوظائف التفاعلية --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // العناصر
            const fromLangSelect = document.getElementById('from-language');
            const toLangSelect = document.getElementById('to-language');
            const originalCodeArea = document.getElementById('original-code');
            const translatedCodeArea = document.getElementById('translated-code');
            const resultsArea = document.getElementById('results-area');
            
            // الأزرار
            const btnTranslate = document.getElementById('btn-translate');
            const btnDetect = document.getElementById('btn-detect');
            const btnValidate = document.getElementById('btn-validate');
            const btnSwap = document.getElementById('btn-swap');
            const btnCopyOriginal = document.getElementById('btn-copy-original');
            const btnCopyTranslated = document.getElementById('btn-copy-translated');
            const btnClearOriginal = document.getElementById('btn-clear-original');
            const btnDownload = document.getElementById('btn-download');

            // CSRF Token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // وظيفة عرض حالة التحميل
            function showLoading(message = 'جاري المعالجة...') {
                resultsArea.innerHTML = `
                    <div class="flex items-center justify-center py-8">
                        <svg class="animate-spin h-8 w-8 text-purple-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-lg text-gray-700 dark:text-gray-300">${message}</span>
                    </div>
                `;
            }

            // وظيفة عرض النتائج
            function showResults(data, type = 'success') {
                let html = '';
                
                if (type === 'success') {
                    html = `
                        <div class="space-y-4">
                            <div class="flex items-center text-green-600 dark:text-green-400 mb-4">
                                <i class="fas fa-check-circle text-2xl mr-2"></i>
                                <span class="text-lg font-semibold">تمت العملية بنجاح</span>
                            </div>
                    `;
                    
                    if (data.notes) {
                        html += `
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">
                                    <i class="fas fa-sticky-note mr-2"></i>ملاحظات الترجمة:
                                </h4>
                                <div class="text-sm text-blue-800 dark:text-blue-200 whitespace-pre-line">${data.notes}</div>
                            </div>
                        `;
                    }
                    
                    if (data.comparison) {
                        const comp = data.comparison;
                        html += `
                            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                                <h4 class="font-semibold text-purple-900 dark:text-purple-100 mb-3">
                                    <i class="fas fa-chart-bar mr-2"></i>إحصائيات المقارنة:
                                </h4>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded">
                                        <div class="text-gray-600 dark:text-gray-400">عدد الأسطر</div>
                                        <div class="text-lg font-bold text-purple-600">${comp.original_lines} → ${comp.translated_lines}</div>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded">
                                        <div class="text-gray-600 dark:text-gray-400">حجم الكود</div>
                                        <div class="text-lg font-bold text-purple-600">${comp.original_size} → ${comp.translated_size}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    if (data.from_cache) {
                        html += `
                            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                <i class="fas fa-bolt mr-1"></i>
                                تم الحصول على النتيجة من الذاكرة المؤقتة (أسرع)
                            </div>
                        `;
                    }
                    
                    html += `</div>`;
                } else {
                    html = `
                        <div class="flex items-center text-red-600 dark:text-red-400">
                            <i class="fas fa-exclamation-circle text-2xl mr-2"></i>
                            <div>
                                <div class="text-lg font-semibold">حدث خطأ</div>
                                <div class="text-sm mt-1">${data.message || 'حدث خطأ غير متوقع'}</div>
                            </div>
                        </div>
                    `;
                }
                
                resultsArea.innerHTML = html;
            }

            // وظيفة إرسال الطلب
            async function sendRequest(action, additionalData = {}) {
                const code = originalCodeArea.value.trim();
                
                if (!code) {
                    showResults({ message: 'الرجاء إدخال الكود أولاً' }, 'error');
                    return;
                }
                
                showLoading();
                
                try {
                    const response = await fetch('{{ route("developer.ai.code-translator.post") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            code: code,
                            from_language: fromLangSelect.value,
                            to_language: toLangSelect.value,
                            action: action,
                            ...additionalData
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        if (action === 'translate' && data.data.translated_code) {
                            translatedCodeArea.value = data.data.translated_code;
                        }
                        
                        if (action === 'detect' && data.data.language) {
                            fromLangSelect.value = data.data.language;
                            showResults({
                                notes: `تم الكشف عن اللغة: ${data.data.language_name}\nنسبة الثقة: ${data.data.confidence}%`
                            }, 'success');
                            return;
                        }
                        
                        if (action === 'validate') {
                            const validMsg = data.data.valid ? 
                                `✓ الكود صحيح: ${data.data.message}` : 
                                `✗ الكود غير صحيح: ${data.data.message}`;
                            showResults({ notes: validMsg }, data.data.valid ? 'success' : 'error');
                            return;
                        }
                        
                        showResults(data.data, 'success');
                    } else {
                        showResults(data, 'error');
                    }
                } catch (error) {
                    showResults({ message: 'فشل الاتصال بالخادم: ' + error.message }, 'error');
                }
            }

            // معالجات الأحداث
            btnTranslate.addEventListener('click', () => sendRequest('translate'));
            btnDetect.addEventListener('click', () => sendRequest('detect'));
            btnValidate.addEventListener('click', () => sendRequest('validate'));
            
            btnSwap.addEventListener('click', () => {
                const temp = fromLangSelect.value;
                fromLangSelect.value = toLangSelect.value;
                toLangSelect.value = temp;
            });
            
            btnCopyOriginal.addEventListener('click', () => {
                originalCodeArea.select();
                document.execCommand('copy');
                showNotification('تم النسخ!');
            });
            
            btnCopyTranslated.addEventListener('click', () => {
                translatedCodeArea.select();
                document.execCommand('copy');
                showNotification('تم النسخ!');
            });
            
            btnClearOriginal.addEventListener('click', () => {
                if (confirm('هل تريد مسح الكود؟')) {
                    originalCodeArea.value = '';
                    translatedCodeArea.value = '';
                    resultsArea.innerHTML = `
                        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                            <i class="fas fa-info-circle text-4xl mb-3"></i>
                            <p>النتائج والملاحظات ستظهر هنا بعد الترجمة</p>
                        </div>
                    `;
                }
            });
            
            btnDownload.addEventListener('click', () => {
                const code = translatedCodeArea.value;
                if (!code) {
                    alert('لا يوجد كود للتحميل');
                    return;
                }
                
                const ext = toLangSelect.value === 'csharp' ? 'cs' : 
                           toLangSelect.value === 'typescript' ? 'ts' : 
                           toLangSelect.value;
                const blob = new Blob([code], { type: 'text/plain' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `translated_code.${ext}`;
                a.click();
                URL.revokeObjectURL(url);
                showNotification('تم التحميل!');
            });
            
            // وظيفة الإشعارات
            function showNotification(message) {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-down';
                notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        @keyframes fade-in-down {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-down {
            animation: fade-in-down 0.3s ease-out;
        }
    </style>
@endpush

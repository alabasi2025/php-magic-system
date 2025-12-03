@extends('layouts.app')

@section('title', 'مُعيد الهيكلة الذكي')

@section('content')
    <div class="container mx-auto p-4 md:p-8">

        <!-- زر العودة -->
        <div class="mb-6">
            <a href="{{ route('ai-tools.dashboard') }}"
               class="inline-flex items-center text-gray-600 hover:text-indigo-600 transition duration-150 ease-in-out">
                <i class="fas fa-arrow-left ml-2"></i>
                العودة إلى لوحة أدوات الذكاء الاصطناعي
            </a>
        </div>

        <!-- العنوان والوصف -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-3">
                <i class="fas fa-code-branch text-indigo-600 mr-2"></i>
                مُعيد الهيكلة الذكي
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                يحلل الكود البرمجي الخاص بك ويقترح إعادة هيكلة شاملة لتحسين القراءة، تقليل التعقيد، ورفع مستوى الصيانة.
            </p>
        </div>

        <!-- بطاقات الإحصائيات (Stats Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <!-- بطاقة تحسين القراءة -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-indigo-500">
                <div class="flex items-center">
                    <i class="fas fa-chart-line text-3xl text-indigo-500 mr-4"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">تحسين القراءة</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">95%</p>
                    </div>
                </div>
            </div>

            <!-- بطاقة تقليل التعقيد -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-purple-500">
                <div class="flex items-center">
                    <i class="fas fa-puzzle-piece text-3xl text-purple-500 mr-4"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">تقليل التعقيد</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">40%</p>
                    </div>
                </div>
            </div>

            <!-- بطاقة سرعة المعالجة -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border-t-4 border-pink-500">
                <div class="flex items-center">
                    <i class="fas fa-tachometer-alt text-3xl text-pink-500 mr-4"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">سرعة المعالجة</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">2 ثانية</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج الإدخال التفاعلي -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 md:p-10 border border-gray-200 dark:border-gray-700">
            <form action="#" method="POST" id="refactoring-form">
                @csrf

                <!-- حقل إدخال الكود -->
                <div class="mb-6">
                    <label for="source_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الكود البرمجي الأصلي
                    </label>
                    <textarea id="source_code" name="source_code" rows="15"
                              class="w-full p-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-inner focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50 dark:bg-gray-900 dark:text-white font-mono text-sm"
                              placeholder="الصق الكود الذي تريد إعادة هيكلته هنا..."></textarea>
                </div>

                <!-- خيارات إضافية -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- لغة البرمجة -->
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            لغة البرمجة
                        </label>
                        <select id="language" name="language"
                                class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-900 dark:text-white">
                            <option value="php">PHP</option>
                            <option value="javascript">JavaScript</option>
                            <option value="python">Python</option>
                            <option value="java">Java</option>
                            <option value="csharp">C#</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>

                    <!-- مستوى التحسين المطلوب -->
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            مستوى التحسين المطلوب
                        </label>
                        <select id="level" name="level"
                                class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-900 dark:text-white">
                            <option value="readability">تحسين القراءة فقط</option>
                            <option value="performance">تحسين الأداء</option>
                            <option value="full">هيكلة شاملة (القراءة والأداء)</option>
                        </select>
                    </div>
                </div>

                <!-- زر الإرسال مع تدرج لوني جذاب -->
                <button type="submit"
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-xl text-lg font-semibold text-white
                               bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700
                               focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out transform hover:scale-[1.01]">
                    <i class="fas fa-magic ml-2"></i>
                    إعادة الهيكلة الآن
                </button>
            </form>
        </div>

        <!-- منطقة عرض النتيجة (مخفية افتراضياً) -->
        <div id="result-area" class="mt-10 hidden">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 border-b pb-2">
                الكود بعد إعادة الهيكلة
            </h2>
            <div class="bg-gray-900 rounded-xl shadow-2xl overflow-hidden">
                <div class="flex justify-between items-center p-3 bg-gray-800 border-b border-gray-700">
                    <span class="text-sm text-indigo-400 font-mono">refactored_code.php</span>
                    <button id="copy-button" class="text-gray-400 hover:text-white text-sm">
                        <i class="fas fa-copy ml-1"></i>
                        نسخ الكود
                    </button>
                </div>
                <pre class="p-4 overflow-x-auto text-sm text-green-400 font-mono" dir="ltr">
                    <code id="refactored-code">
// سيتم عرض الكود المُعاد هيكلته هنا بعد المعالجة
// مثال:
// function calculateTotal(items) {
//   let total = 0;
//   for (const item of items) {
//     total += item.price * item.quantity;
//   }
//   return total;
// }
                    </code>
                </pre>
            </div>
        </div>

    </div>

    <!-- سكريبت بسيط لمحاكاة التفاعل والنسخ -->
    <script>
        document.getElementById('refactoring-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const resultArea = document.getElementById('result-area');
            const refactoredCode = document.getElementById('refactored-code');
            const submitButton = e.target.querySelector('button[type="submit"]');

            // محاكاة عملية المعالجة
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري المعالجة...';
            submitButton.disabled = true;

            setTimeout(() => {
                // محاكاة الكود المُعاد هيكلته
                const sourceCode = document.getElementById('source_code').value;
                if (sourceCode.trim() === '') {
                    refactoredCode.textContent = '// يرجى إدخال كود برمجي لإعادة هيكلته.';
                } else {
                    refactoredCode.textContent = '// هذا هو الكود المُعاد هيكلته بواسطة الذكاء الاصطناعي:\n' + sourceCode.split('\n').map(line => '  ' + line).join('\n');
                }

                submitButton.innerHTML = '<i class="fas fa-magic ml-2"></i> إعادة الهيكلة الآن';
                submitButton.disabled = false;
                resultArea.classList.remove('hidden');
                resultArea.scrollIntoView({ behavior: 'smooth' });
            }, 2000);
        });

        document.getElementById('copy-button').addEventListener('click', function() {
            const code = document.getElementById('refactored-code').textContent;
            navigator.clipboard.writeText(code).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check ml-1"></i> تم النسخ!';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 1500);
            }).catch(err => {
                console.error('فشل في النسخ: ', err);
            });
        });
    </script>
@endsection
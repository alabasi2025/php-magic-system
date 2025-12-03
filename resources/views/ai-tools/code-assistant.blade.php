@extends('layouts.app')

@section('title', 'مساعد الكود الذكي')

@section('content')
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <!-- زر العودة -->
        <div class="mb-6">
            <a href="{{ route('ai-tools.dashboard') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out">
                <i class="fas fa-arrow-right mr-2 transform rotate-180"></i>
                العودة إلى الأدوات
            </a>
        </div>

        <!-- العنوان والوصف -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-3">
                <i class="fas fa-code text-indigo-600 dark:text-indigo-400"></i>
                مساعد الكود الذكي
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                نظام إكمال تلقائي متقدم يعمل في الوقت الفعلي أثناء كتابة الكود، لزيادة إنتاجيتك وتقليل الأخطاء البرمجية.
            </p>
        </div>

        <!-- بطاقات الإحصائيات (Stats Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <!-- البطاقة 1: سرعة الإكمال -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transform hover:scale-[1.02] transition duration-300 ease-in-out">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                            <i class="fas fa-bolt fa-lg"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">سرعة الإكمال</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">500 مللي ثانية</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- البطاقة 2: اللغات المدعومة -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transform hover:scale-[1.02] transition duration-300 ease-in-out">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-gradient-to-br from-green-500 to-teal-600 text-white">
                            <i class="fas fa-language fa-lg"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">اللغات المدعومة</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">+20 لغة</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- البطاقة 3: توفير الوقت -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transform hover:scale-[1.02] transition duration-300 ease-in-out">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-gradient-to-br from-yellow-500 to-orange-600 text-white">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">توفير الوقت المقدر</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">30%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج الإدخال التفاعلي للأداة -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-8 lg:p-10">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-3 border-gray-200 dark:border-gray-700">
                <i class="fas fa-keyboard text-indigo-600 dark:text-indigo-400 mr-2"></i>
                تجربة مساعد الكود الذكي
            </h2>

            <form action="#" method="POST">
                <!-- حقل اختيار اللغة -->
                <div class="mb-6">
                    <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        لغة البرمجة
                    </label>
                    <select id="language" name="language"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option>JavaScript</option>
                        <option>Python</option>
                        <option>PHP (Laravel)</option>
                        <option>HTML/CSS</option>
                        <option>Java</option>
                    </select>
                </div>

                <!-- حقل إدخال الكود (منطقة النص) -->
                <div class="mb-6">
                    <label for="code_input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        أدخل الكود هنا (ابدأ بالكتابة وشاهد الإكمال التلقائي)
                    </label>
                    <textarea id="code_input" name="code_input" rows="10"
                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-4 font-mono text-lg dark:bg-gray-900 dark:border-gray-700 dark:text-green-400"
                              placeholder="مثال: function calculateSum(a, b) { ... "></textarea>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        هذا الحقل يحاكي بيئة التطوير، حيث سيظهر الإكمال التلقائي المقترح أثناء الكتابة.
                    </p>
                </div>

                <!-- زر التنفيذ (للتجربة) -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-xl text-white bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 ease-in-out transform hover:scale-[1.01]">
                        <i class="fas fa-magic mr-2"></i>
                        توليد الكود المقترح
                    </button>
                </div>
            </form>

            <!-- منطقة عرض النتيجة (الإكمال المقترح) -->
            <div class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                    الإكمال المقترح (النتيجة)
                </h3>
                <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto text-sm border border-dashed border-indigo-300 dark:border-indigo-700">
                    <code class="text-gray-800 dark:text-gray-200">
// سيظهر هنا الكود المقترح من مساعد الكود الذكي بناءً على إدخالك
// مثال:
// function calculateSum(a, b) {
//     return a + b;
// }
                    </code>
                </pre>
            </div>
        </div>
    </div>
@endsection

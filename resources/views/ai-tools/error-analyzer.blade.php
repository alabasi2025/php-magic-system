@extends('layouts.app')

@section('title', 'محلل الأخطاء الذكي')

@section('content')
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">

        {{-- زر العودة --}}
        <div class="mb-6">
            <a href="{{ route('ai-tools.dashboard') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out"
               dir="rtl">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة إلى لوحة أدوات الذكاء الاصطناعي
            </a>
        </div>

        {{-- العنوان والوصف مع التدرج اللوني --}}
        <header class="text-center mb-10 p-6 rounded-xl shadow-2xl bg-white dark:bg-gray-800"
                style="background-image: linear-gradient(to right, #4c51bf, #6b46c1); color: white;">
            <div class="flex items-center justify-center mb-3">
                <i class="fas fa-bug text-4xl mr-3"></i>
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                    محلل الأخطاء الذكي
                </h1>
            </div>
            <p class="mt-3 max-w-3xl mx-auto text-xl font-light">
                <i class="fas fa-microchip mr-2"></i>
                يحلل الأخطاء البرمجية أو سجلات النظام بدقة متناهية، ويقدم تشخيصاً دقيقاً مع خطوات إصلاح مفصلة وموثوقة.
            </p>
        </header>

        {{-- بطاقات الإحصائيات (Stats Cards) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 text-right">
            {{-- بطاقة 1: الأخطاء التي تم تحليلها --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl p-6 border-r-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <i class="fas fa-chart-line text-3xl text-indigo-500"></i>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">أخطاء تم تحليلها</p>
                </div>
                <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">12,450</p>
            </div>

            {{-- بطاقة 2: متوسط وقت التحليل --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl p-6 border-r-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <i class="fas fa-clock text-3xl text-purple-500"></i>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">متوسط وقت التحليل</p>
                </div>
                <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">4.2 ثانية</p>
            </div>

            {{-- بطاقة 3: نسبة التشخيص الصحيح --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl p-6 border-r-4 border-green-500">
                <div class="flex items-center justify-between">
                    <i class="fas fa-check-circle text-3xl text-green-500"></i>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">نسبة التشخيص الصحيح</p>
                </div>
                <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">98.7%</p>
            </div>
        </div>

        {{-- نموذج الإدخال التفاعلي --}}
        <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-xl p-8 mb-10">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-3 border-gray-200 dark:border-gray-700">
                <i class="fas fa-code-branch ml-2 text-indigo-600"></i>
                أدخل الخطأ للتحليل
            </h2>

            <form action="#" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="error_input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الصق الكود أو سجل الخطأ (Log) هنا:
                    </label>
                    <textarea id="error_input" name="error_input" rows="10"
                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition duration-150 ease-in-out p-4"
                              placeholder="مثال: Fatal error: Uncaught Error: Call to undefined function App\Http\Controllers\nonExistentFunction() in..."></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-105">
                        <i class="fas fa-search ml-2"></i>
                        تحليل الخطأ الآن
                    </button>
                </div>
            </form>
        </div>

        {{-- قسم نتائج التحليل (Placeholder) --}}
        <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-3 border-gray-200 dark:border-gray-700">
                <i class="fas fa-wrench ml-2 text-green-600"></i>
                نتائج التشخيص والإصلاح
            </h2>

            <div id="analysis_results" class="text-gray-600 dark:text-gray-400">
                <p class="text-lg italic">
                    سيظهر هنا التشخيص الدقيق للخطأ وخطوات الإصلاح المفصلة بعد إرسال النموذج.
                </p>

                {{-- مثال على بطاقة النتيجة (يمكن إظهارها بعد التحليل) --}}
                <div class="mt-6 p-4 border-l-4 border-green-500 bg-green-50 dark:bg-green-900/20 rounded-md hidden">
                    <h3 class="text-xl font-semibold text-green-800 dark:text-green-300 mb-2">التشخيص:</h3>
                    <p class="mb-4">
                        الخطأ هو "Call to undefined function" ويشير إلى أن الدالة `nonExistentFunction` غير معرفة في نطاق المتحكم.
                    </p>
                    <h3 class="text-xl font-semibold text-green-800 dark:text-green-300 mb-2">خطوات الإصلاح:</h3>
                    <ol class="list-decimal list-inside space-y-2 text-green-700 dark:text-green-400">
                        <li>تأكد من أن اسم الدالة مكتوب بشكل صحيح.</li>
                        <li>إذا كانت الدالة موجودة في ملف آخر، تأكد من تضمين (Include) الملف بشكل صحيح.</li>
                        <li>إذا كانت الدالة جزءًا من فئة (Class)، استخدم صيغة الاستدعاء الصحيحة للكائنات (`$this->function()`) أو الثوابت (`ClassName::function()`).</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>
@endsection
@extends('layouts.app')

@section('title', 'مكتشف الثغرات الأمنية الذكي')

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header and Back Button -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">مكتشف الثغرات الأمنية الذكي</h1>
        <a href="{{ route('ai-tools.dashboard') }}" class="flex items-center text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition duration-150 ease-in-out">
            <i class="fas fa-arrow-right-to-bracket fa-flip-horizontal ml-2"></i>
            العودة إلى الأدوات
        </a>
    </div>

    <!-- Tool Description -->
    <div class="mb-8 p-6 rounded-xl shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
            <i class="fas fa-shield-halved text-indigo-500 ml-2"></i>
            هذه الأداة المتقدمة تستخدم الذكاء الاصطناعي لفحص الكود البرمجي الخاص بك بدقة عالية، وتحديد الثغرات الأمنية المحتملة، وتقديم اقتراحات فورية ومفصلة لإصلاح هذه الثغرات لضمان أمان تطبيقك.
        </p>
    </div>

    <!-- Stats Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Card 1: Total Scans -->
        <div class="p-5 rounded-xl shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 transform hover:scale-[1.02] transition duration-300 ease-in-out">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400">
                    <i class="fas fa-chart-line fa-lg"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي عمليات الفحص</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">1,245</p>
                </div>
            </div>
        </div>

        <!-- Card 2: High-Risk Vulnerabilities -->
        <div class="p-5 rounded-xl shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 transform hover:scale-[1.02] transition duration-300 ease-in-out">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400">
                    <i class="fas fa-skull-crossbones fa-lg"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ثغرات عالية الخطورة (مكتشفة)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">42</p>
                </div>
            </div>
        </div>

        <!-- Card 3: Fixed Issues -->
        <div class="p-5 rounded-xl shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 transform hover:scale-[1.02] transition duration-300 ease-in-out">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400">
                    <i class="fas fa-check-double fa-lg"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">مشاكل تم إصلاحها</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">987</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Input Form Section with Gradient -->
    <div class="relative p-1 rounded-2xl shadow-2xl overflow-hidden">
        <!-- Gradient Border/Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 opacity-75 dark:opacity-50 blur-lg"></div>
        
        <!-- Content Card -->
        <div class="relative p-6 sm:p-8 lg:p-10 bg-white dark:bg-gray-900 rounded-xl">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <i class="fas fa-code fa-lg text-indigo-500 ml-3"></i>
                إدخال الكود للفحص الأمني
            </h2>

            <form action="#" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="code_input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الصق الكود البرمجي هنا (يدعم لغات متعددة)
                    </label>
                    <textarea id="code_input" name="code_input" rows="15" class="w-full p-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-inner focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-150 ease-in-out" placeholder="أدخل الكود البرمجي الذي تريد فحصه أمنياً..."></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-3 text-lg font-semibold text-white rounded-lg shadow-lg transition duration-300 ease-in-out 
                                                 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 
                                                 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50">
                        <i class="fas fa-magnifying-glass-chart ml-2"></i>
                        بدء الفحص الأمني
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Placeholder for Results Section -->
    <div class="mt-10 p-6 rounded-xl shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-file-shield fa-lg text-green-500 ml-3"></i>
            نتائج الفحص الأمني
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
            ستظهر نتائج الفحص الأمني هنا بعد إرسال الكود. ستتضمن النتائج قائمة بالثغرات المكتشفة، مستوى خطورتها، واقتراحات الإصلاح المدعومة بالذكاء الاصطناعي.
        </p>
    </div>

</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header and Description -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center">
            <i class="fas fa-vial fa-3x text-indigo-500 mr-4" style="text-shadow: 0 0 10px rgba(99, 102, 241, 0.5);"></i>
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white">
                    مولد اختبارات ذكي متقدم
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 mt-2 max-w-3xl">
                    استخدم الذكاء الاصطناعي لتوليد اختبارات (Unit, Feature, Integration) تلقائياً لأي كود أو متطلبات، مما يضمن جودة عالية وتغطية شاملة.
                </p>
            </div>
        </div>
        <a href="{{ route('ai-tools.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out flex items-center">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للأدوات
        </a>
    </div>

    <!-- Stats Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Card 1: Tests Generated Today -->
        <div class="p-6 rounded-xl shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 transform hover:scale-[1.02] transition duration-300 ease-in-out">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-lg">
                    <i class="fas fa-chart-line fa-lg"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">اختبارات اليوم</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">1,245</p>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Tests Generated -->
        <div class="p-6 rounded-xl shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 transform hover:scale-[1.02] transition duration-300 ease-in-out">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-green-500 to-teal-600 text-white shadow-lg">
                    <i class="fas fa-cogs fa-lg"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي الاختبارات</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">87,542</p>
                </div>
            </div>
        </div>

        <!-- Card 3: Success Rate -->
        <div class="p-6 rounded-xl shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 transform hover:scale-[1.02] transition duration-300 ease-in-out">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-red-500 to-pink-600 text-white shadow-lg">
                    <i class="fas fa-percent fa-lg"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">معدل النجاح (المتوسط)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">98.7%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Tool Interface -->
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700">
        <form action="#" method="POST">
            @csrf
            
            <!-- Input Area -->
            <div class="mb-6">
                <label for="requirements" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-code mr-1 text-indigo-500"></i>
                    الكود أو المتطلبات التفصيلية
                </label>
                <textarea id="requirements" name="requirements" rows="10" class="w-full p-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-inner focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-300" placeholder="أدخل الكود المراد اختباره، أو وصفاً مفصلاً للميزة التي تريد توليد اختبارات لها (مثال: دالة Laravel لمعالجة طلبات المستخدمين)."></textarea>
            </div>

            <!-- Options Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Test Type -->
                <div>
                    <label for="test_type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-filter mr-1 text-green-500"></i>
                        نوع الاختبار المطلوب
                    </label>
                    <select id="test_type" name="test_type" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-300">
                        <option value="unit">Unit Test (اختبار الوحدة)</option>
                        <option value="feature">Feature Test (اختبار الميزة)</option>
                        <option value="integration">Integration Test (اختبار التكامل)</option>
                    </select>
                </div>

                <!-- Framework/Language -->
                <div>
                    <label for="framework" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-laptop-code mr-1 text-red-500"></i>
                        اللغة/الإطار البرمجي
                    </label>
                    <select id="framework" name="framework" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-300">
                        <option value="laravel">Laravel/PHPUnit</option>
                        <option value="react">React/Jest</option>
                        <option value="python">Python/Pytest</option>
                        <option value="other">أخرى...</option>
                    </select>
                </div>

                <!-- Verbosity Level -->
                <div>
                    <label for="verbosity" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-sliders-h mr-1 text-yellow-500"></i>
                        مستوى التفصيل
                    </label>
                    <select id="verbosity" name="verbosity" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-300">
                        <option value="normal">عادي (Normal)</option>
                        <option value="detailed">مفصل (Detailed)</option>
                        <option value="minimal">مختصر (Minimal)</option>
                    </select>
                </div>
            </div>

            <!-- Generate Button -->
            <div class="text-center">
                <button type="submit" class="w-full md:w-1/2 py-4 px-6 text-xl font-bold text-white rounded-xl shadow-2xl transition duration-500 ease-in-out transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
                    <i class="fas fa-robot ml-2"></i>
                    توليد الاختبارات الآن
                </button>
            </div>
        </form>
    </div>

    <!-- Output Area Placeholder -->
    <div class="mt-10">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-file-code mr-2 text-teal-500"></i>
            نتائج الاختبارات المولدة
        </h2>
        <div class="bg-gray-100 dark:bg-gray-900 p-6 rounded-xl shadow-inner border border-gray-300 dark:border-gray-700 min-h-[300px] flex items-center justify-center">
            <p class="text-gray-500 dark:text-gray-400 text-lg">
                سيظهر الكود الخاص بالاختبارات المولدة هنا بعد الضغط على زر "توليد الاختبارات الآن".
            </p>
        </div>
    </div>

</div>
@endsection
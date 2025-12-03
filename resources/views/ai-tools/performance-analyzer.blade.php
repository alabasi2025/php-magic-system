@extends('layouts.app')

@section('title', 'محلل الأداء الذكي')

@section('content')
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <!-- زر العودة -->
        <div class="mb-6">
            <a href="{{ route('ai-tools.dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-indigo-600 transition duration-150 ease-in-out">
                <i class="fas fa-arrow-right-to-bracket fa-rotate-180 ml-2"></i>
                العودة إلى لوحة الأدوات
            </a>
        </div>

        <!-- بطاقة الأداة الرئيسية -->
        <div class="bg-white shadow-2xl rounded-xl overflow-hidden transform transition duration-500 hover:scale-[1.01] border border-gray-100">
            <!-- رأس البطاقة مع التدرج اللوني -->
            <div class="p-6 sm:p-8 bg-gradient-to-r from-indigo-600 to-purple-700 text-white">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl sm:text-4xl font-extrabold flex items-center">
                        <i class="fas fa-tachometer-alt fa-fw mr-3 text-indigo-200"></i>
                        محلل الأداء الذكي
                    </h1>
                    <span class="text-sm font-medium bg-white bg-opacity-20 px-3 py-1 rounded-full">AI Performance Analyzer</span>
                </div>
                <p class="mt-3 text-indigo-100 text-lg">
                    يحلل الكود الخاص بك بعمق، ويكتشف نقاط الضعف في الأداء، ويقدم اقتراحات فورية ومدروسة لتحسين الكفاءة والسرعة.
                </p>
            </div>

            <!-- محتوى البطاقة - نموذج الإدخال -->
            <div class="p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">تحليل الكود</h2>

                <form action="#" method="POST" class="space-y-6">
                    <!-- حقل إدخال الكود -->
                    <div>
                        <label for="code_input" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-code fa-fw ml-1 text-indigo-500"></i>
                            الصق الكود المراد تحليله هنا:
                        </label>
                        <textarea id="code_input" name="code_input" rows="15" class="mt-1 block w-full p-4 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono" placeholder="أدخل كود PHP، JavaScript، Python، أو أي لغة أخرى..."></textarea>
                    </div>

                    <!-- خيارات التحليل (Stats Cards - افتراضية) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- بطاقة مقياس التعقيد -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-md border-r-4 border-indigo-500">
                            <div class="flex items-center">
                                <i class="fas fa-brain fa-fw text-2xl text-indigo-600 mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">مقياس التعقيد</p>
                                    <p class="text-xl font-semibold text-gray-900">Cyclomatic Complexity</p>
                                </div>
                            </div>
                        </div>
                        <!-- بطاقة استهلاك الذاكرة -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-md border-r-4 border-purple-500">
                            <div class="flex items-center">
                                <i class="fas fa-memory fa-fw text-2xl text-purple-600 mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">تقدير الذاكرة</p>
                                    <p class="text-xl font-semibold text-gray-900">Memory Usage</p>
                                </div>
                            </div>
                        </div>
                        <!-- بطاقة زمن التنفيذ -->
                        <div class="bg-gray-50 p-4 rounded-lg shadow-md border-r-4 border-pink-500">
                            <div class="flex items-center">
                                <i class="fas fa-clock fa-fw text-2xl text-pink-600 mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">زمن التنفيذ المتوقع</p>
                                    <p class="text-xl font-semibold text-gray-900">Execution Time</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- زر التحليل -->
                    <div>
                        <button type="submit" class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-bold rounded-lg shadow-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-[1.005]">
                            <i class="fas fa-cogs fa-fw ml-2"></i>
                            بدء التحليل الذكي
                        </button>
                    </div>
                </form>

                <!-- قسم النتائج (افتراضي) -->
                <div class="mt-10 pt-6 border-t border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line fa-fw mr-2 text-green-600"></i>
                        نتائج التحليل والاقتراحات
                    </h2>
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                        <p class="text-green-800 font-medium">
                            <i class="fas fa-check-circle fa-fw ml-1"></i>
                            سيتم عرض تقرير الأداء المفصل والاقتراحات هنا بعد إرسال الكود للتحليل.
                        </p>
                        <ul class="mt-3 text-sm text-green-700 list-disc pr-5 space-y-1">
                            <li>نقاط الضعف المكتشفة (Bottlenecks)</li>
                            <li>تحسينات مقترحة للكود (Refactoring Suggestions)</li>
                            <li>مقارنة بين الأداء الحالي والمحسن (Performance Comparison)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
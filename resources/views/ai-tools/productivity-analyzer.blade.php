@extends('layouts.app')

@section('title', 'محلل الإنتاجية الذكي')

@section('content')
    <div class="container mx-auto px-4 py-8">

        <!-- Header Section with Gradient and Back Button -->
        <div class="bg-white shadow-xl rounded-2xl p-6 mb-8 border-t-4 border-indigo-500">
            <div class="flex justify-between items-start mb-4">
                <h1 class="text-4xl font-extrabold text-gray-900 flex items-center">
                    <i class="fa-solid fa-chart-line text-indigo-600 mr-3"></i>
                    محلل الإنتاجية الذكي
                </h1>
                <a href="{{ route('ai-tools.dashboard') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out">
                    <i class="fa-solid fa-arrow-right-to-bracket rotate-180 ml-2"></i>
                    العودة للأدوات
                </a>
            </div>

            <!-- Tool Description -->
            <p class="text-lg text-gray-600 border-r-4 border-indigo-400 pr-4">
                هذه الأداة المبتكرة تقوم بتحليل أنماط عملك كمطور، مثل أوقات التركيز، فترات الراحة، وأنواع المهام المنجزة. بناءً على هذا التحليل، تقدم الأداة اقتراحات شخصية ومدروسة لتحسين إنتاجيتك ورفع كفاءة عملك اليومي.
            </p>
        </div>

        <!-- Stats Cards Section (Illustrative Metrics) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <!-- Card 1: Focus Time -->
            <div class="bg-white rounded-xl shadow-lg p-6 transform hover:scale-[1.02] transition duration-300 ease-in-out">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                        <i class="fa-solid fa-clock fa-xl"></i>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-500">متوسط وقت التركيز</p>
                        <p class="text-3xl font-bold text-gray-900">3.5 ساعة</p>
                    </div>
                </div>
            </div>

            <!-- Card 2: Productivity Score -->
            <div class="bg-white rounded-xl shadow-lg p-6 transform hover:scale-[1.02] transition duration-300 ease-in-out">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fa-solid fa-star fa-xl"></i>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-500">نقاط الإنتاجية الأسبوعية</p>
                        <p class="text-3xl font-bold text-gray-900">85/100</p>
                    </div>
                </div>
            </div>

            <!-- Card 3: Completed Tasks -->
            <div class="bg-white rounded-xl shadow-lg p-6 transform hover:scale-[1.02] transition duration-300 ease-in-out">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fa-solid fa-check-double fa-xl"></i>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-500">مهام مكتملة (آخر 7 أيام)</p>
                        <p class="text-3xl font-bold text-gray-900">42 مهمة</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Input Form and Analysis Area -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">إدخال بيانات التحليل</h2>

            <form action="#" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="activity_log" class="block text-sm font-medium text-gray-700 mb-2">
                        سجل الأنشطة أو البيانات الخام للمطور (لتحليل الأنماط)
                    </label>
                    <textarea id="activity_log" name="activity_log" rows="8"
                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-lg p-4 transition duration-150 ease-in-out"
                              placeholder="أدخل سجل الأنشطة اليومية، مثل:
- 9:00 ص: بدء العمل، مراجعة البريد الإلكتروني (30 دقيقة)
- 9:30 ص: كتابة كود لميزة تسجيل الدخول (120 دقيقة)
- 11:30 ص: استراحة قصيرة (15 دقيقة)
- 11:45 ص: اجتماع فريق (60 دقيقة)
- ..."></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-lg text-white
                                   bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-[1.02]">
                        <i class="fa-solid fa-brain fa-lg ml-2"></i>
                        بدء التحليل الذكي للإنتاجية
                    </button>
                </div>
            </form>

            <!-- Placeholder for Analysis Results -->
            <div class="mt-10 pt-6 border-t border-gray-200">
                <h3 class="text-xl font-bold text-gray-800 mb-4">نتائج التحليل والاقتراحات</h3>
                <div class="bg-gray-50 p-6 rounded-lg border border-dashed border-gray-300">
                    <p class="text-gray-500 text-center">
                        سيتم عرض تقرير مفصل هنا بعد إدخال البيانات والضغط على زر "بدء التحليل".
                    </p>
                    <ul class="mt-4 space-y-2 text-gray-700 list-disc list-inside pr-4">
                        <li>تحديد فترات الذروة في التركيز.</li>
                        <li>اقتراحات مخصصة لأوقات الاستراحة المثلى.</li>
                        <li>تحليل لأنواع المهام التي تستغرق وقتًا أطول.</li>
                        <li>نصائح لتقليل عوامل التشتيت.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection
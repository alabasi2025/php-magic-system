@php
    // تحديد اللغة والاتجاه (افتراضياً العربية RTL)
    // يجب أن يتم تحديد اللغة الفعلية من إعدادات التطبيق أو الجلسة
    $lang = 'ar'; // app()->getLocale() ?? 'ar';
    $isRtl = $lang === 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $align = $isRtl ? 'text-right' : 'text-left';
    $alignOpposite = $isRtl ? 'text-left' : 'text-right';

    // النصوص المستخدمة لدعم اللغة العربية والإنجليزية
    $texts = [
        'title' => $isRtl ? 'محلل الأداء المدعوم بالذكاء الاصطناعي' : 'AI-Powered Performance Analyzer',
        'input_title' => $isRtl ? 'إدخال الكود للتحليل' : 'Code Input for Analysis',
        'input_placeholder' => $isRtl ? 'الصق كود PHP, JavaScript, SQL, إلخ هنا...' : 'Paste your PHP, JavaScript, SQL, etc. code here...',
        'analyze_button' => $isRtl ? 'تحليل الكود' : 'Analyze Code',
        'results_title' => $isRtl ? 'نتائج التحليل' : 'Analysis Results',
        'speed' => $isRtl ? 'السرعة المقدرة' : 'Estimated Speed',
        'bottlenecks' => $isRtl ? 'نقاط الاختناق (Bottlenecks)' : 'Bottlenecks',
        'suggestions' => $isRtl ? 'اقتراحات التحسين' : 'Optimization Suggestions',
        'chart_title' => $isRtl ? 'الرسم البياني للأداء' : 'Performance Chart',
        'error_title' => $isRtl ? 'خطأ في التحليل' : 'Analysis Error',
        'error_message' => $isRtl ? 'حدث خطأ أثناء محاولة تحليل الكود. يرجى التحقق من الكود والمحاولة مرة أخرى.' : 'An error occurred while trying to analyze the code. Please check the code and try again.',
    ];
@endphp

{{--
    ملف Blade View: resources/views/developer/ai/performance-analyzer.blade.php
    الغرض: واجهة مستخدم متكاملة لتحليل أداء الكود باستخدام الذكاء الاصطناعي (Manus AI).
    التقنيات: Laravel Blade, Tailwind CSS, Font Awesome, JavaScript (للتفاعل).
    المتطلبات: تصميم عصري، دعم الوضع الداكن، دعم RTL/LTR، رسوم بيانية.
    ملاحظة: يفترض وجود ملف تخطيط رئيسي (layouts.app) يتضمن Tailwind CSS و Font Awesome.
--}}

@extends('layouts.app')

@section('title', $texts['title'])

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8" dir="{{ $dir }}">
    {{-- عنوان الصفحة --}}
    <h1 class="text-3xl font-extrabold mb-6 text-gray-900 dark:text-white {{ $align }}">
        <i class="fas fa-rocket mr-3 ml-3"></i>
        {{ $texts['title'] }}
    </h1>

    {{-- بطاقة الإدخال والتحليل --}}
    <div id="input-card" class="bg-white dark:bg-gray-800 shadow-2xl rounded-xl p-6 md:p-8 mb-8 transition duration-300 ease-in-out">
        <h2 class="text-2xl font-semibold mb-4 text-gray-700 dark:text-gray-200 {{ $align }}">
            <i class="fas fa-code mr-2 ml-2"></i>
            {{ $texts['input_title'] }}
        </h2>

        <form id="analysis-form" action="{{ route('ai-tools.performance-analyzer.analyze') }}" method="POST">
            @csrf
            {{-- حقل إدخال الكود --}}
            <textarea
                id="code-input"
                name="code"
                rows="15"
                class="w-full p-4 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-mono text-sm resize-none"
                placeholder="{{ $texts['input_placeholder'] }}"
                required
            ></textarea>

            {{-- زر التحليل --}}
            <div class="mt-6 {{ $alignOpposite }}">
                <button
                    type="submit"
                    id="analyze-btn"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg transition duration-150 ease-in-out disabled:opacity-50"
                >
                    <i class="fas fa-cogs mr-2 ml-2"></i>
                    {{ $texts['analyze_button'] }}
                </button>
            </div>
        </form>
    </div>

    {{-- بطاقة نتائج التحليل (مخفية افتراضياً) --}}
    <div id="results-card" class="hidden bg-white dark:bg-gray-800 shadow-2xl rounded-xl p-6 md:p-8 mb-8 transition duration-300 ease-in-out">
        <h2 class="text-2xl font-semibold mb-6 text-green-600 dark:text-green-400 {{ $align }}">
            <i class="fas fa-chart-line mr-2 ml-2"></i>
            {{ $texts['results_title'] }}
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {{-- ملخص السرعة --}}
            <div class="bg-green-50 dark:bg-gray-700 p-4 rounded-lg shadow-md border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 {{ $align }}">{{ $texts['speed'] }}</p>
                <p id="result-speed" class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1 {{ $align }}">95%</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 {{ $align }}"><i class="fas fa-bolt"></i> {{ $isRtl ? 'أداء ممتاز' : 'Excellent Performance' }}</p>
            </div>

            {{-- ملخص نقاط الاختناق --}}
            <div class="bg-red-50 dark:bg-gray-700 p-4 rounded-lg shadow-md border-l-4 border-red-500">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 {{ $align }}">{{ $texts['bottlenecks'] }}</p>
                <p id="result-bottlenecks-count" class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1 {{ $align }}">3</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 {{ $align }}"><i class="fas fa-exclamation-triangle"></i> {{ $isRtl ? 'نقاط حرجة تحتاج لمعالجة' : 'Critical points need attention' }}</p>
            </div>

            {{-- ملخص الاقتراحات --}}
            <div class="bg-blue-50 dark:bg-gray-700 p-4 rounded-lg shadow-md border-l-4 border-blue-500">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 {{ $align }}">{{ $texts['suggestions'] }}</p>
                <p id="result-suggestions-count" class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-1 {{ $align }}">7</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 {{ $align }}"><i class="fas fa-lightbulb"></i> {{ $isRtl ? 'اقتراحات لتحسين الكفاءة' : 'Suggestions for efficiency' }}</p>
            </div>
        </div>

        {{-- تفاصيل النتائج --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- نقاط الاختناق --}}
            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg shadow-inner">
                <h3 class="text-xl font-semibold mb-3 text-red-600 dark:text-red-400 {{ $align }}">
                    <i class="fas fa-bug mr-2 ml-2"></i>
                    {{ $texts['bottlenecks'] }}
                </h3>
                <ul id="bottlenecks-list" class="space-y-3 text-gray-700 dark:text-gray-300 {{ $align }}">
                    <li class="flex items-start">
                        <i class="fas fa-times-circle text-red-500 mt-1 mr-2 ml-2"></i>
                        <span>{{ $isRtl ? 'استخدام استعلامات قاعدة بيانات غير مُحسّنة (N+1 Problem).' : 'Unoptimized database queries (N+1 Problem).' }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-times-circle text-red-500 mt-1 mr-2 ml-2"></i>
                        <span>{{ $isRtl ? 'حلقة تكرارية كبيرة بدون استخدام التخزين المؤقت (Caching).' : 'Large loop without proper caching.' }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-times-circle text-red-500 mt-1 mr-2 ml-2"></i>
                        <span>{{ $isRtl ? 'استدعاء دالة مكلفة داخل حلقة.' : 'Expensive function call inside a loop.' }}</span>
                    </li>
                </ul>
            </div>

            {{-- اقتراحات التحسين --}}
            <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-lg shadow-inner">
                <h3 class="text-xl font-semibold mb-3 text-blue-600 dark:text-blue-400 {{ $align }}">
                    <i class="fas fa-magic mr-2 ml-2"></i>
                    {{ $texts['suggestions'] }}
                </h3>
                <ul id="suggestions-list" class="space-y-3 text-gray-700 dark:text-gray-300 {{ $align }}">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2 ml-2"></i>
                        <span>{{ $isRtl ? 'استخدم Eager Loading (with) لحل مشكلة N+1.' : 'Use Eager Loading (with) to solve the N+1 problem.' }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2 ml-2"></i>
                        <span>{{ $isRtl ? 'طبق التخزين المؤقت (Redis/Memcached) للبيانات المتكررة.' : 'Apply caching (Redis/Memcached) for repetitive data.' }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2 ml-2"></i>
                        <span>{{ $isRtl ? 'استبدل الدالة المكلفة بدالة أكثر كفاءة أو قم بتحسينها.' : 'Replace the expensive function with a more efficient one or optimize it.' }}</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-500 mt-1 mr-2 ml-2"></i>
                        <span>{{ $isRtl ? 'استخدم الـ Queues للعمليات التي تستغرق وقتاً طويلاً.' : 'Use Queues for long-running operations.' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- قسم الرسوم البيانية --}}
        <div class="mt-8 bg-gray-50 dark:bg-gray-700 p-5 rounded-lg shadow-inner">
            <h3 class="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-200 {{ $align }}">
                <i class="fas fa-chart-pie mr-2 ml-2"></i>
                {{ $texts['chart_title'] }}
            </h3>
            {{-- Placeholder للرسم البياني (يفترض استخدام مكتبة مثل Chart.js) --}}
            <div class="h-64">
                <canvas id="performanceChart" class="w-full h-full"></canvas>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 {{ $align }}">
                {{ $isRtl ? 'ملاحظة: هذا الرسم البياني يتطلب مكتبة JavaScript خارجية (مثل Chart.js) ليتم عرضه بشكل صحيح.' : 'Note: This chart requires an external JavaScript library (e.g., Chart.js) to render correctly.' }}
            </p>
        </div>

        {{-- زر تحليل جديد --}}
        <div class="mt-8 {{ $alignOpposite }}">
            <button
                onclick="document.getElementById('results-card').classList.add('hidden'); document.getElementById('input-card').scrollIntoView({ behavior: 'smooth' });"
                class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-lg shadow-lg transition duration-150 ease-in-out"
            >
                <i class="fas fa-redo mr-2 ml-2"></i>
                {{ $isRtl ? 'تحليل كود جديد' : 'Analyze New Code' }}
            </button>
        </div>
    </div>

    {{-- بطاقة معالجة الأخطاء (مخفية افتراضياً) --}}
    <div id="error-card" class="hidden bg-red-100 dark:bg-red-900 border-l-4 border-red-500 dark:border-red-400 text-red-700 dark:text-red-200 p-4 rounded-lg shadow-md" role="alert">
        <div class="flex items-center">
            <div class="py-1"><i class="fas fa-exclamation-circle fa-lg mr-3 ml-3"></i></div>
            <div>
                <p class="font-bold {{ $align }}">{{ $texts['error_title'] }}</p>
                <p class="text-sm {{ $align }}">{{ $texts['error_message'] }}</p>
            </div>
        </div>
    </div>

</div>

{{-- قسم JavaScript للتفاعل (محاكاة بسيطة) --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('analysis-form');
        const resultsCard = document.getElementById('results-card');
        const errorCard = document.getElementById('error-card');
        const analyzeBtn = document.getElementById('analyze-btn');
        const codeInput = document.getElementById('code-input');

        // محاكاة إرسال النموذج
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            analyzeBtn.disabled = true;
            analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 ml-2"></i> {{ $isRtl ? 'جاري التحليل...' : 'Analyzing...' }}';
            resultsCard.classList.add('hidden');
            errorCard.classList.add('hidden');

            // محاكاة عملية التحليل (تستغرق 2 ثانية)
            setTimeout(() => {
                analyzeBtn.disabled = false;
                analyzeBtn.innerHTML = '<i class="fas fa-cogs mr-2 ml-2"></i> {{ $texts['analyze_button'] }}';

                // محاكاة نجاح أو فشل التحليل بناءً على محتوى الكود
                const code = codeInput.value.trim();
                if (code.includes('error') || code.length < 10) {
                    errorCard.classList.remove('hidden');
                    resultsCard.classList.add('hidden');
                } else {
                    // عرض النتائج
                    resultsCard.classList.remove('hidden');
                    // التمرير إلى النتائج
                    resultsCard.scrollIntoView({ behavior: 'smooth' });
                    // تحديث بيانات الرسم البياني (محاكاة)
                    renderChart();
                }
            }, 2000);
        });

        // دالة محاكاة عرض الرسم البياني (تتطلب Chart.js)
        function renderChart() {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            // التأكد من أن Chart.js متاح (يفترض أنه تم تضمينه في layouts.app)
            if (typeof Chart !== 'undefined') {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['{{ $isRtl ? 'السرعة' : 'Speed' }}', '{{ $isRtl ? 'الذاكرة' : 'Memory' }}', '{{ $isRtl ? 'الاستعلامات' : 'Queries' }}', '{{ $isRtl ? 'التعقيد' : 'Complexity' }}'],
                        datasets: [{
                            label: '{{ $isRtl ? 'نقاط الأداء' : 'Performance Score' }}',
                            data: [95, 80, 70, 85], // بيانات محاكاة
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(255, 159, 64, 0.8)',
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(54, 162, 235, 0.8)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: '{{ $isRtl ? 'النسبة المئوية' : 'Percentage' }}'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: '{{ $isRtl ? 'right' : 'left' }}'
                            }
                        }
                    }
                });
            } else {
                console.error('Chart.js library is not loaded. Performance chart cannot be rendered.');
            }
        }

        // محاكاة تفعيل الوضع الداكن (افتراضياً يتم التحكم به عبر نظام Laravel/Tailwind)
        // يمكن إضافة منطق تبديل الوضع الداكن هنا إذا لم يكن مدمجاً في التخطيط الرئيسي
        // مثال:
        // if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        //     document.documentElement.classList.add('dark');
        // } else {
        //     document.documentElement.classList.remove('dark');
        // }
    });
</script>
@endpush

@endsection

@extends('layouts.app')

@section('title', 'مساعد التخطيط الذكي')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header and Back Button -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">مساعد التخطيط الذكي</h1>
        <a href="{{ route('ai-tools.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            <i class="fas fa-arrow-right-to-bracket fa-flip-horizontal ml-2"></i>
            العودة إلى الأدوات
        </a>
    </div>

    <!-- Tool Description -->
    <div class="mb-10 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border-t-4 border-indigo-500">
        <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
            <i class="fas fa-lightbulb text-indigo-500 ml-2"></i>
            هذه الأداة المبتكرة تساعدك في **تقدير الوقت والموارد اللازمة** لتطوير الميزات الجديدة في مشاريعك. قم بإدخال تفاصيل الميزة، وسيقوم الذكاء الاصطناعي بتحليلها لتقديم خطة عمل وتقديرات دقيقة.
        </p>
    </div>

    <!-- Stats Cards Section (Placeholder for results) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Card 1: Estimated Time -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl transform hover:scale-[1.02] transition duration-300 ease-in-out border-r-4 border-green-500">
            <div class="flex items-center">
                <i class="fas fa-clock text-3xl text-green-500 ml-4"></i>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">الوقت المقدر</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="estimated-time">--</p>
                </div>
            </div>
        </div>
        <!-- Card 2: Required Resources -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl transform hover:scale-[1.02] transition duration-300 ease-in-out border-r-4 border-yellow-500">
            <div class="flex items-center">
                <i class="fas fa-users-gear text-3xl text-yellow-500 ml-4"></i>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">الموارد المطلوبة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="required-resources">--</p>
                </div>
            </div>
        </div>
        <!-- Card 3: Estimated Cost -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl transform hover:scale-[1.02] transition duration-300 ease-in-out border-r-4 border-red-500">
            <div class="flex items-center">
                <i class="fas fa-money-bill-wave text-3xl text-red-500 ml-4"></i>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">التكلفة التقديرية</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="estimated-cost">--</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Input Form -->
    <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-2xl">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white border-b pb-3">إدخال تفاصيل الميزة</h2>
        <form id="planning-form">
            <div class="space-y-6">
                <!-- Feature Description -->
                <div>
                    <label for="feature-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">وصف الميزة المطلوبة <span class="text-red-500">*</span></label>
                    <textarea id="feature-description" name="feature-description" rows="4" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3" placeholder="مثال: إضافة نظام دفع جديد يدعم بطاقات الائتمان والمحافظ الإلكترونية، مع تقارير شهرية مفصلة."></textarea>
                </div>

                <!-- Priority and Complexity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">أولوية الميزة</label>
                        <select id="priority" name="priority" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3">
                            <option value="high">عالية</option>
                            <option value="medium" selected>متوسطة</option>
                            <option value="low">منخفضة</option>
                        </select>
                    </div>
                    <!-- Complexity -->
                    <div>
                        <label for="complexity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">التعقيد المتوقع</label>
                        <select id="complexity" name="complexity" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3">
                            <option value="simple">بسيط</option>
                            <option value="moderate" selected>متوسط</option>
                            <option value="complex">معقد</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Generate Button -->
            <div class="mt-8">
                <button type="submit" class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-[1.01]">
                    <i class="fas fa-rocket ml-3"></i>
                    توليد خطة وتقديرات المشروع
                </button>
            </div>
        </form>
    </div>

    <!-- Results Section (Hidden by default) -->
    <div id="planning-results" class="mt-10 hidden">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white border-b pb-3">خطة العمل المقترحة</h2>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-2xl">
            <p class="text-gray-700 dark:text-gray-300" id="result-content">
                <!-- AI generated plan will be inserted here -->
            </p>
        </div>
    </div>
</div>

<!-- Simple Placeholder Script for interactivity -->
<script>
    document.getElementById('planning-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Simulate AI processing and results
        const description = document.getElementById('feature-description').value;
        if (description.trim() === '') {
            alert('الرجاء إدخال وصف الميزة أولاً.');
            return;
        }

        // Simulate API call and update stats/results
        document.getElementById('estimated-time').textContent = '4 أسابيع';
        document.getElementById('required-resources').textContent = 'مطور، مصمم UX';
        document.getElementById('estimated-cost').textContent = '15,000 $';

        const resultHtml = \`
            <h3 class="text-xl font-semibold text-indigo-600 dark:text-indigo-400 mb-4">ملخص التقديرات:</h3>
            <ul class="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                <li><span class="font-medium">الوقت:</span> 4 أسابيع (20 يوم عمل)</li>
                <li><span class="font-medium">الموارد:</span> مطور رئيسي، مصمم واجهات/تجربة مستخدم (UX/UI).</li>
                <li><span class="font-medium">التكلفة التقديرية:</span> 15,000 دولار أمريكي.</li>
            </ul>
            
            <h3 class="text-xl font-semibold text-indigo-600 dark:text-indigo-400 mb-4">الخطوات الرئيسية المقترحة:</h3>
            <ol class="list-decimal list-inside space-y-3 text-gray-700 dark:text-gray-300">
                <li><span class="font-medium">التخطيط والتحليل (الأسبوع 1):</span> جمع المتطلبات النهائية، تصميم قاعدة البيانات، وتحديد واجهات البرمجة (APIs).</li>
                <li><span class="font-medium">التصميم والتطوير (الأسبوع 2-3):</span> بناء الواجهات الأمامية والخلفية للميزة (\${description}).</li>
                <li><span class="font-medium">الاختبار والمراجعة (الأسبوع 4):</span> اختبار شامل للميزة، وإطلاقها التجريبي، وجمع الملاحظات.</li>
            </ol>
        \`;

        document.getElementById('result-content').innerHTML = resultHtml;
        document.getElementById('planning-results').classList.remove('hidden');
        
        // Scroll to results
        document.getElementById('planning-results').scrollIntoView({ behavior: 'smooth' });
    });
</script>

@endsection

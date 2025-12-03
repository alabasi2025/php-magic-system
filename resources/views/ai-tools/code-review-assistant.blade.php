@extends('layouts.app')

@section('title', 'مساعد المراجعة الذكي للكود')

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <!-- زر العودة -->
    <div class="mb-6">
        <a href="{{ route('ai-tools.dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition duration-150 ease-in-out">
            <i class="fas fa-arrow-right-to-bracket fa-rotate-180 ml-2"></i>
            العودة إلى لوحة أدوات الذكاء الاصطناعي
        </a>
    </div>

    <!-- العنوان والوصف -->
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-3">
            <i class="fas fa-code-branch text-blue-600 mr-2"></i>
            مساعد المراجعة الذكي للكود
        </h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            يوفر هذا المساعد مراجعات أعمق للكود، مع تحليل متقدم للجودة والأمان والأداء، ويتعلم باستمرار من مراجعات فريقك لتقديم اقتراحات أكثر دقة وفعالية.
        </p>
    </div>

    <!-- بطاقات الإحصائيات (Stats Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <!-- البطاقة 1: إجمالي المراجعات -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-blue-500 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-file-code fa-2x"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-500 truncate">إجمالي المراجعات</p>
                    <p class="text-3xl font-bold text-gray-900">1,452</p>
                </div>
            </div>
        </div>

        <!-- البطاقة 2: متوسط درجة الجودة -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-green-500 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-star fa-2x"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-500 truncate">متوسط درجة الجودة</p>
                    <p class="text-3xl font-bold text-gray-900">92<span class="text-xl font-normal">/100</span></p>
                </div>
            </div>
        </div>

        <!-- البطاقة 3: توفير الوقت المقدر -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-purple-500 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm font-medium text-gray-500 truncate">توفير الوقت المقدر</p>
                    <p class="text-3xl font-bold text-gray-900">250<span class="text-xl font-normal"> ساعة</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- نموذج الإدخال التفاعلي -->
    <div class="bg-white rounded-xl shadow-2xl p-8 lg:p-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">
            <i class="fas fa-terminal text-red-500 ml-2"></i>
            إجراء مراجعة كود جديدة
        </h2>

        <form action="#" method="POST">
            @csrf
            <!-- خيار الإدخال: لصق الكود أو رابط المستودع -->
            <div class="mb-6">
                <label for="code_input_type" class="block text-sm font-medium text-gray-700 mb-2">طريقة إدخال الكود</label>
                <select id="code_input_type" name="code_input_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="paste">لصق الكود مباشرة</option>
                    <option value="repo_link">رابط مستودع (GitHub/GitLab)</option>
                </select>
            </div>

            <!-- حقل لصق الكود (يظهر افتراضياً) -->
            <div id="paste_code_field" class="mb-6">
                <label for="code_snippet" class="block text-sm font-medium text-gray-700 mb-2">الكود المراد مراجعته</label>
                <textarea id="code_snippet" name="code_snippet" rows="10" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-4 font-mono text-sm" placeholder="الصق الكود هنا..."></textarea>
            </div>

            <!-- حقل رابط المستودع (يتم إخفاؤه افتراضياً) -->
            <div id="repo_link_field" class="mb-6 hidden">
                <label for="repo_link" class="block text-sm font-medium text-gray-700 mb-2">رابط المستودع (URL)</label>
                <input type="url" id="repo_link" name="repo_link" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-3" placeholder="مثال: https://github.com/user/repo-name">
                <p class="mt-2 text-xs text-gray-500">يرجى التأكد من أن المستودع عام أو أن لديك صلاحيات وصول.</p>
            </div>

            <!-- خيارات المراجعة المتقدمة -->
            <div class="mb-8 p-4 rounded-lg" style="background: linear-gradient(to left, #f0f9ff, #e0f2fe);">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">
                    <i class="fas fa-cogs text-blue-700 ml-1"></i>
                    تخصيص المراجعة
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700">لغة البرمجة</label>
                        <select id="language" name="language" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option>PHP (Laravel)</option>
                            <option>JavaScript (React/Vue)</option>
                            <option>Python (Django/Flask)</option>
                            <option>أخرى...</option>
                        </select>
                    </div>
                    <div>
                        <label for="focus" class="block text-sm font-medium text-gray-700">تركيز المراجعة</label>
                        <select id="focus" name="focus" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option>الأمان (Security)</option>
                            <option>الأداء (Performance)</option>
                            <option>جودة الكود (Readability)</option>
                            <option>الكل</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- زر الإرسال -->
            <div class="flex justify-center">
                <button type="submit" class="w-full md:w-auto px-8 py-3 text-lg font-semibold text-white rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:scale-105"
                    style="background: linear-gradient(to right, #3b82f6, #1d4ed8);">
                    <i class="fas fa-magic ml-2"></i>
                    بدء المراجعة الذكية
                </button>
            </div>
        </form>
    </div>

    <!-- قسم النتائج (يمكن إخفاؤه/إظهاره باستخدام JavaScript) -->
    <div class="mt-12 bg-white rounded-xl shadow-2xl p-8 lg:p-10 border-t-4 border-red-500">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">
            <i class="fas fa-clipboard-list text-red-500 ml-2"></i>
            نتائج المراجعة الأخيرة
        </h2>
        <div class="text-gray-600">
            <p class="mb-4">لم يتم إجراء أي مراجعة بعد. بعد إرسال الكود، ستظهر النتائج التفصيلية هنا، بما في ذلك المشكلات المكتشفة، واقتراحات التحسين، ودرجة الجودة الإجمالية.</p>
            <ul class="list-disc list-inside space-y-2 text-sm">
                <li><i class="fas fa-check-circle text-green-500 ml-1"></i> تحليل الأمان: فحص الثغرات المحتملة.</li>
                <li><i class="fas fa-check-circle text-green-500 ml-1"></i> تحسين الأداء: اقتراحات لتقليل زمن التنفيذ.</li>
                <li><i class="fas fa-check-circle text-green-500 ml-1"></i> مطابقة المعايير: التأكد من اتباع أفضل الممارسات.</li>
            </ul>
        </div>
    </div>
</div>

<!-- إضافة بسيطة لتبديل حقول الإدخال -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('code_input_type');
        const pasteField = document.getElementById('paste_code_field');
        const repoField = document.getElementById('repo_link_field');

        select.addEventListener('change', function() {
            if (this.value === 'paste') {
                pasteField.classList.remove('hidden');
                repoField.classList.add('hidden');
            } else {
                pasteField.classList.add('hidden');
                repoField.classList.remove('hidden');
            }
        });
    });
</script>
@endsection
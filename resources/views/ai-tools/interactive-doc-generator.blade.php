@extends('layouts.app')

@section('title', 'مولد التوثيق التفاعلي')

@section('content')
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <!-- شريط العنوان والعودة -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                <i class="fas fa-book-open text-indigo-600 dark:text-indigo-400 mr-2"></i>
                مولد التوثيق التفاعلي
            </h1>
            <a href="{{ route('ai-tools.dashboard') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-105">
                <i class="fas fa-arrow-right-to-bracket fa-flip-horizontal mr-2"></i>
                العودة للأدوات
            </a>
        </div>

        <!-- وصف الأداة -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 mb-8 border-t-4 border-indigo-500">
            <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                <i class="fas fa-info-circle text-indigo-500 ml-2"></i>
                هذه الأداة المبتكرة تقوم بـ **توليد توثيق تفاعلي** لمشاريعك أو منتجاتك. بدلاً من التوثيق الثابت، ستحصل على صفحات توثيق غنية بـ **أمثلة حية** قابلة للتجربة والتعديل مباشرة، مما يسهل على المستخدمين فهم واستخدام الميزات بسرعة وكفاءة.
            </p>
        </div>

        <!-- بطاقات الإحصائيات (Stats Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- بطاقة 1: التوثيقات المولدة -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-xl p-5 text-white transform hover:scale-[1.02] transition duration-300">
                <div class="flex items-center">
                    <i class="fas fa-file-code fa-2x opacity-75"></i>
                    <div class="ml-4">
                        <p class="text-sm font-medium opacity-80">توثيقات مولدة</p>
                        <p class="text-3xl font-bold">1,245</p>
                    </div>
                </div>
            </div>
            <!-- بطاقة 2: متوسط التفاعل -->
            <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-xl shadow-xl p-5 text-white transform hover:scale-[1.02] transition duration-300">
                <div class="flex items-center">
                    <i class="fas fa-chart-line fa-2x opacity-75"></i>
                    <div class="ml-4">
                        <p class="text-sm font-medium opacity-80">متوسط التفاعل</p>
                        <p class="text-3xl font-bold">92%</p>
                    </div>
                </div>
            </div>
            <!-- بطاقة 3: توفير الوقت -->
            <div class="bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl shadow-xl p-5 text-white transform hover:scale-[1.02] transition duration-300">
                <div class="flex items-center">
                    <i class="fas fa-clock fa-2x opacity-75"></i>
                    <div class="ml-4">
                        <p class="text-sm font-medium opacity-80">توفير الوقت</p>
                        <p class="text-3xl font-bold">45%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج الإدخال التفاعلي -->
        <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-2 border-indigo-200 dark:border-indigo-700">
                <i class="fas fa-cogs text-indigo-500 ml-2"></i>
                إعدادات التوثيق
            </h2>

            <form action="#" method="POST">
                @csrf
                <!-- حقل اسم المشروع -->
                <div class="mb-6">
                    <label for="project_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-project-diagram text-indigo-400 ml-1"></i>
                        اسم المشروع
                    </label>
                    <input type="text" id="project_name" name="project_name" placeholder="أدخل اسم مشروعك (مثال: نظام إدارة المحتوى)"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-150 ease-in-out" required>
                </div>

                <!-- حقل وصف المشروع -->
                <div class="mb-6">
                    <label for="project_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-align-right text-indigo-400 ml-1"></i>
                        وصف مختصر للمشروع
                    </label>
                    <textarea id="project_description" name="project_description" rows="4" placeholder="صف مشروعك بإيجاز لتوليد توثيق دقيق..."
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-150 ease-in-out" required></textarea>
                </div>

                <!-- حقل نوع التوثيق (اختيار متعدد) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-list-check text-indigo-400 ml-1"></i>
                        أنواع التوثيق المطلوبة
                    </label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center bg-indigo-100 dark:bg-indigo-900/50 p-3 rounded-lg cursor-pointer hover:shadow-md transition duration-150">
                            <input type="checkbox" name="doc_type[]" value="api" class="form-checkbox h-5 w-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="mr-2 text-gray-800 dark:text-gray-200 font-medium">توثيق API</span>
                        </label>
                        <label class="inline-flex items-center bg-indigo-100 dark:bg-indigo-900/50 p-3 rounded-lg cursor-pointer hover:shadow-md transition duration-150">
                            <input type="checkbox" name="doc_type[]" value="user_guide" class="form-checkbox h-5 w-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="mr-2 text-gray-800 dark:text-gray-200 font-medium">دليل المستخدم</span>
                        </label>
                        <label class="inline-flex items-center bg-indigo-100 dark:bg-indigo-900/50 p-3 rounded-lg cursor-pointer hover:shadow-md transition duration-150">
                            <input type="checkbox" name="doc_type[]" value="setup" class="form-checkbox h-5 w-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="mr-2 text-gray-800 dark:text-gray-200 font-medium">إعداد وتثبيت</span>
                        </label>
                    </div>
                </div>

                <!-- زر التوليد -->
                <button type="submit"
                        class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 ease-in-out transform hover:scale-[1.01]">
                    <i class="fas fa-magic fa-spin ml-2"></i>
                    توليد التوثيق التفاعلي الآن
                </button>
            </form>
        </div>

        <!-- قسم التوثيق الناتج (مثال) -->
        <div class="mt-10 p-8 bg-gray-50 dark:bg-gray-900 rounded-xl shadow-inner border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-terminal text-green-500 ml-2"></i>
                النتائج والأمثلة الحية
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">سيظهر التوثيق التفاعلي مع الأمثلة القابلة للتجربة هنا بعد التوليد.</p>
            <div class="h-64 bg-white dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center">
                <p class="text-gray-400 dark:text-gray-500">منطقة عرض التوثيق التفاعلي (Live Preview)</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- يمكن إضافة أكواد JavaScript هنا لجعل التوثيق تفاعليًا بشكل حقيقي -->
    <script>
        // مثال على تفاعل بسيط
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Interactive Documentation Generator page loaded.');
        });
    </script>
@endpush

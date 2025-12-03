@extends('layouts.app')

@section('title', 'مولد الكود من الصور والتصاميم')

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    {{-- زر العودة --}}
    <div class="mb-6">
        <a href="{{ route('ai-tools.dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-indigo-600 transition duration-150 ease-in-out font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى لوحة أدوات الذكاء الاصطناعي
        </a>
    </div>

    {{-- العنوان والوصف --}}
    <header class="text-center mb-10 p-8 rounded-2xl shadow-2xl transform hover:scale-[1.01] transition duration-300 ease-in-out"
            style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-3">
            <i class="fas fa-magic mr-3"></i>
            مولد الكود من الصور والتصاميم
        </h1>
        <p class="text-lg md:text-xl text-indigo-100 max-w-4xl mx-auto mt-4">
            أداة الذكاء الاصطناعي الثورية التي تحول تصاميم واجهات المستخدم (UI/UX) من صور أو ملفات Figma إلى كود Blade وTailwind CSS نظيف وقابل للتعديل في ثوانٍ.
        </p>
    </header>

    {{-- بطاقات الإحصائيات (Stats Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        {{-- بطاقة 1: سرعة التحويل --}}
        <div class="bg-white p-6 rounded-xl shadow-xl border-r-4 border-indigo-500 hover:shadow-2xl transition duration-300">
            <div class="flex items-center">
                <i class="fas fa-bolt text-4xl text-indigo-500 ml-4"></i>
                <div>
                    <p class="text-sm font-medium text-gray-500">سرعة التحويل</p>
                    <p class="text-2xl font-bold text-gray-900">أقل من 5 ثوانٍ</p>
                </div>
            </div>
        </div>
        {{-- بطاقة 2: دقة الكود --}}
        <div class="bg-white p-6 rounded-xl shadow-xl border-r-4 border-purple-500 hover:shadow-2xl transition duration-300">
            <div class="flex items-center">
                <i class="fas fa-code text-4xl text-purple-500 ml-4"></i>
                <div>
                    <p class="text-sm font-medium text-gray-500">دقة الكود</p>
                    <p class="text-2xl font-bold text-gray-900">99% توافق مع Tailwind</p>
                </div>
            </div>
        </div>
        {{-- بطاقة 3: التصاميم المدعومة --}}
        <div class="bg-white p-6 rounded-xl shadow-xl border-r-4 border-pink-500 hover:shadow-2xl transition duration-300">
            <div class="flex items-center">
                <i class="fas fa-palette text-4xl text-pink-500 ml-4"></i>
                <div>
                    <p class="text-sm font-medium text-gray-500">التصاميم المدعومة</p>
                    <p class="text-2xl font-bold text-gray-900">صور، Figma، Sketch</p>
                </div>
            </div>
        </div>
    </div>

    {{-- نموذج الإدخال التفاعلي --}}
    <div class="bg-white p-8 rounded-2xl shadow-2xl border border-gray-100">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-3">ابدأ التحويل الآن</h2>
        <form action="#" method="POST" enctype="multipart/form-data" class="space-y-6">
            {{-- حقل رفع الصورة/التصميم --}}
            <div>
                <label for="design_file" class="block text-lg font-medium text-gray-700 mb-2">
                    <i class="fas fa-upload mr-2 text-indigo-500"></i>
                    رفع ملف التصميم (صورة، PDF، أو رابط Figma)
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400 transition duration-150">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m-4-4l-1.172-1.172a4 4 0 00-5.656 0L12 32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="design_file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>اضغط للتحميل</span>
                                <input id="design_file" name="design_file" type="file" class="sr-only" accept="image/*, .pdf">
                            </label>
                            <p class="pr-1">أو اسحب وأفلت هنا</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, PDF حتى 10MB</p>
                    </div>
                </div>
            </div>

            {{-- حقل رابط Figma (اختياري) --}}
            <div>
                <label for="figma_link" class="block text-lg font-medium text-gray-700 mb-2">
                    <i class="fab fa-figma mr-2 text-purple-500"></i>
                    رابط Figma (اختياري)
                </label>
                <input type="url" name="figma_link" id="figma_link" placeholder="أدخل رابط ملف Figma هنا..."
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
            </div>

            {{-- خيارات الإخراج --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="framework" class="block text-lg font-medium text-gray-700 mb-2">
                        <i class="fas fa-laptop-code mr-2 text-green-500"></i>
                        إطار العمل المستهدف
                    </label>
                    <select id="framework" name="framework"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                        <option value="blade_tailwind">Laravel Blade + Tailwind CSS</option>
                        <option value="react_tailwind">React + Tailwind CSS</option>
                        <option value="vue_tailwind">Vue + Tailwind CSS</option>
                    </select>
                </div>
                <div>
                    <label for="responsiveness" class="block text-lg font-medium text-gray-700 mb-2">
                        <i class="fas fa-mobile-alt mr-2 text-blue-500"></i>
                        استجابة التصميم (Responsiveness)
                    </label>
                    <select id="responsiveness" name="responsiveness"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150">
                        <option value="full">استجابة كاملة (Full Responsive)</option>
                        <option value="desktop_only">سطح المكتب فقط</option>
                    </select>
                </div>
            </div>

            {{-- زر التحويل --}}
            <div class="pt-5">
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-lg text-lg font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-[1.01]">
                    <i class="fas fa-cogs mr-3"></i>
                    توليد الكود الآن
                </button>
            </div>
        </form>
    </div>

    {{-- قسم النتائج (يمكن إخفاؤه في البداية) --}}
    <div id="results-section" class="mt-12 bg-gray-50 p-8 rounded-2xl shadow-inner border border-gray-200">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">النتائج</h2>
        <div class="bg-white p-6 rounded-xl shadow-md">
            <p class="text-gray-600 text-center">سيظهر الكود الناتج هنا بعد عملية التحويل.</p>
            {{-- مثال على منطقة عرض الكود --}}
            <pre class="mt-4 bg-gray-800 text-green-400 p-4 rounded-lg overflow-x-auto text-left" dir="ltr">
&lt;!-- الكود الناتج سيظهر هنا --&gt;
&lt;div class="bg-white shadow-lg rounded-lg p-6"&gt;
    &lt;h2 class="text-2xl font-bold text-gray-900"&gt;تصميم محول&lt;/h2&gt;
    &lt;p class="text-gray-600 mt-2"&gt;تم توليد هذا الكود بواسطة الذكاء الاصطناعي.&lt;/p&gt;
&lt;/div&gt;
            </pre>
            <button class="mt-4 w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-copy ml-2"></i>
                نسخ الكود
            </button>
        </div>
    </div>
</div>
@endsection
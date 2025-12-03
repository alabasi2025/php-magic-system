@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 md:p-8" dir="rtl">
    <!-- زر العودة -->
    <div class="mb-6 flex justify-start">
        <a href="{{ route('ai-tools.dashboard') }}" class="text-white hover:text-indigo-300 transition duration-300 flex items-center p-2 rounded-lg bg-gray-700 hover:bg-gray-600 shadow-lg">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة إلى لوحة الأدوات
        </a>
    </div>

    <!-- العنوان والوصف -->
    <header class="text-center mb-10">
        <h1 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-500 mb-3">
            <i class="fas fa-magic ml-3"></i>
            مولد الكود من اللغة الطبيعية المتقدم
        </h1>
        <p class="text-xl text-gray-300 max-w-3xl mx-auto">
            أطلق العنان لقوة الذكاء الاصطناعي! هذه الأداة تفهم الأوامر المعقدة باللغة العربية وتحولها إلى أنظمة برمجية كاملة وعالية الجودة.
        </p>
    </header>

    <!-- بطاقات الإحصائيات (Stats Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <!-- البطاقة 1 -->
        <div class="bg-gray-800 p-6 rounded-xl shadow-2xl border border-gray-700 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center">
                <i class="fas fa-brain text-3xl text-indigo-400 ml-4"></i>
                <div>
                    <p class="text-sm font-medium text-gray-400">فهم سياقي</p>
                    <p class="text-2xl font-bold text-white">متقدم</p>
                </div>
            </div>
        </div>
        <!-- البطاقة 2 -->
        <div class="bg-gray-800 p-6 rounded-xl shadow-2xl border border-gray-700 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center">
                <i class="fas fa-cogs text-3xl text-purple-400 ml-4"></i>
                <div>
                    <p class="text-sm font-medium text-gray-400">دعم الأنظمة</p>
                    <p class="text-2xl font-bold text-white">كاملة</p>
                </div>
            </div>
        </div>
        <!-- البطاقة 3 -->
        <div class="bg-gray-800 p-6 rounded-xl shadow-2xl border border-gray-700 transform hover:scale-[1.02] transition duration-300">
            <div class="flex items-center">
                <i class="fas fa-bolt text-3xl text-pink-400 ml-4"></i>
                <div>
                    <p class="text-sm font-medium text-gray-400">سرعة التحويل</p>
                    <p class="text-2xl font-bold text-white">فورية</p>
                </div>
            </div>
        </div>
    </div>

    <!-- نموذج الإدخال والنتيجة -->
    <div class="bg-gray-900 p-8 rounded-2xl shadow-2xl border border-indigo-900/50">
        <form action="#" method="POST">
            @csrf
            <div class="mb-6">
                <label for="arabic_command" class="block text-lg font-semibold text-gray-200 mb-3">
                    <i class="fas fa-keyboard ml-2 text-indigo-400"></i>
                    أدخل الأمر البرمجي المعقد باللغة العربية:
                </label>
                <textarea id="arabic_command" name="arabic_command" rows="10"
                    class="w-full p-4 bg-gray-700 border border-gray-600 rounded-xl text-white focus:ring-indigo-500 focus:border-indigo-500 transition duration-300 resize-none placeholder-gray-400"
                    placeholder="مثال: قم بإنشاء نظام إدارة مهام (To-Do List) باستخدام Laravel و Vue.js، مع إمكانية تسجيل الدخول، وتخزين المهام في قاعدة بيانات MySQL، وتوفير واجهة برمجة تطبيقات (API) لجلب المهام."></textarea>
            </div>

            <div class="flex justify-center">
                <button type="submit"
                    class="px-8 py-3 text-lg font-bold text-white rounded-full shadow-lg transition duration-300 transform hover:scale-105
                           bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50">
                    <i class="fas fa-code ml-2"></i>
                    توليد النظام البرمجي
                </button>
            </div>
        </form>

        <!-- منطقة النتيجة (Placeholder) -->
        <div class="mt-10 pt-8 border-t border-gray-700">
            <h2 class="text-3xl font-bold text-gray-100 mb-4">
                <i class="fas fa-file-code ml-2 text-green-400"></i>
                النتيجة: الكود والنظام المتولد
            </h2>
            <div class="bg-gray-800 p-6 rounded-xl shadow-inner border border-gray-700 min-h-[200px]">
                <p class="text-gray-400 italic">
                    سيظهر هنا الكود البرمجي الكامل (ملفات، هياكل، تعليمات) بعد إرسال الأمر.
                </p>
                <!-- مثال على عرض الكود (يمكن استبداله بمكون عرض كود حقيقي) -->
                <pre class="mt-4 p-4 bg-gray-900 rounded-lg overflow-x-auto text-sm text-green-300">
                    <code>
// مثال على ملف Controller متولد
&lt;?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    // ... الكود المتولد ...
}
                    </code>
                </pre>
            </div>
        </div>
    </div>
</div>
@endsection

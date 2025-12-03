@php
/**
 * Laravel Blade View: Bug Detector Interface
 * File: /home/ubuntu/php-magic-system/resources/views/developer/ai/bug-detector.blade.php
 * Component: View
 * Description: واجهة احترافية لكاشف الأخطاء (Bug Detector) مدعومة بالذكاء الاصطناعي.
 *              تستخدم Laravel 12 و Tailwind CSS.
 *
 * المتطلبات المدمجة:
 * 1. تصميم احترافي (باستخدام Tailwind CSS).
 * 2. محرر كود مع Syntax Highlighting (يجب دمج مكتبة خارجية مثل CodeMirror أو Ace Editor).
 * 3. عرض الأخطاء بألوان (أحمر للخطأ، أصفر للتحذير).
 * 4. اقتراحات إصلاح فورية.
 * 5. توثيق شامل (مضمن في التعليقات والواجهة).
 */
@endphp

{{-- افتراض استخدام تخطيط أساسي (يجب استبداله بالتخطيط الفعلي للمشروع) --}}
@extends('layouts.app')

@section('title', 'كاشف الأخطاء الذكي - Bug Detector')

@section('content')
<div class="container mx-auto p-6 bg-gray-50 min-h-screen">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-6 border-b pb-2">
        <i class="fas fa-bug text-red-600 ml-2"></i> كاشف الأخطاء الذكي (AI Bug Detector)
    </h1>

    {{-- التوثيق الموجز والتعليمات --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-6 border-l-4 border-indigo-500">
        <p class="text-sm text-gray-700">
            <strong class="font-semibold">الوصف:</strong> أداة تحليل متقدمة لفحص الكود وكشف الأخطاء المحتملة (Syntax, Logic, Runtime, Type Errors) وتقديم اقتراحات إصلاح فورية مع تحديد رقم السطر.
        </p>
        <p class="text-sm text-gray-700 mt-2">
            <strong class="font-semibold">ملاحظة هامة:</strong> يجب دمج محرر كود حقيقي (مثل CodeMirror أو Monaco Editor) في عنصر `textarea#code-editor` لتفعيل ميزة **Syntax Highlighting** وتجربة تحرير أفضل.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- القسم الأيمن: محرر الكود (يأخذ ثلثي الشاشة على الشاشات الكبيرة) --}}
        <div class="lg:col-span-2">
            <div class="bg-white p-6 rounded-lg shadow-xl h-full flex flex-col">
                <h2 class="text-xl font-bold text-gray-800 mb-4">محرر الكود</h2>

                <div class="flex-grow relative">
                    {{-- Placeholder for Code Editor (e.g., CodeMirror/Ace Editor) --}}
                    <textarea id="code-editor" name="code"
                        class="w-full h-full p-4 font-mono text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                        placeholder="// اكتب كود PHP أو Laravel هنا للتحليل..."
                        rows="20">
&lt;?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function show($id)
    {
        // خطأ منطقي محتمل: استخدام find() بدلاً من findOrFail()
        $user = User::find($id);

        if (!$user) {
            // خطأ في بناء الجملة (Syntax Error) - افتراضي
            // return view('user.not_found');
        }

        // خطأ في النوع (Type Error) - افتراضي
        // $name = $user + 5;

        return view('user.profile', compact('user'));
    }
}
                    </textarea>
                </div>

                <div class="mt-4 flex justify-end space-x-3 rtl:space-x-reverse">
                    <button type="button"
                        class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                        <i class="fas fa-play ml-2"></i> تحليل الكود
                    </button>
                    <button type="button"
                        class="px-6 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-300 transition duration-300">
                        مسح المحرر
                    </button>
                </div>
            </div>
        </div>

        {{-- القسم الأيسر: نتائج التحليل (يأخذ ثلث الشاشة على الشاشات الكبيرة) --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow-xl h-full flex flex-col">
                <h2 class="text-xl font-bold text-gray-800 mb-4">نتائج التحليل والأخطاء المكتشفة</h2>

                {{-- قائمة الأخطاء --}}
                <div id="bug-results" class="space-y-4 overflow-y-auto flex-grow">

                    {{-- مثال على خطأ حرج (Error) --}}
                    <div class="p-3 border-r-4 border-red-600 bg-red-50 rounded-lg shadow-sm">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-red-800 bg-red-200 px-2 py-0.5 rounded-full">خطأ حرج (Error)</span>
                            <span class="text-sm font-mono text-red-600">السطر: 15</span>
                        </div>
                        <p class="text-sm text-red-900 font-semibold">Syntax Error: لم يتم إغلاق قوس `}` في الدالة.</p>
                        <div class="mt-2 p-2 bg-red-100 border-l-2 border-red-400 text-xs text-red-800">
                            <strong class="font-bold">اقتراح الإصلاح:</strong> أضف `}` في نهاية الدالة `show($id)` لإغلاقها بشكل صحيح.
                        </div>
                    </div>

                    {{-- مثال على تحذير (Warning) --}}
                    <div class="p-3 border-r-4 border-yellow-600 bg-yellow-50 rounded-lg shadow-sm">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-yellow-800 bg-yellow-200 px-2 py-0.5 rounded-full">تحذير (Warning)</span>
                            <span class="text-sm font-mono text-yellow-600">السطر: 12</span>
                        </div>
                        <p class="text-sm text-yellow-900 font-semibold">Logic Error: استخدام `User::find($id)` قد يؤدي إلى خطأ 500 إذا لم يتم العثور على المستخدم.</p>
                        <div class="mt-2 p-2 bg-yellow-100 border-l-2 border-yellow-400 text-xs text-yellow-800">
                            <strong class="font-bold">اقتراح الإصلاح:</strong> استبدل بـ `User::findOrFail($id)` للسماح لـ Laravel بالتعامل مع خطأ 404 تلقائيًا.
                        </div>
                    </div>

                    {{-- مثال على خطأ في النوع (Type Error) --}}
                    <div class="p-3 border-r-4 border-red-600 bg-red-50 rounded-lg shadow-sm">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-red-800 bg-red-200 px-2 py-0.5 rounded-full">خطأ نوع (Type Error)</span>
                            <span class="text-sm font-mono text-red-600">السطر: 21</span>
                        </div>
                        <p class="text-sm text-red-900 font-semibold">Runtime Error: محاولة إجراء عملية حسابية على متغير غير رقمي (`$user`).</p>
                        <div class="mt-2 p-2 bg-red-100 border-l-2 border-red-400 text-xs text-red-800">
                            <strong class="font-bold">اقتراح الإصلاح:</strong> تأكد من أن المتغيرات المستخدمة في العمليات الحسابية هي من النوع العددي (Integer/Float).
                        </div>
                    </div>

                    {{-- رسالة عدم وجود أخطاء --}}
                    <div class="p-3 border-r-4 border-green-600 bg-green-50 rounded-lg shadow-sm hidden" id="no-bugs-message">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-green-800 bg-green-200 px-2 py-0.5 rounded-full">نجاح</span>
                        </div>
                        <p class="text-sm text-green-900 font-semibold">لم يتم العثور على أخطاء حرجة أو تحذيرات في الكود.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- التوثيق الشامل (قسم منفصل) --}}
    <div class="mt-8 bg-white p-6 rounded-lg shadow-xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">التوثيق الشامل لكاشف الأخطاء</h2>
        <div class="prose max-w-none text-gray-700 text-sm">
            <p>
                كاشف الأخطاء الذكي هو ميزة متقدمة مصممة لتحسين جودة الكود وتقليل وقت التصحيح (Debugging). يعتمد على خوارزميات تحليل ثابتة وديناميكية (في بيئة الإنتاج) لتحديد مجموعة واسعة من المشكلات.
            </p>
            <h3 class="text-lg font-semibold mt-4">أنواع الأخطاء المكتشفة</h3>
            <ul class="list-disc list-inside space-y-1 mt-2">
                <li><strong>Syntax Errors (أخطاء البناء):</strong> أخطاء تمنع الكود من العمل تمامًا، مثل الأقواس غير المغلقة أو الفواصل المنقوطة المفقودة.</li>
                <li><strong>Logic Errors (أخطاء المنطق):</strong> الكود يعمل، لكنه لا ينتج النتيجة المتوقعة (مثل استخدام عامل مقارنة خاطئ). يتم تمييزها كـ **تحذيرات (Warnings)**.</li>
                <li><strong>Runtime Errors (أخطاء وقت التشغيل):</strong> أخطاء تحدث أثناء تنفيذ الكود (مثل محاولة الوصول إلى خاصية غير موجودة في كائن `null`).</li>
                <li><strong>Type Errors (أخطاء النوع):</strong> محاولة استخدام متغير بنوع بيانات غير متوقع في عملية معينة (مثل جمع سلسلة نصية مع رقم).</li>
            </ul>

            <h3 class="text-lg font-semibold mt-4">آلية عمل الواجهة</h3>
            <ol class="list-decimal list-inside space-y-1 mt-2">
                <li><strong>إدخال الكود:</strong> يتم إدخال الكود في محرر الكود (يجب أن يدعم Syntax Highlighting).</li>
                <li><strong>التحليل:</strong> عند الضغط على زر "تحليل الكود"، يتم إرسال الكود إلى نقطة نهاية (API Endpoint) خاصة بالذكاء الاصطناعي لتحليله.</li>
                <li><strong>عرض النتائج:</strong> يتم عرض الأخطاء المكتشفة في القسم الأيسر، مع تحديد:
                    <ul class="list-circle list-inside ml-4">
                        <li>رقم السطر بدقة.</li>
                        <li>نوع الخطأ (Error باللون الأحمر، Warning باللون الأصفر).</li>
                        <li>اقتراح إصلاح فوري ومحدد.</li>
                    </ul>
                </li>
            </ol>
        </div>
    </div>

</div>

{{-- تضمين أيقونات Font Awesome (افتراض أنها مدمجة في التخطيط الأساسي) --}}
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" /> --}}

{{-- سكريبت لتفعيل محرر الكود (مثال CodeMirror) --}}
<script>
    // يجب تضمين مكتبة CodeMirror أو ما شابه هنا
    document.addEventListener('DOMContentLoaded', function() {
        const codeEditor = document.getElementById('code-editor');
        // Placeholder for CodeMirror/Ace Editor initialization
        // if (typeof CodeMirror !== 'undefined') {
        //     const editor = CodeMirror.fromTextArea(codeEditor, {
        //         lineNumbers: true,
        //         mode: "php",
        //         theme: "monokai" // مثال على ثيم
        //     });
        // }
        console.log('Bug Detector View Loaded. Code Editor initialization placeholder is active.');
    });
</script>
@endsection

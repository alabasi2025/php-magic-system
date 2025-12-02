@php
    // هذا الملف يمثل واجهة عرض للتقارير المحاسبية المولدة.
    // يستخدم Tailwind CSS لتصميم الواجهة ويدعم اللغة العربية (RTL).
    // يفترض أن البيانات (مثل $reportData, $reportTitle, $reportParameters) يتم تمريرها من المتحكم (Controller).
    // يفترض أن التخطيط الرئيسي (layouts.app) يتضمن Tailwind CSS و Alpine.js.
@endphp

@extends('layouts.app')

{{-- تعيين اتجاه الصفحة إلى اليمين لليمين (RTL) --}}
@section('html_attributes') dir="rtl" lang="ar" @endsection

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- عنوان التقرير --}}
        <header class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white text-right border-b pb-2">
                {{ $reportTitle ?? 'تقرير محاسبي مُولَّد' }}
            </h1>
        </header>

        {{-- منطقة عرض معلمات التقرير --}}
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6 p-4 text-right">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-3 border-b pb-2">
                معلمات التقرير
            </h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2">
                {{-- مثال على عرض المعلمات --}}
                @if (isset($reportParameters))
                    @foreach ($reportParameters as $key => $value)
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $key }}
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $value }}
                            </dd>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        لم يتم تحديد معلمات لهذا التقرير.
                    </p>
                @endif
            </dl>
        </div>

        {{-- منطقة عرض التقرير الفعلية مع دعم حالة التحميل (باستخدام Alpine.js افتراضياً) --}}
        <div x-data="{ isLoading: true }" x-init="setTimeout(() => { isLoading = false }, 1000)">
            {{-- حالة التحميل --}}
            <div x-show="isLoading" class="flex justify-center items-center h-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                {{-- تصميم بسيط للتحميل (Spinner) --}}
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
                <p class="mr-4 text-lg text-gray-600 dark:text-gray-300">
                    جاري تحميل التقرير...
                </p>
            </div>

            {{-- عرض التقرير بعد التحميل --}}
            <div x-show="!isLoading" x-cloak>
                <div class="flex justify-end mb-4">
                    {{-- زر طباعة (مثال على وظيفة إضافية) --}}
                    <button onclick="window.print()" class="flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        {{-- أيقونة طباعة افتراضية (يفترض توفرها في التخطيط الرئيسي) --}}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m0 0v2a2 2 0 002 2h4a2 2 0 002-2v-2m-4-8h.01M7 12h.01M17 12h.01"></path></svg>
                        طباعة التقرير
                    </button>
                </div>

                {{-- جدول عرض بيانات التقرير --}}
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 dark:border-gray-700 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-right">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            {{-- رؤوس الأعمدة (مثال) --}}
                                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                التاريخ
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                الوصف
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                الحساب
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                مدين
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                دائن
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                الرصيد
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        {{-- حلقة تكرار لعرض بيانات التقرير --}}
                                        @forelse ($reportData ?? [] as $item)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150 ease-in-out">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $item['date'] }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $item['description'] }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{-- قد يكون اسم حساب من INTERMEDIATE_ACCOUNTS أو CASH_BOXES --}}
                                                    {{ $item['account_name'] }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400 font-semibold">
                                                    {{ number_format($item['debit'], 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400 font-semibold">
                                                    {{ number_format($item['credit'], 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-bold">
                                                    {{ number_format($item['balance'], 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                                    لا توجد بيانات لعرضها في هذا التقرير.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    {{-- قسم الإجماليات --}}
                                    <tfoot class="bg-gray-100 dark:bg-gray-700/70">
                                        <tr>
                                            <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-gray-900 dark:text-white uppercase">
                                                الإجمالي
                                            </td>
                                            <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-red-700 dark:text-red-300">
                                                {{ number_format($totals['total_debit'] ?? 0, 2) }}
                                            </td>
                                            <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-green-700 dark:text-green-300">
                                                {{ number_format($totals['total_credit'] ?? 0, 2) }}
                                            </td>
                                            <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                                {{ number_format($totals['final_balance'] ?? 0, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- إضافة سكريبتات خاصة بالصفحة، مثل تهيئة Alpine.js أو مكتبات الطباعة --}}
    {{-- يفترض أن Alpine.js متاح عالمياً، لذا لا حاجة لاستيراده هنا. --}}
    <script>
        // تعليق: يمكن إضافة منطق JavaScript إضافي هنا، مثل معالجة تصدير التقرير إلى PDF/Excel.
        // مثال: تهيئة مكتبة Chart.js إذا كان التقرير يتضمن رسوماً بيانية.
        document.addEventListener('DOMContentLoaded', function () {
            console.log('واجهة التقرير المحاسبي المولّد جاهزة.');
        });
    </script>
@endpush

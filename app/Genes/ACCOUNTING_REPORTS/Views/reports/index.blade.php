@extends('layouts.app')

{{-- تعليق: تحديد عنوان الصفحة --}}
@section('title', 'تقارير المحاسبة')

@section('content')
    {{-- تعليق: حاوية رئيسية مع تباعد (padding) وتصميم عصري --}}
    <div class="p-6 bg-white shadow-xl rounded-xl">
        {{-- تعليق: عنوان الصفحة --}}
        <h1 class="text-3xl font-extrabold text-gray-900 mb-6 border-b-4 border-indigo-500 pb-3">
            تقارير المحاسبة
        </h1>

        {{-- تعليق: قسم أدوات التحكم والفلاتر --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 space-y-4 md:space-y-0">
            {{-- تعليق: زر إنشاء تقرير جديد --}}
            <a href="{{ route('accounting_reports.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105 w-full md:w-auto text-center">
                <i class="fas fa-file-alt mr-2"></i> إنشاء تقرير جديد
            </a>

            {{-- تعليق: نموذج الفلترة المتقدمة (يمكن أن يكون AJAX) --}}
            <form action="{{ route('accounting_reports.index') }}" method="GET" class="flex flex-wrap gap-4 w-full md:w-auto items-center">
                {{-- تعليق: فلتر الفترة الزمنية --}}
                <input type="date" name="start_date" class="form-input rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2" placeholder="من تاريخ">
                <input type="date" name="end_date" class="form-input rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2" placeholder="إلى تاريخ">

                {{-- تعليق: فلتر الحسابات الوسيطة (INTERMEDIATE_ACCOUNTS) --}}
                <select name="intermediate_account_id" class="form-select rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2">
                    <option value="">جميع الحسابات الوسيطة</option>
                    {{-- تعليق: حلقة تكرار لعرض الحسابات الوسيطة --}}
                    {{-- @foreach ($intermediateAccounts as $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                    @endforeach --}}
                </select>

                {{-- تعليق: فلتر الصناديق النقدية (CASH_BOXES) --}}
                <select name="cash_box_id" class="form-select rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2">
                    <option value="">جميع الصناديق النقدية</option>
                    {{-- تعليق: حلقة تكرار لعرض الصناديق النقدية --}}
                    {{-- @foreach ($cashBoxes as $box)
                        <option value="{{ $box->id }}">{{ $box->name }}</option>
                    @endforeach --}}
                </select>

                {{-- تعليق: فلتر حسابات الشركاء (PARTNER_ACCOUNTING) --}}
                <select name="partner_account_id" class="form-select rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2">
                    <option value="">جميع حسابات الشركاء</option>
                    {{-- تعليق: حلقة تكرار لعرض حسابات الشركاء --}}
                    {{-- @foreach ($partnerAccounts as $partner)
                        <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                    @endforeach --}}
                </select>

                {{-- تعليق: زر تطبيق الفلاتر --}}
                <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg shadow transition duration-300 ease-in-out">
                    <i class="fas fa-filter mr-1"></i> تصفية
                </button>
            </form>
        </div>

        {{-- تعليق: جدول عرض التقارير أو نتائج التصفية --}}
        <div class="overflow-x-auto bg-gray-50 rounded-lg shadow-inner">
            <table class="min-w-full divide-y divide-gray-200">
                {{-- تعليق: رأس الجدول --}}
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            نوع التقرير
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الفترة الزمنية
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحساب/الصندوق/الشريك
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            تاريخ الإنشاء
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                {{-- تعليق: جسم الجدول --}}
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- تعليق: حلقة تكرار لعرض صفوف التقارير (بيانات وهمية كمثال) --}}
                    {{-- @forelse ($reports as $report) --}}
                        <tr class="hover:bg-indigo-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                1
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                تقرير كشف حساب
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                2024-01-01 إلى 2024-12-31
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                الصندوق الرئيسي
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                2025-12-02
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{-- تعليق: زر عرض التقرير --}}
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-eye"></i> عرض
                                </a>
                                {{-- تعليق: زر تحميل التقرير --}}
                                <a href="#" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-download"></i> تحميل
                                </a>
                            </td>
                        </tr>
                    {{-- @empty --}}
                        {{-- تعليق: رسالة في حال عدم وجود تقارير --}}
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 text-lg">
                                لا توجد تقارير محاسبية متاحة حالياً.
                            </td>
                        </tr>
                    {{-- @endforelse --}}
                </tbody>
            </table>
        </div>

        {{-- تعليق: قسم الترقيم (Pagination) --}}
        <div class="mt-6">
            {{-- {{ $reports->links() }} --}}
        </div>
    </div>
@endsection

{{-- تعليق: قسم السكربتات الإضافية (مثل تهيئة مكتبة اختيار متقدمة) --}}
@push('scripts')
    <script>
        // تعليق: يمكن إضافة سكربتات هنا لتهيئة مكتبات مثل Select2 أو لغة جافاسكربت للتعامل مع الفلاتر
        console.log('Accounting Reports Index View Loaded.');
    </script>
@endpush

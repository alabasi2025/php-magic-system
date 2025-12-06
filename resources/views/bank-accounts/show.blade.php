@extends('layouts.app') {{-- افتراض وجود ملف تخطيط رئيسي --}}

@section('content')
<div dir="rtl" class="p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">
    {{-- رأس الصفحة وأزرار الإجراءات --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-4 sm:mb-0">
            تفاصيل الحساب البنكي: <span class="text-[#0052CC]">{{ $bankAccount->bank_name ?? 'اسم البنك' }}</span>
        </h1>
        <div class="flex space-x-2 space-x-reverse">
            {{-- زر رجوع --}}
            <a href="{{ route('bank_accounts.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0052CC]">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
            {{-- زر طباعة --}}
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0052CC]">
                <i class="fas fa-print ml-2"></i>
                طباعة
            </button>
            {{-- زر تعديل --}}
            <a href="{{ route('bank_accounts.edit', $bankAccount->id ?? 1) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#0052CC] hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0052CC]">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
        </div>
    </div>

    {{-- شبكة المحتوى الرئيسية --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- العمود الأيمن: المعلومات الأساسية والمالية --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. قسم المعلومات الأساسية --}}
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        <i class="fas fa-info-circle ml-2 text-[#0052CC]"></i>
                        المعلومات الأساسية للبنك
                    </h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">اسم البنك</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $bankAccount->bank_name ?? 'البنك الأهلي' }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">رقم الحساب</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $bankAccount->account_number ?? '1234567890' }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">رقم الآيبان (IBAN)</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $bankAccount->iban ?? 'SA0380000000608010167519' }}</dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">نوع الحساب</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $bankAccount->account_type ?? 'جاري' }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">الحالة</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                @if(($bankAccount->status ?? 'نشط') == 'نشط')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">نشط</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">معطل</span>
                                @endif
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">الحساب المحاسبي المرتبط</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $bankAccount->account_chart_name ?? '10101 - الصندوق والبنك' }}</dd>
                        </div>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">ملاحظات</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $bankAccount->notes ?? 'لا توجد ملاحظات.' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- 2. قسم المعلومات المالية --}}
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        <i class="fas fa-money-bill-wave ml-2 text-[#0052CC]"></i>
                        المعلومات المالية
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    {{-- بطاقة الرصيد الحالي --}}
                    <div class="bg-blue-50 border-r-4 border-[#0052CC] p-5 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <i class="fas fa-wallet text-2xl text-[#0052CC] ml-3"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500 truncate">الرصيد الحالي</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">
                                    {{ number_format($bankAccount->current_balance ?? 150000.50, 2) }} <span class="text-base font-normal">{{ $bankAccount->currency ?? 'ريال' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- بطاقة الرصيد الافتتاحي --}}
                    <div class="bg-gray-50 border-r-4 border-gray-300 p-5 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <i class="fas fa-piggy-bank text-2xl text-gray-600 ml-3"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500 truncate">الرصيد الافتتاحي</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">
                                    {{ number_format($bankAccount->opening_balance ?? 100000.00, 2) }} <span class="text-base font-normal">{{ $bankAccount->currency ?? 'ريال' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- بطاقة عدد المعاملات --}}
                    <div class="bg-gray-50 border-r-4 border-gray-300 p-5 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <i class="fas fa-exchange-alt text-2xl text-gray-600 ml-3"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-500 truncate">عدد المعاملات</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">
                                    {{ $bankAccount->transactions_count ?? 45 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- العمود الأيسر: معلومات إضافية (يمكن استخدامها لاحقًا للرسم البياني أو ملخص) --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-chart-line ml-2 text-[#0052CC]"></i>
                    ملخص الأداء (Placeholder)
                </h3>
                <p class="text-sm text-gray-500">
                    يمكن إضافة رسم بياني لتطور الرصيد أو ملخص شهري للمعاملات هنا.
                </p>
            </div>
        </div>
    </div>

    {{-- 3. قسم آخر 10 معاملات (سيتم إضافته في المرحلة التالية) --}}
    <div class="mt-6">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-list-alt ml-2 text-[#0052CC]"></i>
                    آخر 10 معاملات (سندات قبض/صرف)
                </h3>
            </div>
            <div class="p-4">
                {{-- جدول المعاملات --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    التاريخ
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    النوع
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المرجع
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الوصف
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المبلغ
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- مثال على سند قبض --}}
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-12-01</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">سند قبض</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">REC-00123</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">دفع فاتورة العميل أحمد</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">+ 5,000.00 ريال</td>
                            </tr>
                            {{-- مثال على سند صرف --}}
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-11-28</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">سند صرف</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">PAY-00456</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">دفع إيجار المكتب</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">- 2,500.00 ريال</td>
                            </tr>
                            {{-- يمكن تكرار الصفوف هنا باستخدام حلقة foreach --}}
                            @forelse ($bankAccount->latestTransactions ?? [] as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($transaction->type == 'receipt')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">سند قبض</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">سند صرف</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transaction->reference }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium @if($transaction->type == 'receipt') text-green-600 @else text-red-600 @endif">
                                        @if($transaction->type == 'receipt') + @else - @endif {{ number_format($transaction->amount, 2) }} {{ $bankAccount->currency ?? 'ريال' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        لا توجد معاملات حديثة لهذا الحساب.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- تضمين Font Awesome و Tailwind CSS (افتراضياً يتم تضمينهما في layout.app) --}}
{{-- إذا لم يكن كذلك، يجب إضافة الروابط هنا --}}
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" /> --}}
{{-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> --}}

@extends('layouts.app')

@section('title', 'إدارة الحسابات البنكية')

@section('content')
    {{-- تعيين اتجاه الصفحة إلى اليمين لليسار (RTL) --}}
    <div dir="rtl" class="p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">

        {{-- العنوان الرئيسي وزر الإضافة --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">إدارة الحسابات البنكية</h1>
            <a href="{{ route('bank-accounts.create') }}"
               style="background-color: #0052CC;"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                <i class="fas fa-plus ml-2"></i>
                إضافة حساب بنكي جديد
            </a>
        </div>

        {{-- 1. بطاقات الإحصائيات (Stats Cards) --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5 mb-8">
            {{-- بيانات وهمية للإحصائيات --}}
            @php
                $stats = [
                    ['title' => 'إجمالي البنوك', 'value' => '12', 'icon' => 'fas fa-university', 'color' => 'bg-blue-500'],
                    ['title' => 'الحسابات النشطة', 'value' => '9', 'icon' => 'fas fa-check-circle', 'color' => 'bg-green-500'],
                    ['title' => 'إجمالي الأرصدة', 'value' => '4,500,000 ر.س', 'icon' => 'fas fa-money-bill-wave', 'color' => 'bg-indigo-500'],
                    ['title' => 'عدد المعاملات', 'value' => '1,250', 'icon' => 'fas fa-exchange-alt', 'color' => 'bg-yellow-500'],
                    ['title' => 'متوسط الرصيد', 'value' => '375,000 ر.س', 'icon' => 'fas fa-chart-line', 'color' => 'bg-red-500'],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="{{ $stat['icon'] }} text-2xl text-white p-3 rounded-full {{ $stat['color'] }}"></i>
                            </div>
                            <div class="mr-4">
                                <p class="text-sm font-medium text-gray-500 truncate">{{ $stat['title'] }}</p>
                                <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stat['value'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 2. نظام البحث والفلترة --}}
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <form action="#" method="GET" class="space-y-4 lg:space-y-0 lg:flex lg:gap-4 lg:items-end">
                {{-- البحث باسم البنك --}}
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700">البحث باسم البنك</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="text" name="search" id="search"
                               class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 pl-4 sm:text-sm border-gray-300 rounded-md"
                               placeholder="ابحث باسم البنك أو رقم الحساب">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                {{-- فلتر حسب الحالة --}}
                <div class="w-full lg:w-1/5">
                    <label for="status" class="block text-sm font-medium text-gray-700">الحالة</label>
                    <select id="status" name="status"
                            class="mt-1 block w-full pl-10 pr-4 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">الكل</option>
                        <option value="active">نشط</option>
                        <option value="inactive">معطل</option>
                    </select>
                </div>

                {{-- فلتر حسب النوع --}}
                <div class="w-full lg:w-1/5">
                    <label for="type" class="block text-sm font-medium text-gray-700">نوع الحساب</label>
                    <select id="type" name="type"
                            class="mt-1 block w-full pl-10 pr-4 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">الكل</option>
                        <option value="current">جاري</option>
                        <option value="saving">توفير</option>
                        <option value="investment">استثماري</option>
                    </select>
                </div>

                {{-- زر البحث --}}
                <div class="w-full lg:w-auto">
                    <button type="submit"
                            style="background-color: #0052CC;"
                            class="w-full lg:w-auto inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-filter ml-2"></i>
                        تطبيق الفلاتر
                    </button>
                </div>
            </form>
        </div>

        {{-- 3. جدول عرض الحسابات البنكية --}}
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        اسم البنك
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        رقم الحساب
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نوع الحساب
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الرصيد الحالي
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الحالة
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">الإجراءات</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- بيانات وهمية للحسابات --}}
                                @php
                                    $banks = [
                                        ['name' => 'البنك الأهلي السعودي', 'account_number' => '1234567890', 'type' => 'جاري', 'balance' => '1,200,000 ر.س', 'status' => 'نشط'],
                                        ['name' => 'مصرف الراجحي', 'account_number' => '0987654321', 'type' => 'توفير', 'balance' => '500,000 ر.س', 'status' => 'نشط'],
                                        ['name' => 'بنك الرياض', 'account_number' => '1122334455', 'type' => 'استثماري', 'balance' => '2,800,000 ر.س', 'status' => 'معطل'],
                                        ['name' => 'البنك السعودي الفرنسي', 'account_number' => '5544332211', 'type' => 'جاري', 'balance' => '150,000 ر.س', 'status' => 'نشط'],
                                    ];
                                @endphp

                                @foreach ($banks as $bank)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $bank['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $bank['account_number'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $bank['type'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $bank['balance'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($bank['status'] == 'نشط')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $bank['status'] }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    {{ $bank['status'] }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                            {{-- أزرار الإجراءات --}}
                                            <div class="flex space-x-2 space-x-reverse">
                                                {{-- عرض --}}
                                                <a href="{{ route('bank-accounts.show', 1) }}"
                                                   class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition duration-150 ease-in-out"
                                                   title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                {{-- تعديل --}}
                                                <a href="{{ route('bank-accounts.edit', 1) }}"
                                                   class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-50 transition duration-150 ease-in-out"
                                                   title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{-- تفعيل/تعطيل --}}
                                                <button type="button"
                                                        class="text-{{ $bank['status'] == 'نشط' ? 'red' : 'green' }}-600 hover:text-{{ $bank['status'] == 'نشط' ? 'red' : 'green' }}-900 p-1 rounded-full hover:bg-{{ $bank['status'] == 'نشط' ? 'red' : 'green' }}-50 transition duration-150 ease-in-out"
                                                        title="{{ $bank['status'] == 'نشط' ? 'تعطيل' : 'تفعيل' }}">
                                                    <i class="fas fa-{{ $bank['status'] == 'نشط' ? 'toggle-off' : 'toggle-on' }}"></i>
                                                </button>
                                                {{-- حذف --}}
                                                <button type="button"
                                                        class="text-gray-600 hover:text-gray-900 p-1 rounded-full hover:bg-gray-50 transition duration-150 ease-in-out"
                                                        title="حذف">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- مثال على التجاوب (Responsive) --}}
        <div class="mt-8 text-center text-sm text-gray-500">
            <p class="hidden sm:block">هذه الواجهة متجاوبة وتظهر بشكل جيد على الشاشات الكبيرة والمتوسطة.</p>
            <p class="sm:hidden">الواجهة متجاوبة وتظهر بشكل جيد على شاشات الهواتف المحمولة.</p>
        </div>

    </div>
@endsection

{{-- تضمين مكتبة Font Awesome (افتراضياً يتم تضمينها في layout.app) --}}
{{-- @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
@endpush --}}

{{-- تضمين مكتبة Tailwind CSS (افتراضياً يتم تضمينها في layout.app) --}}
{{-- @push('styles')
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
@endpush --}}

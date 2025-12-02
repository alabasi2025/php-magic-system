@extends('layouts.app')

@section('title', 'معاملات الصندوق')

@section('content')
<div class="container mx-auto px-4 sm:px-8">
    <div class="py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold leading-tight text-gray-800">
                معاملات الصندوق: {{ $cashBox->name ?? 'غير محدد' }}
            </h2>
            <a href="{{ route('cash_boxes.transactions.create', $cashBox->id ?? 0) }}"
               class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out shadow-md">
                + إضافة معاملة جديدة
            </a>
        </div>

        <!-- قسم الفلاتر -->
        <div class="bg-white p-4 rounded-lg shadow-lg mb-6">
            <form action="{{ route('cash_boxes.transactions.index', $cashBox->id ?? 0) }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <!-- فلتر التاريخ من -->
                <div class="w-full sm:w-auto">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">التاريخ من</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                </div>

                <!-- فلتر التاريخ إلى -->
                <div class="w-full sm:w-auto">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">التاريخ إلى</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                </div>

                <!-- فلتر النوع -->
                <div class="w-full sm:w-auto">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">نوع المعاملة</label>
                    <select id="type" name="type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                        <option value="">الكل</option>
                        <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>إيداع</option>
                        <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>سحب</option>
                    </select>
                </div>

                <!-- زر تطبيق الفلاتر -->
                <div class="w-full sm:w-auto">
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out shadow-md w-full sm:w-auto">
                        تطبيق الفلاتر
                    </button>
                </div>
            </form>
        </div>

        <!-- جدول المعاملات -->
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow-lg rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                التاريخ
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                النوع
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                المبلغ
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                الوصف
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $transaction->created_at->format('Y-m-d H:i') }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                @if ($transaction->type === 'deposit')
                                    <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                        <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                        <span class="relative">إيداع</span>
                                    </span>
                                @else
                                    <span class="relative inline-block px-3 py-1 font-semibold text-red-900 leading-tight">
                                        <span aria-hidden class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                        <span class="relative">سحب</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                <p class="text-gray-900 whitespace-no-wrap">{{ number_format($transaction->amount, 2) }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $transaction->description }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">عرض</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                لا توجد معاملات لعرضها.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- قسم الترقيم (Pagination) -->
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>

    </div>
</div>
@endsection

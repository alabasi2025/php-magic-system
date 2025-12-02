@extends('layouts.app')

@section('title', 'معاملات الشريك')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">معاملات الشريك</h1>
        <a href="{{ route('partners.transactions.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
            + إضافة معاملة
        </a>
    </div>

    <!-- قسم الفلاتر -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">تصفية المعاملات</h2>
        <form action="{{ route('partners.transactions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-1">نوع المعاملة</label>
                <select id="transaction_type" name="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">الكل</option>
                    <option value="deposit">إيداع</option>
                    <option value="withdrawal">سحب</option>
                    <option value="profit">ربح</option>
                    <option value="loss">خسارة</option>
                </select>
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
                <input type="date" id="start_date" name="start_date" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
                <input type="date" id="end_date" name="end_date" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="md:col-span-1">
                <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
                    تطبيق الفلاتر
                </button>
            </div>
        </form>
    </div>

    <!-- جدول المعاملات -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التاريخ
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            النوع
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المبلغ
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الوصف
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- حلقة لعرض المعاملات --}}
                    @forelse ($transactions as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->date }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $typeClasses = [
                                    'deposit' => 'bg-green-100 text-green-800',
                                    'withdrawal' => 'bg-red-100 text-red-800',
                                    'profit' => 'bg-blue-100 text-blue-800',
                                    'loss' => 'bg-yellow-100 text-yellow-800',
                                ];
                                $typeText = [
                                    'deposit' => 'إيداع',
                                    'withdrawal' => 'سحب',
                                    'profit' => 'ربح',
                                    'loss' => 'خسارة',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeClasses[$transaction->type] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $typeText[$transaction->type] ?? $transaction->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            {{ number_format($transaction->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate">
                            {{ $transaction->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('partners.transactions.show', $transaction->id) }}" class="text-indigo-600 hover:text-indigo-900 ml-2">عرض</a>
                            <a href="{{ route('partners.transactions.edit', $transaction->id) }}" class="text-yellow-600 hover:text-yellow-900">تعديل</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            لا توجد معاملات لعرضها.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- قسم الروابط (Pagination) --}}
        <div class="p-4">
            {{-- {{ $transactions->links() }} --}}
        </div>
    </div>
</div>
@endsection

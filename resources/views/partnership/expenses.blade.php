@extends('layouts.app')
@section('title', 'إدارة المصروفات')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">إدارة المصروفات</h1>
            <button onclick="alert('إضافة مصروف جديد')" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                إضافة مصروف
            </button>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-red-50 rounded-lg p-4 border-r-4 border-red-500">
                <p class="text-red-600 text-sm">إجمالي المصروفات</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ number_format($expenses->sum('amount') ?? 0) }} ريال</h3>
            </div>
            <div class="bg-orange-50 rounded-lg p-4 border-r-4 border-orange-500">
                <p class="text-orange-600 text-sm">مصروفات هذا الشهر</p>
                <h3 class="text-2xl font-bold text-gray-800">0 ريال</h3>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 border-r-4 border-purple-500">
                <p class="text-purple-600 text-sm">عدد المصروفات</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $expenses->total() ?? 0 }}</h3>
            </div>
        </div>

        <!-- جدول المصروفات -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">#</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">الوصف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($expenses ?? [] as $expense)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $expense->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $expense->expense_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">{{ number_format($expense->amount) }} ريال</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">{{ $expense->expense_type }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $expense->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button class="text-blue-600 hover:text-blue-900 ml-3">عرض</button>
                            <button class="text-indigo-600 hover:text-indigo-900 ml-3">تعديل</button>
                            <button class="text-red-600 hover:text-red-900">حذف</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <p class="text-lg">لا توجد مصروفات مسجلة</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($expenses) && $expenses->hasPages())
        <div class="mt-6">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

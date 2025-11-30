@extends('layouts.app')
@section('title', 'إدارة الشركاء')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">إدارة الشركاء</h1>
            <button onclick="alert('إضافة شريك جديد')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                إضافة شريك
            </button>
        </div>

        <!-- نموذج البحث -->
        <div class="mb-6">
            <div class="flex gap-4">
                <input type="text" id="searchInput" placeholder="البحث عن شريك..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option>جميع الشركاء</option>
                    <option>نشط</option>
                    <option>غير نشط</option>
                </select>
            </div>
        </div>

        <!-- جدول الشركاء -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">الاسم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">البريد الإلكتروني</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">الهاتف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">نسبة الملكية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="partnersTableBody" class="divide-y divide-gray-200">
                    @forelse($partners ?? [] as $partner)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $partner->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $partner->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $partner->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $partner->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $partner->shares->sum('share_percentage') }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($partner->is_active)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">نشط</span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">غير نشط</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="alert('عرض {{ $partner->name }}')" class="text-blue-600 hover:text-blue-900 ml-3">عرض</button>
                            <button onclick="alert('تعديل {{ $partner->name }}')" class="text-indigo-600 hover:text-indigo-900 ml-3">تعديل</button>
                            <button onclick="confirm('هل تريد حذف {{ $partner->name }}؟')" class="text-red-600 hover:text-red-900">حذف</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="mt-4 text-lg">لا يوجد شركاء حالياً</p>
                            <p class="mt-2">ابدأ بإضافة شريك جديد</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($partners) && $partners->hasPages())
        <div class="mt-6">
            {{ $partners->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

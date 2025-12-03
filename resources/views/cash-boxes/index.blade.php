@extends('layouts.app')
@section('title', 'إدارة الصناديق النقدية')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">إدارة الصناديق النقدية</h1>
            <p class="text-gray-600 mt-2">إدارة وتتبع الصناديق النقدية المرتبطة بالحسابات الوسيطة</p>
        </div>
        <a href="{{ route('cash-boxes.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md transition-colors">
            <i class="fas fa-plus mr-2"></i>إضافة صندوق جديد
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if(isset($error))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
        <p class="text-red-700">{{ $error }}</p>
    </div>
    @endif

    @if($cashBoxes->isEmpty())
    <div class="bg-blue-50 border-l-4 border-blue-500 p-8 text-center rounded-md">
        <i class="fas fa-cash-register text-blue-500 text-5xl mb-4"></i>
        <p class="text-blue-700 text-xl font-semibold">لا توجد صناديق نقدية حالياً</p>
        <p class="text-blue-600 mt-2">ابدأ بإنشاء صندوق نقدي جديد مرتبط بحساب وسيط</p>
        <a href="{{ route('cash-boxes.create') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
            إنشاء صندوق الآن
        </a>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الرمز
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        اسم الصندوق
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الوحدة التنظيمية
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الحساب الوسيط
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الرصيد
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الحالة
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الإجراءات
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($cashBoxes as $cashBox)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">
                            {{ $cashBox->code }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-cash-register text-blue-600"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $cashBox->name }}
                                </div>
                                @if($cashBox->description)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ Str::limit($cashBox->description, 50) }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $cashBox->unit ? $cashBox->unit->name : 'غير محدد' }}
                        </div>
                        @if($cashBox->unit)
                        <div class="text-xs text-gray-500">
                            {{ $cashBox->unit->code }}
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($cashBox->intermediateAccount)
                        <div class="text-sm text-gray-900">
                            {{ $cashBox->intermediateAccount->name }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $cashBox->intermediateAccount->code }}
                        </div>
                        @else
                        <span class="text-xs text-red-500">غير مرتبط</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-semibold {{ $cashBox->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($cashBox->balance, 2) }} ريال
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($cashBox->is_active)
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> نشط
                        </span>
                        @else
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i> غير نشط
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <a href="{{ route('cash-boxes.show', $cashBox->id) }}" 
                               class="text-blue-600 hover:text-blue-900 transition-colors" 
                               title="عرض">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('cash-boxes.edit', $cashBox->id) }}" 
                               class="text-yellow-600 hover:text-yellow-900 transition-colors" 
                               title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('cash-boxes.destroy', $cashBox->id) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الصندوق؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900 transition-colors" 
                                        title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $cashBoxes->links() }}
    </div>
    @endif
</div>
@endsection

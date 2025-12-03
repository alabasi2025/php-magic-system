@extends('layouts.app')
@section('title', 'إدارة الصناديق النقدية')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header with Gradient -->
    <div class="bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 rounded-2xl shadow-2xl p-8 mb-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold mb-2 flex items-center">
                    <i class="fas fa-cash-register mr-3"></i>
                    إدارة الصناديق النقدية
                </h1>
                <p class="text-green-100 text-lg">
                    <i class="fas fa-link mr-2"></i>
                    إدارة وتتبع الصناديق النقدية المرتبطة بالحسابات الوسيطة
                </p>
            </div>
            <a href="{{ route('cash-boxes.create') }}" 
               class="bg-white text-green-600 hover:bg-green-50 px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>إضافة صندوق جديد
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg shadow-md">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
            <p class="text-green-700 font-semibold">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg shadow-md">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 text-2xl mr-3"></i>
            <p class="text-red-700 font-semibold">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if(isset($error))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg shadow-md">
        <div class="flex items-center">
            <i class="fas fa-times-circle text-red-500 text-2xl mr-3"></i>
            <p class="text-red-700 font-semibold">{{ $error }}</p>
        </div>
    </div>
    @endif

    @if($cashBoxes->isEmpty())
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-dashed border-green-300 rounded-2xl p-12 text-center shadow-lg">
        <div class="max-w-md mx-auto">
            <i class="fas fa-cash-register text-green-400 text-7xl mb-6 animate-bounce"></i>
            <h3 class="text-2xl font-bold text-green-800 mb-3">لا توجد صناديق نقدية حالياً</h3>
            <p class="text-green-600 text-lg mb-6">ابدأ بإنشاء صندوق نقدي جديد مرتبط بحساب وسيط</p>
            <a href="{{ route('cash-boxes.create') }}" 
               class="inline-block bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-plus-circle mr-2"></i>إنشاء صندوق الآن
            </a>
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-emerald-600">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-hashtag mr-2"></i>الرمز
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-tag mr-2"></i>الاسم
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-building mr-2"></i>الوحدة
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-exchange-alt mr-2"></i>الحساب الوسيط
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-wallet mr-2"></i>الرصيد
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-toggle-on mr-2"></i>الحالة
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-cogs mr-2"></i>الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($cashBoxes as $cashBox)
                    <tr class="hover:bg-green-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">
                                {{ $cashBox->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-cash-register text-green-600 ml-3"></i>
                                <span class="text-gray-900 font-semibold">{{ $cashBox->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-700">{{ $cashBox->unit->name ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-700">{{ $cashBox->intermediateAccount->name ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-lg font-bold text-green-600">
                                {{ number_format($cashBox->balance, 2) }} ريال
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($cashBox->is_active)
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold flex items-center w-fit">
                                    <i class="fas fa-check-circle mr-1"></i>نشط
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold flex items-center w-fit">
                                    <i class="fas fa-times-circle mr-1"></i>غير نشط
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex space-x-2 space-x-reverse">
                                <a href="{{ route('cash-boxes.show', $cashBox->id) }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg transition-colors shadow-md hover:shadow-lg transform hover:scale-105">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cash-boxes.edit', $cashBox->id) }}" 
                                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg transition-colors shadow-md hover:shadow-lg transform hover:scale-105">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('cash-boxes.destroy', $cashBox->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('هل أنت متأكد من حذف هذا الصندوق؟')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition-colors shadow-md hover:shadow-lg transform hover:scale-105">
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
    </div>

    @if($cashBoxes->hasPages())
    <div class="mt-6">
        {{ $cashBoxes->links() }}
    </div>
    @endif
    @endif
</div>

<style>
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}
.animate-bounce {
    animation: bounce 2s infinite;
}
</style>
@endsection

@extends('layouts.app')
@section('title', 'الأدلة المحاسبية')
@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header with Gradient -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8 mb-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold mb-2 flex items-center">
                    <i class="fas fa-book-open mr-3"></i>
                    الأدلة المحاسبية المبسطة
                </h1>
                <p class="text-indigo-100 text-lg">
                    <i class="fas fa-info-circle mr-2"></i>
                    إدارة وتنظيم الأدلة المحاسبية للوحدات التنظيمية
                </p>
            </div>
            <a href="{{ route('chart-of-accounts.create') }}" 
               class="bg-white text-indigo-600 hover:bg-indigo-50 px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>إضافة دليل جديد
            </a>
        </div>
    </div>

    @if(isset($error))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg shadow-md animate-pulse">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
            <p class="text-red-700 font-semibold">{{ $error }}</p>
        </div>
    </div>
    @endif

    @if($chartGroups->isEmpty())
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-dashed border-blue-300 rounded-2xl p-12 text-center shadow-lg">
        <div class="max-w-md mx-auto">
            <i class="fas fa-folder-open text-blue-400 text-7xl mb-6 animate-bounce"></i>
            <h3 class="text-2xl font-bold text-blue-800 mb-3">لا توجد أدلة محاسبية حالياً</h3>
            <p class="text-blue-600 text-lg mb-6">ابدأ رحلتك المحاسبية بإنشاء دليل جديد</p>
            <a href="{{ route('chart-of-accounts.create') }}" 
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-plus-circle mr-2"></i>إنشاء دليل الآن
            </a>
        </div>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($chartGroups as $group)
        <a href="{{ route('chart-of-accounts.show', $group->id) }}" 
           class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border-t-4 border-indigo-500 transform hover:-translate-y-2">
            <div class="flex items-start justify-between mb-4">
                <div class="bg-gradient-to-br from-indigo-100 to-purple-100 p-4 rounded-xl group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-folder text-indigo-600 text-3xl"></i>
                </div>
                <span class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-md">
                    {{ $group->type_label }}
                </span>
            </div>
            
            <h3 class="text-2xl font-bold text-gray-800 mb-3 group-hover:text-indigo-600 transition-colors">
                {{ $group->name }}
            </h3>
            
            @if($group->description)
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($group->description, 100) }}</p>
            @endif
            
            <div class="flex items-center justify-between text-sm text-gray-500 mt-4 pt-4 border-t border-gray-200">
                <span class="flex items-center bg-indigo-50 px-3 py-2 rounded-lg">
                    <i class="fas fa-list mr-2 text-indigo-600"></i>
                    <span class="font-semibold text-indigo-700">{{ $group->accounts_count }} حساب</span>
                </span>
                <span class="flex items-center bg-purple-50 px-3 py-2 rounded-lg">
                    <i class="fas fa-code mr-2 text-purple-600"></i>
                    <span class="font-semibold text-purple-700">{{ $group->code }}</span>
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .8; }
}
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}
.animate-bounce {
    animation: bounce 2s infinite;
}
</style>
@endsection

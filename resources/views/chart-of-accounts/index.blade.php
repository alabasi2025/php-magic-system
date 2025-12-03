@extends('layouts.app')
@section('title', 'الأدلة المحاسبية')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">الأدلة المحاسبية المبسطة</h1>
        <a href="{{ route('chart-of-accounts.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>إضافة دليل جديد
        </a>
    </div>

    @if(isset($error))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
        <p class="text-red-700">{{ $error }}</p>
    </div>
    @endif

    @if($chartGroups->isEmpty())
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 text-center">
        <i class="fas fa-info-circle text-blue-500 text-4xl mb-3"></i>
        <p class="text-blue-700 text-lg">لا توجد أدلة محاسبية حالياً</p>
        <p class="text-blue-600 mt-2">ابدأ بإنشاء دليل محاسبي جديد</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($chartGroups as $group)
        <a href="{{ route('chart-of-accounts.show', $group->id) }}" 
           class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border-t-4">
            <div class="flex items-start justify-between mb-4">
                <div class="bg-gray-100 p-3 rounded-lg">
                    <i class="fas fa-folder text-gray-500 text-2xl"></i>
                </div>
                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                    {{ $group->type_label }}
                </span>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $group->name }}</h3>
            
            @if($group->description)
            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($group->description, 100) }}</p>
            @endif
            
            <div class="flex items-center justify-between text-sm text-gray-500 mt-4 pt-4 border-t">
                <span>
                    <i class="fas fa-list mr-1"></i>
                    {{ $group->accounts_count }} حساب
                </span>
                <span>
                    <i class="fas fa-code mr-1"></i>
                    {{ $group->code }}
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection

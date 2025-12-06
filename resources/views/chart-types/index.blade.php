@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-layer-group text-indigo-600 mr-2"></i>
                أنواع الأدلة المحاسبية
            </h1>
            <p class="text-gray-600">إدارة أنواع الأدلة المحاسبية المتاحة في النظام</p>
        </div>
        <a href="{{ route('chart-types.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors shadow-lg">
            <i class="fas fa-plus ml-2"></i>
            إضافة نوع جديد
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Types Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($chartTypes as $type)
        <div class="bg-white rounded-xl shadow-lg p-6 border-r-4 border-{{ $type->color }}-500 hover:shadow-xl transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="bg-{{ $type->color }}-100 text-{{ $type->color }}-600 rounded-lg p-3">
                        <i class="fas {{ $type->icon }} text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">{{ $type->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $type->code }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $type->is_active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                    {{ $type->is_active ? 'نشط' : 'معطل' }}
                </span>
            </div>

            <p class="text-gray-600 text-sm mb-4">{{ $type->description }}</p>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-sort-numeric-down ml-1"></i>
                    ترتيب: {{ $type->sort_order }}
                </div>
                <div class="flex space-x-2 space-x-reverse">
                    <a href="{{ route('chart-types.edit', $type) }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('chart-types.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($chartTypes->isEmpty())
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
        <p class="text-gray-500 text-lg">لا توجد أنواع أدلة محاسبية</p>
        <a href="{{ route('chart-types.create') }}" class="inline-block mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors">
            <i class="fas fa-plus ml-2"></i>
            إضافة نوع جديد
        </a>
    </div>
    @endif
</div>
@endsection

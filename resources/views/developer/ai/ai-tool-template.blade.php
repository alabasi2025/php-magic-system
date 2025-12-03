@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg shadow-lg p-8 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">{{ $title }}</h1>
                    <p class="text-purple-100 text-lg">{{ $description }}</p>
                </div>
                <div class="text-6xl opacity-50">
                    <i class="{{ $icon }}"></i>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center py-12">
                <div class="text-6xl text-purple-600 mb-4">
                    <i class="fas fa-code"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">قيد التطوير</h2>
                <p class="text-gray-600 text-lg mb-8">
                    هذه الأداة قيد التطوير حالياً وستكون متاحة قريباً
                </p>
                <div class="flex justify-center space-x-4 space-x-reverse">
                    <a href="{{ route('developer.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-arrow-right ml-2"></i>
                        العودة لنظام المطور
                    </a>
                </div>
            </div>
        </div>

        <!-- Features Preview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            @foreach($features ?? [] as $feature)
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="text-3xl text-purple-600 mb-3">
                    <i class="{{ $feature['icon'] }}"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $feature['title'] }}</h3>
                <p class="text-gray-600">{{ $feature['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

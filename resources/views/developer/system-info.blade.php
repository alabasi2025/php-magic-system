@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-server text-green-500 mr-2"></i>
                معلومات النظام
            </h1>
            <a href="{{ route('developer.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-right mr-2"></i>
                العودة
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($info as $key => $value)
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-lg border border-gray-200 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">
                            {{ ucwords(str_replace('_', ' ', $key)) }}
                        </div>
                        <div class="text-lg font-bold text-gray-800">
                            {{ $value }}
                        </div>
                    </div>
                    <div class="text-3xl text-gray-300">
                        @switch($key)
                            @case('php_version')
                                <i class="fab fa-php"></i>
                                @break
                            @case('laravel_version')
                                <i class="fab fa-laravel"></i>
                                @break
                            @case('database')
                            @case('database_driver')
                                <i class="fas fa-database"></i>
                                @break
                            @case('memory_limit')
                                <i class="fas fa-memory"></i>
                                @break
                            @case('environment')
                                <i class="fas fa-cog"></i>
                                @break
                            @default
                                <i class="fas fa-info-circle"></i>
                        @endswitch
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

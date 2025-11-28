@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-database text-blue-500 mr-2"></i>
                معلومات قاعدة البيانات
            </h1>
            <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-right mr-2"></i>
                العودة
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- معلومات عامة -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow">
                <div class="text-sm opacity-80">اسم قاعدة البيانات</div>
                <div class="text-2xl font-bold mt-2">{{ $database }}</div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg shadow">
                <div class="text-sm opacity-80">عدد الجداول</div>
                <div class="text-2xl font-bold mt-2">{{ $total_tables }}</div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-lg shadow">
                <div class="text-sm opacity-80">إجمالي السجلات</div>
                <div class="text-2xl font-bold mt-2">{{ array_sum(array_column($tables, 'rows')) }}</div>
            </div>
        </div>

        <!-- جدول الجداول -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-table text-blue-500 mr-2"></i>
                الجداول
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg overflow-hidden">
                    <thead class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                        <tr>
                            <th class="px-6 py-3 text-right text-sm font-semibold">#</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold">اسم الجدول</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold">عدد السجلات</th>
                            <th class="px-6 py-3 text-right text-sm font-semibold">النسبة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($tables as $index => $table)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-800">{{ $table['name'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ number_format($table['rows']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $total = array_sum(array_column($tables, 'rows'));
                                    $percentage = $total > 0 ? ($table['rows'] / $total) * 100 : 0;
                                @endphp
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

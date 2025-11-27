@extends('layouts.app')

@section('title', 'خدمات API - SEMOP')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <div class="bg-white/20 p-4 rounded-lg">
                    <i class="fas fa-code text-4xl"></i>
                </div>
                <div>
                    <h1 class="text-4xl font-bold">خدمات API</h1>
                    <p class="text-indigo-100 mt-2">إدارة ومراقبة خدمات API RESTful</p>
                </div>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-bold hover:bg-indigo-50 transition-colors">
                    <i class="fas fa-book ml-2"></i>
                    التوثيق
                </button>
                <button class="bg-green-500 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-600 transition-colors">
                    <i class="fas fa-play ml-2"></i>
                    اختبار API
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">إجمالي الخدمات</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">37</p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-lg">
                    <i class="fas fa-server text-indigo-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">نشطة</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">35</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">الطلبات اليوم</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">15.2K</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-exchange-alt text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">متوسط الاستجابة</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">125ms</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-tachometer-alt text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">معدل النجاح</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">99.8%</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- API Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
        $services = [
            ['name' => 'Accounting API', 'endpoint' => '/api/accounting', 'requests' => '2.5K', 'status' => 'active', 'color' => 'green'],
            ['name' => 'Inventory API', 'endpoint' => '/api/inventory', 'requests' => '1.8K', 'status' => 'active', 'color' => 'blue'],
            ['name' => 'Sales API', 'endpoint' => '/api/sales', 'requests' => '3.2K', 'status' => 'active', 'color' => 'purple'],
            ['name' => 'Purchases API', 'endpoint' => '/api/purchases', 'requests' => '1.5K', 'status' => 'active', 'color' => 'orange'],
            ['name' => 'CRM API', 'endpoint' => '/api/crm', 'requests' => '2.1K', 'status' => 'active', 'color' => 'teal'],
            ['name' => 'HR API', 'endpoint' => '/api/hr', 'requests' => '980', 'status' => 'active', 'color' => 'indigo'],
            ['name' => 'Payroll API', 'endpoint' => '/api/payroll', 'requests' => '650', 'status' => 'active', 'color' => 'green'],
            ['name' => 'Assets API', 'endpoint' => '/api/assets', 'requests' => '420', 'status' => 'active', 'color' => 'gray'],
            ['name' => 'Manufacturing API', 'endpoint' => '/api/manufacturing', 'requests' => '1.2K', 'status' => 'active', 'color' => 'blue'],
        ];
        @endphp

        @foreach($services as $service)
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow border-t-4 border-{{ $service['color'] }}-500">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="bg-{{ $service['color'] }}-100 p-3 rounded-lg">
                        <i class="fas fa-code text-{{ $service['color'] }}-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $service['name'] }}</h3>
                        <p class="text-sm text-gray-600">{{ $service['endpoint'] }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">
                    <i class="fas fa-circle text-xs"></i> نشط
                </span>
            </div>
            
            <div class="space-y-2 mb-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">الطلبات اليوم:</span>
                    <span class="font-bold text-gray-800">{{ $service['requests'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">الاستجابة:</span>
                    <span class="font-bold text-green-600">120ms</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">معدل النجاح:</span>
                    <span class="font-bold text-green-600">99.9%</span>
                </div>
            </div>

            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="flex-1 bg-{{ $service['color'] }}-600 text-white px-4 py-2 rounded-lg hover:bg-{{ $service['color'] }}-700 transition-colors text-sm">
                    <i class="fas fa-play ml-1"></i>
                    اختبار
                </button>
                <button class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors text-sm">
                    <i class="fas fa-book ml-1"></i>
                    التوثيق
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- API Endpoints Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold text-gray-800">نقاط النهاية (Endpoints)</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-6 py-4 text-right">الطريقة</th>
                        <th class="px-6 py-4 text-right">المسار</th>
                        <th class="px-6 py-4 text-right">الوصف</th>
                        <th class="px-6 py-4 text-right">المصادقة</th>
                        <th class="px-6 py-4 text-right">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                    $endpoints = [
                        ['method' => 'GET', 'path' => '/api/accounting', 'desc' => 'جلب جميع السجلات', 'auth' => true],
                        ['method' => 'POST', 'path' => '/api/accounting', 'desc' => 'إنشاء سجل جديد', 'auth' => true],
                        ['method' => 'GET', 'path' => '/api/accounting/{id}', 'desc' => 'جلب سجل محدد', 'auth' => true],
                        ['method' => 'PUT', 'path' => '/api/accounting/{id}', 'desc' => 'تحديث سجل', 'auth' => true],
                        ['method' => 'DELETE', 'path' => '/api/accounting/{id}', 'desc' => 'حذف سجل', 'auth' => true],
                    ];
                    @endphp

                    @foreach($endpoints as $endpoint)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            @if($endpoint['method'] == 'GET')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">GET</span>
                            @elseif($endpoint['method'] == 'POST')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">POST</span>
                            @elseif($endpoint['method'] == 'PUT')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold">PUT</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">DELETE</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-mono text-sm text-gray-800">{{ $endpoint['path'] }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $endpoint['desc'] }}</td>
                        <td class="px-6 py-4">
                            @if($endpoint['auth'])
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">
                                    <i class="fas fa-lock"></i> مطلوبة
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-bold">
                                    عامة
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-indigo-600 hover:text-indigo-800 transition-colors">
                                <i class="fas fa-play"></i> اختبار
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

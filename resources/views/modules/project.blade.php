@extends('layouts.app')
@section('title', 'المشاريع')
@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">إدارة المشاريع</h1>
    <p class="text-gray-600 mb-6">نظام إدارة المشاريع والمهام</p>
    
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <p class="text-blue-700">
            <i class="fas fa-info-circle mr-2"></i>
            هذا النظام قيد التطوير. سيتم إضافة المزيد من المميزات قريباً.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-50 p-6 rounded-lg">
            <i class="fas fa-project-diagram text-blue-500 text-3xl mb-3"></i>
            <h3 class="font-bold text-lg mb-2">المشاريع</h3>
            <p class="text-gray-600 text-sm">إدارة جميع المشاريع</p>
        </div>
        
        <div class="bg-green-50 p-6 rounded-lg">
            <i class="fas fa-tasks text-green-500 text-3xl mb-3"></i>
            <h3 class="font-bold text-lg mb-2">المهام</h3>
            <p class="text-gray-600 text-sm">متابعة المهام والتقدم</p>
        </div>
        
        <div class="bg-purple-50 p-6 rounded-lg">
            <i class="fas fa-chart-line text-purple-500 text-3xl mb-3"></i>
            <h3 class="font-bold text-lg mb-2">التقارير</h3>
            <p class="text-gray-600 text-sm">تقارير الأداء والإنجاز</p>
        </div>
    </div>
</div>
@endsection

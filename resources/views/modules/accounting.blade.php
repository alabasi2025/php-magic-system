@extends('layouts.app')
@section('title', 'نظام المحاسبة')
@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">نظام المحاسبة</h1>
    <p class="text-gray-600">نظام محاسبة كامل مع دليل الحسابات والقيود والتقارير المالية</p>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-50 p-4 rounded-lg">
            <i class="fas fa-book text-blue-500 text-2xl mb-2"></i>
            <h3 class="font-bold">دليل الحسابات</h3>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <i class="fas fa-file-invoice text-green-500 text-2xl mb-2"></i>
            <h3 class="font-bold">القيود اليومية</h3>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <i class="fas fa-chart-bar text-purple-500 text-2xl mb-2"></i>
            <h3 class="font-bold">التقارير المالية</h3>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'نقاط الولاء')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">نقاط الولاء</h1>
            <p class="text-gray-600 mt-2">نظام نقاط الولاء والمكافآت للعملاء</p>
        </div>
        <button class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-3 rounded-lg">
            <i class="fas fa-plus ml-2"></i>
            إضافة جديد
        </button>
    </div>
    
    <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-8 rounded-lg border-2 border-amber-200 text-center">
        <i class="fas fa-star text-amber-500 text-6xl mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">نقاط الولاء</h2>
        <p class="text-gray-600">نظام نقاط الولاء والمكافآت للعملاء</p>
        <div class="mt-6">
            <span class="bg-amber-500 text-white px-4 py-2 rounded-full text-sm">قريباً</span>
        </div>
    </div>
</div>
@endsection

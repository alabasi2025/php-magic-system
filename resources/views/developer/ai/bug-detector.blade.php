@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg shadow-lg p-8 mb-8 text-white">
            <h1 class="text-4xl font-bold mb-2">تحسين الكود</h1>
            <p class="text-purple-100 text-lg">تحسين وإعادة هيكلة الكود تلقائياً باستخدام الذكاء الاصطناعي</p>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center py-12">
                <div class="text-6xl text-purple-600 mb-4"><i class="fas fa-magic"></i></div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">قيد التطوير</h2>
                <p class="text-gray-600 text-lg mb-8">هذه الأداة قيد التطوير حالياً وستكون متاحة قريباً</p>
                <a href="{{ route('developer.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i> العودة لنظام المطور
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

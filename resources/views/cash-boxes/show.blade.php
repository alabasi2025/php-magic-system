@extends('layouts.app')
@section('title', 'تفاصيل الصندوق: ' . $cashBox->name)
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <a href="{{ route('cash-boxes.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $cashBox->name }}</h1>
                    <p class="text-gray-600 mt-1">تفاصيل الصندوق النقدي</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 space-x-reverse">
                <a href="{{ route('cash-boxes.edit', $cashBox->id) }}" 
                   class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>تعديل
                </a>
                <form action="{{ route('cash-boxes.destroy', $cashBox->id) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الصندوق؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-trash mr-2"></i>حذف
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- المعلومات الأساسية -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>المعلومات الأساسية
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">اسم الصندوق</label>
                        <p class="text-lg font-semibold text-gray-800">{{ $cashBox->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">رمز الصندوق</label>
                        <p class="text-lg font-mono bg-gray-100 px-3 py-1 rounded inline-block">{{ $cashBox->code }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">الوحدة التنظيمية</label>
                        @if($cashBox->unit)
                        <p class="text-lg text-gray-800">{{ $cashBox->unit->name }}</p>
                        <p class="text-sm text-gray-500">{{ $cashBox->unit->code }}</p>
                        @else
                        <p class="text-gray-400">غير محدد</p>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">الحالة</label>
                        @if($cashBox->is_active)
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> نشط
                        </span>
                        @else
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i> غير نشط
                        </span>
                        @endif
                    </div>
                    
                    @if($cashBox->description)
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">الوصف</label>
                        <p class="text-gray-700 bg-gray-50 p-3 rounded-md">{{ $cashBox->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- الحساب الوسيط المرتبط -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-link text-blue-500 mr-2"></i>الحساب الوسيط المرتبط
                </h2>
                
                @if($cashBox->intermediateAccount)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-folder-open text-blue-600 text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                {{ $cashBox->intermediateAccount->name }}
                            </h3>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-500">الرمز:</span>
                                    <span class="font-mono bg-white px-2 py-1 rounded mr-2">{{ $cashBox->intermediateAccount->code }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">النوع:</span>
                                    <span class="text-gray-800 mr-2">حساب وسيط للصناديق</span>
                                </div>
                                @if($cashBox->intermediateAccount->chartGroup)
                                <div class="col-span-2">
                                    <span class="text-gray-500">الدليل المحاسبي:</span>
                                    <span class="text-gray-800 mr-2">{{ $cashBox->intermediateAccount->chartGroup->name }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                    <i class="fas fa-unlink text-red-500 text-3xl mb-2"></i>
                    <p class="text-red-700">لا يوجد حساب وسيط مرتبط بهذا الصندوق</p>
                </div>
                @endif
            </div>
        </div>

        <!-- الإحصائيات والمعلومات الجانبية -->
        <div class="lg:col-span-1">
            <!-- الرصيد -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white mb-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-semibold">الرصيد الحالي</h3>
                    <i class="fas fa-wallet text-2xl opacity-75"></i>
                </div>
                <p class="text-4xl font-bold mb-1">{{ number_format($cashBox->balance, 2) }}</p>
                <p class="text-green-100">ريال يمني</p>
            </div>

            <!-- معلومات التتبع -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-history text-gray-500 mr-2"></i>معلومات التتبع
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">تاريخ الإنشاء</label>
                        <p class="text-sm text-gray-800">
                            <i class="fas fa-calendar-plus text-gray-400 mr-1"></i>
                            {{ $cashBox->created_at->format('Y-m-d') }}
                        </p>
                        <p class="text-xs text-gray-500 mr-5">
                            {{ $cashBox->created_at->format('h:i A') }}
                        </p>
                    </div>
                    
                    @if($cashBox->creator)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">أنشئ بواسطة</label>
                        <p class="text-sm text-gray-800">
                            <i class="fas fa-user text-gray-400 mr-1"></i>
                            {{ $cashBox->creator->name }}
                        </p>
                    </div>
                    @endif
                    
                    @if($cashBox->updated_at != $cashBox->created_at)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">آخر تحديث</label>
                        <p class="text-sm text-gray-800">
                            <i class="fas fa-calendar-check text-gray-400 mr-1"></i>
                            {{ $cashBox->updated_at->format('Y-m-d') }}
                        </p>
                        <p class="text-xs text-gray-500 mr-5">
                            {{ $cashBox->updated_at->format('h:i A') }}
                        </p>
                    </div>
                    @endif
                    
                    @if($cashBox->updater && $cashBox->updated_at != $cashBox->created_at)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">حُدث بواسطة</label>
                        <p class="text-sm text-gray-800">
                            <i class="fas fa-user-edit text-gray-400 mr-1"></i>
                            {{ $cashBox->updater->name }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

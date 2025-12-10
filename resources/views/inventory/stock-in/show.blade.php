@extends('layouts.app')

@section('title', 'عرض أمر التوريد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 py-8 px-4 sm:px-6 lg:px-8" x-data="{ 
    showActions: false,
    animateIn: false
}" x-init="setTimeout(() => animateIn = true, 100)">
    
    <!-- Header Section with Glassmorphism -->
    <div class="max-w-7xl mx-auto mb-8" x-show="animateIn" x-transition:enter="transform transition duration-700" x-transition:enter-start="opacity-0 -translate-y-10" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 shadow-2xl">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -left-4 -top-4 h-72 w-72 animate-blob rounded-full bg-white mix-blend-multiply blur-xl filter"></div>
                <div class="animation-delay-2000 absolute -right-4 -bottom-4 h-72 w-72 animate-blob rounded-full bg-white mix-blend-multiply blur-xl filter"></div>
                <div class="animation-delay-4000 absolute left-20 top-20 h-72 w-72 animate-blob rounded-full bg-white mix-blend-multiply blur-xl filter"></div>
            </div>
            
            <div class="relative flex items-center justify-between">
                <div class="flex items-center space-x-reverse space-x-6">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white bg-opacity-20 backdrop-blur-lg">
                        <i class="fas fa-file-invoice text-4xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">تفاصيل أمر التوريد</h1>
                        <div class="flex items-center space-x-reverse space-x-4">
                            <span class="inline-flex items-center rounded-full bg-white bg-opacity-20 px-4 py-1 text-sm font-medium text-white backdrop-blur-lg">
                                <i class="fas fa-hashtag ml-2"></i>
                                {{ $stockIn->movement_number }}
                            </span>
                            <span class="inline-flex items-center rounded-full bg-white bg-opacity-20 px-4 py-1 text-sm font-medium text-white backdrop-blur-lg">
                                <i class="fas fa-calendar ml-2"></i>
                                {{ \Carbon\Carbon::parse($stockIn->movement_date)->format('Y-m-d') }}
                            </span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('inventory.stock-in.index') }}" class="group flex items-center space-x-reverse space-x-2 rounded-2xl bg-white px-6 py-3 text-indigo-600 shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl">
                    <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                    <span class="font-semibold">العودة</span>
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <!-- Main Content - 2 Columns -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Status Card with Animation -->
                <div x-show="animateIn" x-transition:enter="transform transition duration-700 delay-100" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="group relative overflow-hidden rounded-3xl bg-white p-8 shadow-xl transition-all duration-300 hover:shadow-2xl">
                    @if($stockIn->status === 'pending')
                        <div class="absolute right-0 top-0 h-full w-2 bg-gradient-to-b from-yellow-400 to-orange-500"></div>
                        <div class="flex items-center space-x-reverse space-x-6">
                            <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 shadow-lg">
                                <i class="fas fa-clock text-4xl text-white"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-1">قيد الانتظار</h3>
                                <p class="text-gray-600">في انتظار الاعتماد من المسؤول</p>
                                <div class="mt-4 flex items-center space-x-reverse space-x-2">
                                    <div class="h-2 w-2 animate-pulse rounded-full bg-yellow-500"></div>
                                    <span class="text-sm text-gray-500">حالة نشطة</span>
                                </div>
                            </div>
                        </div>
                    @elseif($stockIn->status === 'approved')
                        <div class="absolute right-0 top-0 h-full w-2 bg-gradient-to-b from-green-400 to-emerald-600"></div>
                        <div class="flex items-center space-x-reverse space-x-6">
                            <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 shadow-lg">
                                <i class="fas fa-check-circle text-4xl text-white"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-1">معتمد</h3>
                                <p class="text-gray-600">تم اعتماد الأمر بنجاح</p>
                                @if($stockIn->approver)
                                <div class="mt-4 flex items-center space-x-reverse space-x-4">
                                    <div class="flex items-center space-x-reverse space-x-2">
                                        <i class="fas fa-user-check text-green-600"></i>
                                        <span class="text-sm font-medium text-gray-700">{{ $stockIn->approver->name }}</span>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $stockIn->approved_at ? \Carbon\Carbon::parse($stockIn->approved_at)->format('Y-m-d H:i') : '' }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    @elseif($stockIn->status === 'rejected')
                        <div class="absolute right-0 top-0 h-full w-2 bg-gradient-to-b from-red-400 to-rose-600"></div>
                        <div class="flex items-center space-x-reverse space-x-6">
                            <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-red-400 to-rose-600 shadow-lg">
                                <i class="fas fa-times-circle text-4xl text-white"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-1">مرفوض</h3>
                                <p class="text-gray-600">تم رفض هذا الأمر</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Info Cards Grid -->
                <div x-show="animateIn" x-transition:enter="transform transition duration-700 delay-200" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    
                    <!-- Warehouse Card -->
                    <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-600 p-6 shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl">
                        <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white opacity-10"></div>
                        <div class="relative">
                            <div class="mb-4 inline-flex rounded-xl bg-white bg-opacity-20 p-3 backdrop-blur-sm">
                                <i class="fas fa-warehouse text-2xl text-white"></i>
                            </div>
                            <p class="text-sm font-medium text-blue-100">المخزن</p>
                            <h4 class="mt-2 text-2xl font-bold text-white">{{ $stockIn->warehouse->name ?? 'غير محدد' }}</h4>
                        </div>
                    </div>

                    <!-- Creator Card -->
                    <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 p-6 shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl">
                        <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white opacity-10"></div>
                        <div class="relative">
                            <div class="mb-4 inline-flex rounded-xl bg-white bg-opacity-20 p-3 backdrop-blur-sm">
                                <i class="fas fa-user text-2xl text-white"></i>
                            </div>
                            <p class="text-sm font-medium text-purple-100">المنشئ</p>
                            <h4 class="mt-2 text-2xl font-bold text-white">{{ $stockIn->creator->name ?? 'غير محدد' }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Notes Card -->
                @if($stockIn->notes)
                <div x-show="animateIn" x-transition:enter="transform transition duration-700 delay-300" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0" class="rounded-2xl bg-white p-6 shadow-lg">
                    <div class="flex items-start space-x-reverse space-x-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500">
                            <i class="fas fa-sticky-note text-xl text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-bold text-gray-900 mb-2">الملاحظات</h4>
                            <p class="text-gray-600 leading-relaxed">{{ $stockIn->notes }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Items Table -->
                <div x-show="animateIn" x-transition:enter="transform transition duration-700 delay-400" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0" class="overflow-hidden rounded-3xl bg-white shadow-xl">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-reverse space-x-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white bg-opacity-20 backdrop-blur-sm">
                                    <i class="fas fa-boxes text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-white">الأصناف</h3>
                                    <p class="text-indigo-100">{{ $stockIn->items->count() }} صنف</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">#</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">الصنف</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">الكمية</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">التكلفة</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">الإجمالي</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">رقم الدفعة</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">الانتهاء</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($stockIn->items as $index => $item)
                                <tr class="transition-colors hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-reverse space-x-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-400 to-indigo-600">
                                                <i class="fas fa-box text-white"></i>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $item->item->name ?? 'غير محدد' }}</div>
                                                <div class="text-sm text-gray-500">{{ $item->item->code ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ number_format($item->quantity, 3) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($item->unit_cost, 2) }} ر.س</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">
                                            {{ number_format($item->total_cost, 2) }} ر.س
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $item->batch_number ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d') : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                            <p class="text-lg font-medium text-gray-500">لا توجد أصناف</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-left text-lg font-bold text-gray-900">الإجمالي الكلي</td>
                                    <td colspan="3" class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-2 text-2xl font-bold text-white shadow-lg">
                                            <i class="fas fa-coins ml-2"></i>
                                            {{ number_format($stockIn->items->sum('total_cost'), 2) }} ر.س
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar - 1 Column -->
            <div class="space-y-6">
                
                <!-- Quick Actions -->
                <div x-show="animateIn" x-transition:enter="transform transition duration-700 delay-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="sticky top-8 space-y-4 rounded-3xl bg-white p-6 shadow-xl">
                    <h3 class="flex items-center space-x-reverse space-x-3 text-xl font-bold text-gray-900 mb-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600">
                            <i class="fas fa-bolt text-white"></i>
                        </div>
                        <span>الإجراءات السريعة</span>
                    </h3>
                    
                    @if($stockIn->status === 'pending')
                        <button onclick="approveOrder()" class="group w-full rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4 text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl">
                            <div class="flex items-center justify-center space-x-reverse space-x-3">
                                <i class="fas fa-check-circle text-2xl transition-transform group-hover:scale-110"></i>
                                <span class="text-lg font-semibold">اعتماد الأمر</span>
                            </div>
                        </button>
                        
                        <a href="{{ route('inventory.stock-in.edit', $stockIn->id) }}" class="group block w-full rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4 text-center text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl">
                            <div class="flex items-center justify-center space-x-reverse space-x-3">
                                <i class="fas fa-edit text-2xl transition-transform group-hover:scale-110"></i>
                                <span class="text-lg font-semibold">تعديل الأمر</span>
                            </div>
                        </a>
                        
                        <button onclick="deleteOrder()" class="group w-full rounded-xl bg-gradient-to-r from-red-500 to-rose-600 px-6 py-4 text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl">
                            <div class="flex items-center justify-center space-x-reverse space-x-3">
                                <i class="fas fa-trash text-2xl transition-transform group-hover:scale-110"></i>
                                <span class="text-lg font-semibold">حذف الأمر</span>
                            </div>
                        </button>
                    @else
                        <div class="rounded-xl bg-gray-100 p-6 text-center">
                            <i class="fas fa-lock text-4xl text-gray-400 mb-3"></i>
                            <p class="font-semibold text-gray-600">لا يمكن التعديل</p>
                            <p class="text-sm text-gray-500 mt-1">الأمر {{ $stockIn->status === 'approved' ? 'معتمد' : 'مرفوض' }}</p>
                        </div>
                    @endif
                    
                    <div class="border-t border-gray-200 pt-4">
                        <a href="{{ route('inventory.stock-in.index') }}" class="group flex w-full items-center justify-center space-x-reverse space-x-3 rounded-xl border-2 border-gray-300 px-6 py-4 text-gray-700 transition-all duration-300 hover:border-indigo-500 hover:bg-indigo-50">
                            <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                            <span class="font-semibold">العودة للقائمة</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes blob {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(20px, -50px) scale(1.1); }
    50% { transform: translate(-20px, 20px) scale(0.9); }
    75% { transform: translate(50px, 50px) scale(1.05); }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>

<script>
function approveOrder() {
    Swal.fire({
        title: 'اعتماد أمر التوريد',
        text: 'هل أنت متأكد من اعتماد هذا الأمر؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، اعتماد',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-3xl',
            confirmButton: 'rounded-xl px-6 py-3',
            cancelButton: 'rounded-xl px-6 py-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'تم الاعتماد!',
                text: 'تم اعتماد الأمر بنجاح',
                icon: 'success',
                confirmButtonColor: '#10b981',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-3'
                }
            });
        }
    });
}

function deleteOrder() {
    Swal.fire({
        title: 'حذف أمر التوريد',
        text: 'هل أنت متأكد من حذف هذا الأمر؟ لا يمكن التراجع عن هذا الإجراء!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، حذف',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-3xl',
            confirmButton: 'rounded-xl px-6 py-3',
            cancelButton: 'rounded-xl px-6 py-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'تم الحذف!',
                text: 'تم حذف الأمر بنجاح',
                icon: 'success',
                confirmButtonColor: '#10b981',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-3'
                }
            });
        }
    });
}
</script>
@endsection

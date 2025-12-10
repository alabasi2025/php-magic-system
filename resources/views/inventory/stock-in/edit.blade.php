@extends('layouts.app')

@section('title', 'تعديل أمر التوريد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-purple-50 to-pink-50 py-8 px-4 sm:px-6 lg:px-8" 
     x-data="stockInForm()" 
     x-init="init()"
     @keydown.escape="$refs.cancelButton.click()">
    
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-pink-500 via-rose-500 to-red-600 p-8 shadow-2xl">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -left-4 -top-4 h-72 w-72 animate-blob rounded-full bg-white mix-blend-multiply blur-xl filter"></div>
                <div class="animation-delay-2000 absolute -right-4 -bottom-4 h-72 w-72 animate-blob rounded-full bg-white mix-blend-multiply blur-xl filter"></div>
            </div>
            
            <div class="relative flex items-center justify-between">
                <div class="flex items-center space-x-reverse space-x-6">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white bg-opacity-20 backdrop-blur-lg">
                        <i class="fas fa-edit text-4xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">تعديل أمر التوريد</h1>
                        <p class="text-pink-100">رقم الأمر: {{ $stockIn->movement_number }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($stockIn->status !== 'pending')
    <div class="max-w-7xl mx-auto mb-6">
        <div class="rounded-2xl bg-red-50 border-r-4 border-red-500 p-6">
            <div class="flex items-center space-x-reverse space-x-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-500">
                    <i class="fas fa-exclamation-triangle text-2xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-900">لا يمكن التعديل</h3>
                    <p class="text-red-700">هذا الأمر {{ $stockIn->status === 'approved' ? 'معتمد' : 'مرفوض' }} ولا يمكن تعديله</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('inventory.stock-in.update', $stockIn->id) }}" method="POST" class="max-w-7xl mx-auto">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <!-- Main Form - 2 Columns -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Basic Info Card -->
                <div class="rounded-3xl bg-white p-8 shadow-xl">
                    <div class="mb-6 flex items-center space-x-reverse space-x-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600">
                            <i class="fas fa-info-circle text-xl text-white"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">المعلومات الأساسية</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Warehouse -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                <i class="fas fa-warehouse ml-2 text-blue-500"></i>
                                المخزن <span class="text-red-500">*</span>
                            </label>
                            <select name="warehouse_id" x-model="formData.warehouse_id" :disabled="isDisabled" required
                                    class="w-full rounded-xl border-2 border-gray-300 px-4 py-3 transition-all duration-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="">اختر المخزن</option>
                                @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ $stockIn->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                <i class="fas fa-calendar ml-2 text-purple-500"></i>
                                تاريخ التوريد <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="movement_date" x-model="formData.movement_date" :disabled="isDisabled" required
                                   value="{{ $stockIn->movement_date }}"
                                   class="w-full rounded-xl border-2 border-gray-300 px-4 py-3 transition-all duration-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 disabled:bg-gray-100 disabled:cursor-not-allowed">
                        </div>

                        <!-- Movement Number (Read-only) -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                <i class="fas fa-hashtag ml-2 text-green-500"></i>
                                رقم الأمر
                            </label>
                            <input type="text" value="{{ $stockIn->movement_number }}" readonly
                                   class="w-full rounded-xl border-2 border-gray-200 bg-gray-50 px-4 py-3 text-gray-600 cursor-not-allowed">
                        </div>

                        <!-- Notes -->
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                <i class="fas fa-sticky-note ml-2 text-amber-500"></i>
                                ملاحظات
                            </label>
                            <textarea name="notes" x-model="formData.notes" :disabled="isDisabled" rows="3"
                                      class="w-full rounded-xl border-2 border-gray-300 px-4 py-3 transition-all duration-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-100 disabled:bg-gray-100 disabled:cursor-not-allowed">{{ $stockIn->notes }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="rounded-3xl bg-white shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-reverse space-x-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white bg-opacity-20 backdrop-blur-sm">
                                    <i class="fas fa-boxes text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-white">الأصناف</h3>
                                    <p class="text-indigo-100" x-text="items.length + ' صنف'"></p>
                                </div>
                            </div>
                            <button type="button" @click="addItem()" :disabled="isDisabled"
                                    class="flex items-center space-x-reverse space-x-2 rounded-xl bg-white px-6 py-3 text-indigo-600 shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-plus"></i>
                                <span class="font-semibold">إضافة صنف</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">الصنف</th>
                                    <th class="px-4 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">الكمية</th>
                                    <th class="px-4 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">التكلفة</th>
                                    <th class="px-4 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">الإجمالي</th>
                                    <th class="px-4 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">رقم الدفعة</th>
                                    <th class="px-4 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-700">الانتهاء</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-700">إجراء</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="transition-colors hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <select :name="'items['+index+'][item_id]'" x-model="item.item_id" @change="updateTotal(index)" :disabled="isDisabled" required
                                                    class="w-full rounded-lg border-2 border-gray-300 px-3 py-2 text-sm transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-gray-100">
                                                <option value="">اختر الصنف</option>
                                                @foreach($items as $itemOption)
                                                <option value="{{ $itemOption->id }}">{{ $itemOption->name }} ({{ $itemOption->code }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" @input="updateTotal(index)" :disabled="isDisabled" step="0.001" min="0" required
                                                   class="w-24 rounded-lg border-2 border-gray-300 px-3 py-2 text-sm transition-all focus:border-green-500 focus:ring-2 focus:ring-green-100 disabled:bg-gray-100">
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="number" :name="'items['+index+'][unit_cost]'" x-model="item.unit_cost" @input="updateTotal(index)" :disabled="isDisabled" step="0.01" min="0" required
                                                   class="w-28 rounded-lg border-2 border-gray-300 px-3 py-2 text-sm transition-all focus:border-purple-500 focus:ring-2 focus:ring-purple-100 disabled:bg-gray-100">
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800" x-text="formatNumber(item.total) + ' ر.س'"></span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="text" :name="'items['+index+'][batch_number]'" x-model="item.batch_number" :disabled="isDisabled"
                                                   class="w-28 rounded-lg border-2 border-gray-300 px-3 py-2 text-sm transition-all focus:border-amber-500 focus:ring-2 focus:ring-amber-100 disabled:bg-gray-100">
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="date" :name="'items['+index+'][expiry_date]'" x-model="item.expiry_date" :disabled="isDisabled"
                                                   class="w-36 rounded-lg border-2 border-gray-300 px-3 py-2 text-sm transition-all focus:border-red-500 focus:ring-2 focus:ring-red-100 disabled:bg-gray-100">
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button type="button" @click="removeItem(index)" :disabled="isDisabled || items.length === 1"
                                                    class="rounded-lg bg-red-500 px-3 py-2 text-white transition-all duration-200 hover:bg-red-600 hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-left text-lg font-bold text-gray-900">الإجمالي الكلي</td>
                                    <td colspan="4" class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-2 text-2xl font-bold text-white shadow-lg">
                                            <i class="fas fa-coins ml-2"></i>
                                            <span x-text="formatNumber(grandTotal) + ' ر.س'"></span>
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
                <div class="sticky top-8 space-y-4 rounded-3xl bg-white p-6 shadow-xl">
                    <h3 class="flex items-center space-x-reverse space-x-3 text-xl font-bold text-gray-900 mb-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600">
                            <i class="fas fa-save text-white"></i>
                        </div>
                        <span>حفظ التعديلات</span>
                    </h3>

                    @if($stockIn->status === 'pending')
                        <button type="submit" class="group w-full rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4 text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl">
                            <div class="flex items-center justify-center space-x-reverse space-x-3">
                                <i class="fas fa-save text-2xl transition-transform group-hover:scale-110"></i>
                                <span class="text-lg font-semibold">حفظ التعديلات</span>
                            </div>
                        </button>
                    @endif

                    <a href="{{ route('inventory.stock-in.show', $stockIn->id) }}" x-ref="cancelButton"
                       class="group flex w-full items-center justify-center space-x-reverse space-x-3 rounded-xl border-2 border-gray-300 px-6 py-4 text-gray-700 transition-all duration-300 hover:border-red-500 hover:bg-red-50">
                        <i class="fas fa-times transition-transform group-hover:rotate-90"></i>
                        <span class="font-semibold">إلغاء</span>
                    </a>
                </div>
            </div>
        </div>
    </form>
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
</style>

<script>
function stockInForm() {
    return {
        formData: {
            warehouse_id: '{{ $stockIn->warehouse_id }}',
            movement_date: '{{ $stockIn->movement_date }}',
            notes: '{{ $stockIn->notes }}'
        },
        items: [],
        isDisabled: {{ $stockIn->status !== 'pending' ? 'true' : 'false' }},
        
        init() {
            // Load existing items
            @foreach($stockIn->items as $item)
            this.items.push({
                item_id: '{{ $item->item_id }}',
                quantity: {{ $item->quantity }},
                unit_cost: {{ $item->unit_cost }},
                total: {{ $item->total_cost }},
                batch_number: '{{ $item->batch_number }}',
                expiry_date: '{{ $item->expiry_date }}'
            });
            @endforeach
            
            // If no items, add one empty row
            if (this.items.length === 0) {
                this.addItem();
            }
        },
        
        addItem() {
            this.items.push({
                item_id: '',
                quantity: 0,
                unit_cost: 0,
                total: 0,
                batch_number: '',
                expiry_date: ''
            });
        },
        
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        
        updateTotal(index) {
            const item = this.items[index];
            item.total = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_cost) || 0);
        },
        
        get grandTotal() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
        },
        
        formatNumber(num) {
            return parseFloat(num || 0).toFixed(2);
        }
    }
}
</script>
@endsection

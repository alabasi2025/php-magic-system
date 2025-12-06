@extends('layouts.app')

@section('title', 'تعديل القالب')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-edit text-white text-xl"></i>
                        </div>
                        تعديل القالب
                    </h1>
                    <p class="text-gray-600 mt-2 mr-16">تعديل بيانات القالب: {{ $journalTemplate->name }}</p>
                </div>
                <a href="{{ route('journal-templates.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع</span>
                </a>
            </div>
        </div>

        <form action="{{ route('journal-templates.update', $journalTemplate) }}" method="POST" id="templateForm">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    المعلومات الأساسية
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Template Name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            اسم القالب <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name', $journalTemplate->name) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"
                               placeholder="مثال: قيد مبيعات يومية">
                        @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            الفئة
                        </label>
                        <select name="category" id="category"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                            <option value="">اختر الفئة</option>
                            <option value="مبيعات" {{ old('category', $journalTemplate->category) == 'مبيعات' ? 'selected' : '' }}>مبيعات</option>
                            <option value="مشتريات" {{ old('category', $journalTemplate->category) == 'مشتريات' ? 'selected' : '' }}>مشتريات</option>
                            <option value="رواتب" {{ old('category', $journalTemplate->category) == 'رواتب' ? 'selected' : '' }}>رواتب</option>
                            <option value="مصروفات" {{ old('category', $journalTemplate->category) == 'مصروفات' ? 'selected' : '' }}>مصروفات</option>
                            <option value="إيرادات" {{ old('category', $journalTemplate->category) == 'إيرادات' ? 'selected' : '' }}>إيرادات</option>
                            <option value="عام" {{ old('category', $journalTemplate->category) == 'عام' ? 'selected' : '' }}>عام</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            الحالة
                        </label>
                        <div class="flex items-center gap-4 h-12">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" 
                                       {{ old('is_active', $journalTemplate->is_active) ? 'checked' : '' }}
                                       class="w-5 h-5 text-green-500 border-2 border-gray-300 rounded focus:ring-2 focus:ring-green-200">
                                <span class="text-gray-700 font-medium">قالب نشط</span>
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            الوصف
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"
                                  placeholder="وصف مختصر للقالب وكيفية استخدامه...">{{ old('description', $journalTemplate->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Template Entries -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-list text-purple-500"></i>
                        سطور القيد
                    </h2>
                    <button type="button" onclick="addEntry()" 
                            class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-2 rounded-xl font-semibold transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>إضافة سطر</span>
                    </button>
                </div>

                <div id="entriesContainer" class="space-y-4">
                    <!-- Entry rows will be added here dynamically -->
                </div>

                <!-- Summary -->
                <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 font-semibold mb-1">إجمالي المدين</p>
                            <p id="totalDebit" class="text-2xl font-bold text-green-600">0.00</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600 font-semibold mb-1">إجمالي الدائن</p>
                            <p id="totalCredit" class="text-2xl font-bold text-red-600">0.00</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600 font-semibold mb-1">الفرق</p>
                            <p id="difference" class="text-2xl font-bold text-blue-600">0.00</p>
                        </div>
                    </div>
                    <div id="balanceAlert" class="hidden mt-4 p-3 bg-red-100 border border-red-300 rounded-lg text-center">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        <span class="text-red-700 font-semibold mr-2">القيد غير متوازن! يجب أن يكون المدين = الدائن</span>
                    </div>
                    <div id="balanceSuccess" class="hidden mt-4 p-3 bg-green-100 border border-green-300 rounded-lg text-center">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-green-700 font-semibold mr-2">القيد متوازن ✓</span>
                    </div>
                </div>
            </div>

            <!-- Hidden field for template_data -->
            <input type="hidden" name="template_data" id="template_data">

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex gap-4 justify-end">
                    <a href="{{ route('journal-templates.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-8 py-3 rounded-xl font-semibold transition-all duration-300">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>حفظ التعديلات</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let entryCounter = 0;
const accounts = @json($accounts);
const existingTemplate = @json($journalTemplate->template_data);

// Load existing entries
document.addEventListener('DOMContentLoaded', function() {
    if (existingTemplate && existingTemplate.entries) {
        existingTemplate.entries.forEach(entry => {
            addEntry(entry);
        });
    } else {
        addEntry();
        addEntry();
    }
    calculateTotals();
});

function addEntry(data = null) {
    entryCounter++;
    const container = document.getElementById('entriesContainer');
    
    const entryHtml = `
        <div class="entry-row bg-gray-50 rounded-xl p-4 border-2 border-gray-200" data-entry="${entryCounter}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-1 text-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold mx-auto">
                        ${entryCounter}
                    </div>
                </div>
                
                <div class="md:col-span-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">الحساب</label>
                    <select class="account-select w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300" required>
                        <option value="">اختر الحساب</option>
                        ${accounts.map(acc => `<option value="${acc.id}" ${data && data.account_id == acc.id ? 'selected' : ''}>${acc.code} - ${acc.name}</option>`).join('')}
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">مدين</label>
                    <input type="number" step="0.01" value="${data ? data.debit : ''}" class="debit-input w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300" placeholder="0.00" onchange="calculateTotals()">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">دائن</label>
                    <input type="number" step="0.01" value="${data ? data.credit : ''}" class="credit-input w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300" placeholder="0.00" onchange="calculateTotals()">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">البيان</label>
                    <input type="text" value="${data ? data.description || '' : ''}" class="description-input w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300" placeholder="بيان...">
                </div>
                
                <div class="md:col-span-1 flex justify-center">
                    <button type="button" onclick="removeEntry(${entryCounter})" 
                            class="bg-red-500 hover:bg-red-600 text-white w-10 h-10 rounded-xl transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', entryHtml);
}

function removeEntry(id) {
    const entry = document.querySelector(`[data-entry="${id}"]`);
    if (entry) {
        entry.remove();
        calculateTotals();
        updateEntryNumbers();
    }
}

function updateEntryNumbers() {
    const entries = document.querySelectorAll('.entry-row');
    entries.forEach((entry, index) => {
        const numberDiv = entry.querySelector('.w-10.h-10');
        if (numberDiv) {
            numberDiv.textContent = index + 1;
        }
    });
}

function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;
    
    document.querySelectorAll('.debit-input').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });
    
    document.querySelectorAll('.credit-input').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });
    
    const difference = Math.abs(totalDebit - totalCredit);
    
    document.getElementById('totalDebit').textContent = totalDebit.toFixed(2);
    document.getElementById('totalCredit').textContent = totalCredit.toFixed(2);
    document.getElementById('difference').textContent = difference.toFixed(2);
    
    const balanceAlert = document.getElementById('balanceAlert');
    const balanceSuccess = document.getElementById('balanceSuccess');
    
    if (difference > 0.01 && (totalDebit > 0 || totalCredit > 0)) {
        balanceAlert.classList.remove('hidden');
        balanceSuccess.classList.add('hidden');
    } else if (totalDebit > 0 && totalCredit > 0) {
        balanceAlert.classList.add('hidden');
        balanceSuccess.classList.remove('hidden');
    } else {
        balanceAlert.classList.add('hidden');
        balanceSuccess.classList.add('hidden');
    }
}

// Form submission
document.getElementById('templateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const entries = [];
    document.querySelectorAll('.entry-row').forEach(row => {
        const accountId = row.querySelector('.account-select').value;
        const debit = parseFloat(row.querySelector('.debit-input').value) || 0;
        const credit = parseFloat(row.querySelector('.credit-input').value) || 0;
        const description = row.querySelector('.description-input').value;
        
        if (accountId && (debit > 0 || credit > 0)) {
            entries.push({
                account_id: accountId,
                debit: debit,
                credit: credit,
                description: description
            });
        }
    });
    
    if (entries.length === 0) {
        alert('يجب إضافة سطر واحد على الأقل');
        return;
    }
    
    const templateData = {
        entries: entries
    };
    
    document.getElementById('template_data').value = JSON.stringify(templateData);
    this.submit();
});
</script>
@endpush

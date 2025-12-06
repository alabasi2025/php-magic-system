@extends('layouts.app')

@section('title', 'إضافة قيد جديد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-plus text-white text-xl"></i>
                        </div>
                        إنشاء قيد محاسبي جديد
                    </h1>
                    <p class="text-gray-600 mt-2 mr-16">قم بإنشاء قيد محاسبي متوازن</p>
                </div>
                <a href="{{ route('journal-entries.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع</span>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('journal-entries.store') }}" id="journalEntryForm">
            @csrf
            
            <!-- Basic Information -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    المعلومات الأساسية
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            رقم القيد
                            <span class="text-gray-400 text-xs">(تلقائي)</span>
                        </label>
                        <input type="text" 
                               value="سيتم إنشاؤه تلقائياً" 
                               disabled
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-500">
                    </div>
                    
                    <div>
                        <label for="entry_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            تاريخ القيد *
                        </label>
                        <input type="date" 
                               id="entry_date" 
                               name="entry_date" 
                               value="{{ date('Y-m-d') }}"
                               required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                    </div>
                    
                    <div>
                        <label for="reference" class="block text-sm font-semibold text-gray-700 mb-2">
                            المرجع
                        </label>
                        <input type="text" 
                               id="reference" 
                               name="reference" 
                               placeholder="رقم المرجع أو الفاتورة"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        الوصف *
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3" 
                              required
                              placeholder="وصف مختصر للقيد المحاسبي..."
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"></textarea>
                </div>
                
                <div class="mt-4">
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                        ملاحظات
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="2" 
                              placeholder="ملاحظات إضافية (اختياري)..."
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"></textarea>
                </div>
            </div>

            <!-- Entry Lines -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-list text-purple-500"></i>
                        سطور القيد
                    </h2>
                    <button type="button" 
                            id="addRowBtn"
                            class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-4 py-2 rounded-xl font-semibold transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        إضافة سطر
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <div id="entriesContainer" class="space-y-4">
                        <!-- Entry Line Template -->
                        <div class="entry-line bg-gray-50 rounded-xl p-4 border-2 border-gray-200">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-500 text-white rounded-lg flex items-center justify-center font-bold">
                                    1
                                </div>
                                
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-4">
                                    <div class="md:col-span-5">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">الحساب</label>
                                        <select name="accounts[]" 
                                                class="account-select w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300" 
                                                required>
                                            <option value="">اختر الحساب</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">مدين</label>
                                        <input type="number" 
                                               name="debits[]" 
                                               class="debit-input w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-300" 
                                               min="0" 
                                               step="0.01" 
                                               value="0"
                                               placeholder="0.00">
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">دائن</label>
                                        <input type="number" 
                                               name="credits[]" 
                                               class="credit-input w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all duration-300" 
                                               min="0" 
                                               step="0.01" 
                                               value="0"
                                               placeholder="0.00">
                                    </div>
                                    
                                    <div class="md:col-span-3">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">البيان</label>
                                        <input type="text" 
                                               name="line_descriptions[]" 
                                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300" 
                                               placeholder="بيان...">
                                    </div>
                                </div>
                                
                                <button type="button" 
                                        class="remove-line flex-shrink-0 w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-lg flex items-center justify-center transition-all duration-300"
                                        title="حذف السطر">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totals -->
                <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <p class="text-sm font-semibold text-gray-600 mb-2">إجمالي المدين</p>
                            <p id="totalDebit" class="text-3xl font-bold text-green-600">0.00</p>
                        </div>
                        
                        <div class="text-center">
                            <p class="text-sm font-semibold text-gray-600 mb-2">إجمالي الدائن</p>
                            <p id="totalCredit" class="text-3xl font-bold text-red-600">0.00</p>
                        </div>
                        
                        <div class="text-center">
                            <p class="text-sm font-semibold text-gray-600 mb-2">الفرق</p>
                            <p id="difference" class="text-3xl font-bold text-gray-600">0.00</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <div id="balanceStatus" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-lg bg-red-100 text-red-700">
                            <i class="fas fa-times-circle"></i>
                            <span>القيد غير متوازن</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('journal-entries.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-8 py-3 rounded-xl font-semibold transition-all duration-300">
                        إلغاء
                    </a>
                    
                    <button type="submit" 
                            id="submitBtn"
                            disabled
                            class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-save ml-2"></i>
                        حفظ القيد
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('entriesContainer');
    const addBtn = document.getElementById('addRowBtn');
    let lineCount = 1;

    // Add new line
    addBtn.addEventListener('click', function() {
        lineCount++;
        const template = container.querySelector('.entry-line').cloneNode(true);
        
        // Update line number
        template.querySelector('.flex-shrink-0').textContent = lineCount;
        
        // Reset values
        template.querySelectorAll('select, input').forEach(input => {
            if (input.type === 'number') {
                input.value = '0';
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            } else {
                input.value = '';
            }
        });
        
        container.appendChild(template);
        updateLineNumbers();
        updateTotals();
    });

    // Remove line
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-line')) {
            const lines = container.querySelectorAll('.entry-line');
            if (lines.length > 2) {
                e.target.closest('.entry-line').remove();
                updateLineNumbers();
                updateTotals();
            } else {
                alert('يجب أن يحتوي القيد على سطرين على الأقل');
            }
        }
    });

    // Update totals on input
    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('debit-input') || e.target.classList.contains('credit-input')) {
            updateTotals();
        }
    });

    function updateLineNumbers() {
        container.querySelectorAll('.entry-line').forEach((line, index) => {
            line.querySelector('.flex-shrink-0').textContent = index + 1;
        });
    }

    function updateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        container.querySelectorAll('.entry-line').forEach(line => {
            const debit = parseFloat(line.querySelector('.debit-input').value) || 0;
            const credit = parseFloat(line.querySelector('.credit-input').value) || 0;
            totalDebit += debit;
            totalCredit += credit;
        });

        const difference = Math.abs(totalDebit - totalCredit);

        document.getElementById('totalDebit').textContent = totalDebit.toFixed(2);
        document.getElementById('totalCredit').textContent = totalCredit.toFixed(2);
        document.getElementById('difference').textContent = difference.toFixed(2);

        const balanceStatus = document.getElementById('balanceStatus');
        const submitBtn = document.getElementById('submitBtn');

        if (totalDebit > 0 && totalCredit > 0 && difference < 0.01) {
            balanceStatus.className = 'inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-lg bg-green-100 text-green-700';
            balanceStatus.innerHTML = '<i class="fas fa-check-circle"></i><span>القيد متوازن ✓</span>';
            submitBtn.disabled = false;
        } else {
            balanceStatus.className = 'inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-lg bg-red-100 text-red-700';
            balanceStatus.innerHTML = '<i class="fas fa-times-circle"></i><span>القيد غير متوازن</span>';
            submitBtn.disabled = true;
        }
    }

    // Add second line by default
    addBtn.click();
    
    // Initial update
    updateTotals();
});
</script>
@endpush

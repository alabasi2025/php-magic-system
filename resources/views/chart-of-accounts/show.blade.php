{{-- Updated: Added Account Groups feature - Force cache refresh --}}
@extends('layouts.app')

@section('title', $chartGroup->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('chart-of-accounts.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-2 inline-block">
                <i class="fas fa-arrow-right ml-1"></i>
                العودة للأدلة المحاسبية
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas {{ $chartGroup->icon ?? 'fa-book' }}" style="color: {{ $chartGroup->color ?? '#3B82F6' }}"></i>
                {{ $chartGroup->name }}
            </h1>
            <p class="text-gray-600">{{ $chartGroup->description }}</p>
            <div class="flex items-center gap-4 mt-2">
                <span class="text-sm text-gray-500">
                    <i class="fas fa-sitemap ml-1"></i>
                    الوحدة: {{ $chartGroup->unit->name }}
                </span>
                <span class="text-sm text-gray-500">
                    <i class="fas fa-list ml-1"></i>
                    عدد الحسابات: {{ $chartGroup->accounts_count ?? 0 }}
                </span>
            </div>
        </div>
        <button onclick="openAddAccountModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors">
            <i class="fas fa-plus ml-2"></i>
            إضافة حساب
        </button>
    </div>

    <!-- Toolbar -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[300px]">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="البحث في الحسابات..." 
                           class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <button onclick="expandAll()" class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg transition-colors">
                    <i class="fas fa-plus-square ml-1"></i>
                    توسيع الكل
                </button>
                <button onclick="collapseAll()" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors">
                    <i class="fas fa-minus-square ml-1"></i>
                    طي الكل
                </button>
                <button onclick="printTree()" class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors">
                    <i class="fas fa-print ml-1"></i>
                    طباعة
                </button>
            </div>
        </div>
    </div>

    <!-- Accounts Tree -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        @if($chartGroup->rootAccounts && $chartGroup->rootAccounts->count() > 0)
            <div id="accountsTree" class="space-y-2">
                @foreach($chartGroup->rootAccounts as $account)
                    @include('chart-of-accounts.partials.account-node', ['account' => $account, 'level' => 0])
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg mb-4">لا توجد حسابات في هذا الدليل</p>
                <button onclick="openAddAccountModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة أول حساب
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Add Account Modal -->
<div id="addAccountModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-plus-circle text-indigo-600 ml-2"></i>
                    إضافة حساب جديد
                </h2>
                <button onclick="closeAddAccountModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <form id="addAccountForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="chart_group_id" value="{{ $chartGroup->id }}">

            <!-- الحساب الأب -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-sitemap text-indigo-600 ml-1"></i>
                    الحساب الأب (اختياري)
                </label>
                <select name="parent_id" id="parent_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- حساب رئيسي --</option>
                    @foreach($chartGroup->accounts->where('is_parent', 1) as $acc)
                        <option value="{{ $acc->id }}">{{ str_repeat('— ', $acc->level) }} {{ $acc->name }} ({{ $acc->code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- الكود -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-barcode text-indigo-600 ml-1"></i>
                    كود الحساب <span class="text-red-500">*</span>
                </label>
                <input type="text" name="code" required 
                       placeholder="مثال: 001-001"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- الاسم -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tag text-indigo-600 ml-1"></i>
                    اسم الحساب <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required 
                       placeholder="مثال: الرواتب والأجور"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- نوع الحساب -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-layer-group text-indigo-600 ml-1"></i>
                    نوع الحساب <span class="text-red-500">*</span>
                </label>
                <select name="is_parent" id="is_parent" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" onchange="toggleAccountTypeFields()">
                    <option value="">-- اختر النوع --</option>
                    <option value="1">حساب رئيسي (للترتيب الشجري فقط)</option>
                    <option value="0">حساب فرعي (الحساب النهائي)</option>
                </select>
                <p class="text-gray-500 text-sm mt-1">الحساب الرئيسي: للترتيب الشجري فقط | الحساب الفرعي: يستخدم في القيود المحاسبية</p>
            </div>

            <!-- نوع الحساب الفرعي (يظهر فقط للحسابات الفرعية) -->
            <div id="accountTypeField" style="display: none;">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tag text-indigo-600 ml-1"></i>
                    نوع الحساب الفرعي <span class="text-red-500">*</span>
                </label>
                <select name="account_type" id="account_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- اختر النوع --</option>
                    <option value="general">حساب عام</option>
                    <option value="cash_box">صندوق</option>
                    <option value="bank">بنك</option>
                    <option value="wallet">محفظة إلكترونية</option>
                    <option value="atm">صراف آلي</option>
                    <option value="intermediate">حساب وسيط</option>
                </select>
            </div>

            <!-- مجموعة الحسابات (يظهر فقط للحسابات الفرعية) -->
            <div id="accountGroupField" style="display: none;">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-layer-group text-indigo-600 ml-1"></i>
                    مجموعة الحسابات (اختياري)
                </label>
                <select name="account_group_id" id="account_group_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- بدون مجموعة --</option>
                    @foreach(\App\Models\AccountGroup::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
                <p class="text-gray-500 text-sm mt-1">تستخدم لتصنيف الحسابات وفلترتها في التقارير</p>
            </div>

            <!-- حساب وسيط لأي فئة (يظهر فقط عند اختيار حساب وسيط) -->
            <div id="intermediateForField" style="display: none;">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-link text-indigo-600 ml-1"></i>
                    حساب وسيط لأي فئة؟ <span class="text-red-500">*</span>
                </label>
                <select name="intermediate_for" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- اختر الفئة --</option>
                    <option value="cash_boxes">الصناديق</option>
                    <option value="banks">البنوك</option>
                    <option value="wallets">المحافظ الإلكترونية</option>
                    <option value="atms">الصرافات الآلية</option>
                </select>
                <p class="text-gray-500 text-sm mt-1">سيظهر هذا الحساب كخيار عند إنشاء عنصر من الفئة المحددة</p>
            </div>

            <!-- الوصف -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-right text-indigo-600 ml-1"></i>
                    الوصف (اختياري)
                </label>
                <textarea name="description" rows="2"
                          placeholder="وصف مختصر للحساب..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <!-- الحالة -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" checked
                       class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label class="mr-3 text-sm font-medium text-gray-700">
                    تفعيل الحساب
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t">
                <button type="button" onclick="closeAddAccountModal()" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </button>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-save ml-2"></i>
                    حفظ الحساب
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Open modal
function openAddAccountModal() {
    document.getElementById('addAccountModal').classList.remove('hidden');
    document.getElementById('addAccountModal').classList.add('flex');
}

// Close modal
function closeAddAccountModal() {
    document.getElementById('addAccountModal').classList.add('hidden');
    document.getElementById('addAccountModal').classList.remove('flex');
    document.getElementById('addAccountForm').reset();
}

// Toggle account type fields based on is_parent selection
function toggleAccountTypeFields() {
    const isParent = document.getElementById('is_parent').value;
    const accountTypeField = document.getElementById('accountTypeField');
    const accountGroupField = document.getElementById('accountGroupField');
    const intermediateForField = document.getElementById('intermediateForField');
    
    if (isParent == 0) {
        // حساب فرعي - إظهار حقل نوع الحساب ومجموعة الحسابات
        accountTypeField.style.display = 'block';
        accountGroupField.style.display = 'block';
    } else {
        // حساب رئيسي - إخفاء جميع الحقول الإضافية
        accountTypeField.style.display = 'none';
        accountGroupField.style.display = 'none';
        intermediateForField.style.display = 'none';
        document.getElementById('account_type').value = '';
        document.getElementById('account_group_id').value = '';
    }
}

// Toggle intermediate for field based on account_type selection
function toggleIntermediateField() {
    const accountType = document.getElementById('account_type').value;
    const intermediateForField = document.getElementById('intermediateForField');
    
    if (accountType === 'intermediate') {
        // حساب وسيط - إظهار حقل الفئة
        intermediateForField.style.display = 'block';
    } else {
        // نوع آخر - إخفاء حقل الفئة
        intermediateForField.style.display = 'none';
    }
}

// Add event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Event listener for is_parent select
    const isParentSelect = document.getElementById('is_parent');
    if (isParentSelect) {
        isParentSelect.addEventListener('change', toggleAccountTypeFields);
    }
    
    // Event listener for account_type select
    const accountTypeSelect = document.getElementById('account_type');
    if (accountTypeSelect) {
        accountTypeSelect.addEventListener('change', toggleIntermediateField);
    }
});

// Submit form
document.getElementById('addAccountForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route("chart-of-accounts.add-account") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert('حدث خطأ: ' + result.message);
        }
    } catch (error) {
        alert('حدث خطأ في الاتصال بالخادم');
        console.error(error);
    }
});

// Toggle account node
function toggleAccount(accountId) {
    const children = document.getElementById('children-' + accountId);
    const icon = document.getElementById('icon-' + accountId);
    
    if (children.classList.contains('hidden')) {
        children.classList.remove('hidden');
        icon.classList.remove('fa-plus-square');
        icon.classList.add('fa-minus-square');
    } else {
        children.classList.add('hidden');
        icon.classList.add('fa-plus-square');
        icon.classList.remove('fa-minus-square');
    }
}

// Expand all
function expandAll() {
    document.querySelectorAll('[id^="children-"]').forEach(el => {
        el.classList.remove('hidden');
    });
    document.querySelectorAll('[id^="icon-"]').forEach(el => {
        el.classList.remove('fa-plus-square');
        el.classList.add('fa-minus-square');
    });
}

// Collapse all
function collapseAll() {
    document.querySelectorAll('[id^="children-"]').forEach(el => {
        el.classList.add('hidden');
    });
    document.querySelectorAll('[id^="icon-"]').forEach(el => {
        el.classList.add('fa-plus-square');
        el.classList.remove('fa-minus-square');
    });
}

// Search
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const accounts = document.querySelectorAll('[data-account-name]');
    
    accounts.forEach(account => {
        const name = account.dataset.accountName.toLowerCase();
        const code = account.dataset.accountCode.toLowerCase();
        
        if (name.includes(searchTerm) || code.includes(searchTerm)) {
            account.classList.remove('hidden');
            // Show parent nodes
            let parent = account.closest('[id^="children-"]');
            while (parent) {
                parent.classList.remove('hidden');
                parent = parent.parentElement.closest('[id^="children-"]');
            }
        } else if (searchTerm !== '') {
            account.classList.add('hidden');
        }
    });
    
    if (searchTerm === '') {
        collapseAll();
    }
});

// Print
function printTree() {
    window.print();
}

// Edit account
function editAccount(accountId) {
    // This is a placeholder - implement edit modal similar to add modal
    alert('وظيفة التعديل قيد التطوير');
}

// Delete account
async function deleteAccount(accountId) {
    if (!confirm('هل أنت متأكد من حذف هذا الحساب؟')) {
        return;
    }
    
    try {
        const response = await fetch(`/chart-of-accounts/delete-account/${accountId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert('حدث خطأ: ' + result.message);
        }  
    } catch (error) {
        alert('حدث خطأ في الاتصال بالخادم');
        console.error(error);
    }
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endpush
@endsection

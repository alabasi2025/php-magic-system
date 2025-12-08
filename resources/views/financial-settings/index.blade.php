@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Enhanced Header with Gradient -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-3">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-300">
                <i class="fas fa-cog text-white text-3xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    إعدادات النظام المالي
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1 text-lg">إدارة وتخصيص إعدادات الدليل المحاسبي ومجموعات الحسابات</p>
            </div>
        </div>
    </div>

    <!-- Enhanced Tabs Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <!-- Modern Tab Headers -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
            <nav class="flex" aria-label="Tabs">
                <button onclick="switchTab('account-types')" id="tab-account-types" 
                        class="tab-button active flex-1 py-5 px-8 text-base font-semibold relative overflow-hidden group transition-all duration-300">
                    <div class="flex items-center justify-center gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-tags text-white"></i>
                        </div>
                        <span class="text-indigo-700 dark:text-indigo-400">إعدادات الدليل المحاسبي</span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-600 transform scale-x-100 transition-transform duration-300"></div>
                </button>
                <button onclick="switchTab('account-groups')" id="tab-account-groups"
                        class="tab-button flex-1 py-5 px-8 text-base font-semibold relative overflow-hidden group transition-all duration-300">
                    <div class="flex items-center justify-center gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-layer-group text-white"></i>
                        </div>
                        <span class="text-gray-600 dark:text-gray-400 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">مجموعات الحسابات</span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-emerald-600 transform scale-x-0 transition-transform duration-300"></div>
                </button>
                <button onclick="switchTab('chart-groups')" id="tab-chart-groups"
                        class="tab-button flex-1 py-5 px-8 text-base font-semibold relative overflow-hidden group transition-all duration-300">
                    <div class="flex items-center justify-center gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-sitemap text-white"></i>
                        </div>
                        <span class="text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">الأدلة المحاسبية</span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-cyan-600 transform scale-x-0 transition-transform duration-300"></div>
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-8">
            <!-- Account Types Tab -->
            <div id="content-account-types" class="tab-content">
                <!-- Enhanced Header with Action Button -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                            <div class="w-2 h-8 bg-gradient-to-b from-indigo-500 to-purple-600 rounded-full"></div>
                            أنواع الحسابات الفرعية
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 mt-2 mr-5">إدارة وتخصيص أنواع الحسابات المستخدمة في النظام</p>
                    </div>
                    <button onclick="openAddAccountTypeModal()" 
                            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl transition-all duration-300 flex items-center gap-3 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-plus-circle text-xl"></i>
                        <span class="font-semibold">إضافة نوع جديد</span>
                    </button>
                </div>

                <!-- Enhanced Account Types Table -->
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                                <tr>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الأيقونة</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">المفتاح</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الاسم بالعربي</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الاسم بالإنجليزي</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الحالة</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">نوع النظام</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الترتيب</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($accountTypes as $type)
                                <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-gray-700 dark:hover:to-gray-600 transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                                            <i class="{{ $type->icon ?? 'fas fa-file' }} text-2xl text-white"></i>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="px-3 py-1.5 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-lg text-sm font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $type->key }}</code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-gray-900 dark:text-white font-semibold text-base">{{ $type->name_ar }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-gray-600 dark:text-gray-400">{{ $type->name_en ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($type->is_active)
                                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-green-400 to-emerald-500 text-white shadow-md flex items-center gap-2 w-fit">
                                                <i class="fas fa-check-circle"></i>
                                                مفعل
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-red-400 to-rose-500 text-white shadow-md flex items-center gap-2 w-fit">
                                                <i class="fas fa-times-circle"></i>
                                                معطل
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($type->is_system)
                                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-blue-400 to-cyan-500 text-white shadow-md flex items-center gap-2 w-fit">
                                                <i class="fas fa-shield-alt"></i>
                                                نظام
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-purple-400 to-pink-500 text-white shadow-md flex items-center gap-2 w-fit">
                                                <i class="fas fa-user-cog"></i>
                                                مخصص
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-gray-800 dark:text-gray-200 font-bold text-lg">{{ $type->sort_order }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <button onclick="openEditAccountTypeModal({{ $type->id }})" title="تعديل" class="p-2 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800 transition-all duration-200 transform hover:scale-110">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            @if(!$type->is_system)
                                            <button onclick="deleteAccountType({{ $type->id }})" title="حذف" class="p-2 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800 transition-all duration-200 transform hover:scale-110">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-folder-open text-6xl mb-4"></i>
                                            <h3 class="text-2xl font-bold mb-2">لا توجد بيانات</h3>
                                            <p>لم يتم إضافة أي أنواع حسابات بعد.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Account Groups Tab -->
            <div id="content-account-groups" class="tab-content hidden">
                <!-- Enhanced Header with Action Button -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                            <div class="w-2 h-8 bg-gradient-to-b from-green-500 to-emerald-600 rounded-full"></div>
                            مجموعات الحسابات
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 mt-2 mr-5">تصنيف الحسابات الفرعية لتسهيل الفلترة في التقارير</p>
                    </div>
                    <button onclick="openAccountGroupModal()" 
                            class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl transition-all duration-300 flex items-center gap-3 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-plus-circle text-xl"></i>
                        <span class="font-semibold">إضافة مجموعة جديدة</span>
                    </button>
                </div>

                <!-- Enhanced Account Groups Table -->
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                                <tr>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الكود</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">اسم المجموعة</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الوصف</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">عدد الحسابات</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الحالة</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الترتيب</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="account-groups-tbody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Rows will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Chart of Accounts Groups Tab -->
            <div id="content-chart-groups" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">الأدلة المحاسبية</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-2">إدارة الأدلة المحاسبية وربطها بمجموعات الحسابات.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Tab Switching Logic
function switchTab(tabId) {
    // Hide all tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Deactivate all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
        button.querySelector('div:last-child').classList.remove('scale-x-100');
        button.querySelector('div:last-child').classList.add('scale-x-0');
        const iconContainer = button.querySelector('.flex > div:first-child');
        const textSpan = button.querySelector('span');
        
        if (button.id === 'tab-account-types') {
            textSpan.classList.remove('text-indigo-700', 'dark:text-indigo-400');
        } else if (button.id === 'tab-account-groups') {
            textSpan.classList.remove('text-green-700', 'dark:text-green-400');
        } else if (button.id === 'tab-chart-groups') {
            textSpan.classList.remove('text-blue-700', 'dark:text-blue-400');
        }
        textSpan.classList.add('text-gray-600', 'dark:text-gray-400');
    });

    // Show the selected tab content
    document.getElementById('content-' + tabId).classList.remove('hidden');

    // Activate the selected tab button
    const activeButton = document.getElementById('tab-' + tabId);
    activeButton.classList.add('active');
    activeButton.querySelector('div:last-child').classList.remove('scale-x-0');
    activeButton.querySelector('div:last-child').classList.add('scale-x-100');
    const activeTextSpan = activeButton.querySelector('span');
    activeTextSpan.classList.remove('text-gray-600', 'dark:text-gray-400');
    if (tabId === 'account-types') {
        activeTextSpan.classList.add('text-indigo-700', 'dark:text-indigo-400');
    } else if (tabId === 'account-groups') {
        activeTextSpan.classList.add('text-green-700', 'dark:text-green-400');
    } else if (tabId === 'chart-groups') {
        activeTextSpan.classList.add('text-blue-700', 'dark:text-blue-400');
    }

    // Store active tab in session storage
    sessionStorage.setItem('activeTab', tabId);
    
    // Load data when switching to account groups tab
    if (tabId === 'account-groups') {
        loadAccountGroups();
    }
}

// On page load, check for active tab in session storage
document.addEventListener('DOMContentLoaded', function() {
    const activeTab = sessionStorage.getItem('activeTab');
    if (activeTab) {
        switchTab(activeTab);
    } else {
        // If no active tab, load account groups data for when user switches to that tab
        loadAccountGroups();
    }
});

// Account Type Modal Functions
function openAddAccountTypeModal() {
    document.getElementById('accountTypeForm').reset();
    document.getElementById('accountTypeId').value = '';
    document.getElementById('modalTitle').textContent = 'إضافة نوع حساب جديد';
    document.getElementById('accountTypeModal').classList.remove('hidden');
    setTimeout(() => {
        document.querySelector('#accountTypeModal .modal-content').classList.remove('scale-95');
    }, 10);
}

async function openEditAccountTypeModal(id) {
    try {
        const response = await fetch(`/financial-settings/account-types/${id}`);
        const data = await response.json();
        if (data) {
            document.getElementById('accountTypeId').value = data.id;
            document.getElementById('key').value = data.key;
            document.getElementById('name_ar').value = data.name_ar;
            document.getElementById('name_en').value = data.name_en;
            document.getElementById('icon').value = data.icon;
            document.getElementById('sort_order').value = data.sort_order;
            document.getElementById('is_active').checked = data.is_active;
            document.getElementById('modalTitle').textContent = 'تعديل نوع الحساب';
            document.getElementById('accountTypeModal').classList.remove('hidden');
            setTimeout(() => {
                document.querySelector('#accountTypeModal .modal-content').classList.remove('scale-95');
            }, 10);
        } else {
            alert('لم يتم العثور على البيانات');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تحميل البيانات');
    }
}

function closeAccountTypeModal() {
    document.querySelector('#accountTypeModal .modal-content').classList.add('scale-95');
    setTimeout(() => {
        document.getElementById('accountTypeModal').classList.add('hidden');
    }, 300);
}

// Account Group Modal Functions
function openAccountGroupModal(id = null) {
    const modal = document.getElementById('accountGroupModal');
    const modalContent = document.getElementById('accountGroupModalContent');
    
    if (!id) {
        // This is for adding a new group
        document.getElementById('accountGroupForm').reset();
        document.getElementById('accountGroupId').value = '';
        document.getElementById('accountGroupModalTitle').textContent = 'إضافة مجموعة حسابات جديدة';
        
        // Ensure all fields are enabled for adding
        document.getElementById('groupName').readOnly = false;
        document.getElementById('groupCode').readOnly = false;
        document.getElementById('groupDescription').readOnly = false;
        document.getElementById('groupSortOrder').readOnly = false;
        document.getElementById('groupIsActive').disabled = false;
        
        // Show the save button
        document.getElementById('submitAccountGroupBtn').style.display = 'block';
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        modalContent.classList.remove('scale-95');
    }, 10);
}

function closeAccountGroupModal() {
    const modal = document.getElementById('accountGroupModal');
    const modalContent = document.getElementById('accountGroupModalContent');
    modalContent.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

async function loadAccountGroups() {
    try {
        const response = await fetch('/financial-settings/account-groups');
        const result = await response.json();
        if (result.success) {
            const tbody = document.getElementById('account-groups-tbody');
            tbody.innerHTML = ''; // Clear existing rows
            result.data.forEach(group => {
                const row = `
                    <tr class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 dark:hover:from-gray-700 dark:hover:to-gray-600 transition-all duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <code class="px-3 py-1.5 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-lg text-sm font-mono font-semibold text-green-600 dark:text-green-400">${group.code || '-'}</code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-900 dark:text-white font-semibold text-base">${group.name}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-600 dark:text-gray-400">${group.description || '-'}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 dark:from-blue-900 dark:to-cyan-900 dark:text-blue-200 shadow-sm flex items-center gap-2 w-fit">
                                <i class="fas fa-file-invoice-dollar"></i>
                                ${group.chart_accounts_count} حساب
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${group.is_active ? 
                                `<span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-green-400 to-emerald-500 text-white shadow-md flex items-center gap-2 w-fit"><i class="fas fa-check-circle"></i> مفعل</span>` : 
                                `<span class="px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-red-400 to-rose-500 text-white shadow-md flex items-center gap-2 w-fit"><i class="fas fa-times-circle"></i> معطل</span>`
                            }
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-800 dark:text-gray-200 font-bold text-lg">${group.sort_order}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <button onclick="viewAccountGroup(${group.id})" title="عرض" class="p-2 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800 transition-all duration-200 transform hover:scale-110">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editAccountGroup(${group.id})" title="تعديل" class="p-2 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800 transition-all duration-200 transform hover:scale-110">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button onclick="deleteAccountGroup(${group.id})" title="حذف" class="p-2 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800 transition-all duration-200 transform hover:scale-110">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } else {
            const tbody = document.getElementById('account-groups-tbody');
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-12"><div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400"><i class="fas fa-folder-open text-6xl mb-4"></i><h3 class="text-2xl font-bold mb-2">لا توجد بيانات</h3><p>لم يتم إضافة أي مجموعات حسابات بعد.</p></div></td></tr>`;
        }
    } catch (error) {
        console.error('Error loading account groups:', error);
        const tbody = document.getElementById('account-groups-tbody');
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-12"><div class="flex flex-col items-center justify-center text-red-500"><i class="fas fa-exclamation-triangle text-6xl mb-4"></i><h3 class="text-2xl font-bold mb-2">فشل تحميل البيانات</h3><p>حدث خطأ أثناء جلب مجموعات الحسابات.</p></div></td></tr>`;
    }
}

async function viewAccountGroup(id) {
    try {
        const response = await fetch(`/financial-settings/account-groups/${id}`);
        const result = await response.json();

        if (result.success) {
            const group = result.data;

            // Set fields to readonly and disable checkbox
            document.getElementById('groupName').readOnly = true;
            document.getElementById('groupCode').readOnly = true;
            document.getElementById('groupDescription').readOnly = true;
            document.getElementById('groupSortOrder').readOnly = true;
            document.getElementById('groupIsActive').disabled = true;

            // Hide the save button
            document.getElementById('submitAccountGroupBtn').style.display = 'none';

            // Fill the form with data
            document.getElementById('accountGroupId').value = group.id;
            document.getElementById('groupName').value = group.name;
            document.getElementById('groupCode').value = group.code || '';
            document.getElementById('groupDescription').value = group.description || '';
            document.getElementById('groupSortOrder').value = group.sort_order;
            document.getElementById('groupIsActive').checked = group.is_active;

            // Open the modal
            openAccountGroupModal(id);
            document.getElementById('accountGroupModalTitle').textContent = 'عرض مجموعة حسابات';
        } else {
            alert(result.message || 'حدث خطأ');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تحميل البيانات');
    }
}

async function editAccountGroup(id) {
    try {
        const response = await fetch(`/financial-settings/account-groups/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const group = result.data;
            
            // Make fields editable and enable checkbox
            document.getElementById('groupName').readOnly = false;
            document.getElementById('groupCode').readOnly = false;
            document.getElementById('groupDescription').readOnly = false;
            document.getElementById('groupSortOrder').readOnly = false;
            document.getElementById('groupIsActive').disabled = false;
            
            // Show the save button
            document.getElementById('submitAccountGroupBtn').style.display = 'block';
            
            // Fill the form with data
            document.getElementById('accountGroupId').value = group.id;
            document.getElementById('groupName').value = group.name;
            document.getElementById('groupCode').value = group.code || '';
            document.getElementById('groupDescription').value = group.description || '';
            document.getElementById('groupSortOrder').value = group.sort_order;
            document.getElementById('groupIsActive').checked = group.is_active;
            
            // Open the modal
            openAccountGroupModal(id);
            document.getElementById('accountGroupModalTitle').textContent = 'تعديل مجموعة حسابات';
        } else {
            alert(result.message || 'حدث خطأ');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تحميل البيانات');
    }
}

async function deleteAccountGroup(id) {
    if (!confirm('هل أنت متأكد من حذف هذه المجموعة؟')) {
        return;
    }
    
    try {
        const response = await fetch(`/financial-settings/account-groups/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            loadAccountGroups(); // Reload the table data
        } else {
            alert(result.message || 'حدث خطأ');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('حدث خطأ أثناء الحذف');
    }
}

// Account Group Form Submit
document.getElementById('accountGroupForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitAccountGroupBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> جاري الحفظ...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Convert checkbox to boolean
    data.is_active = document.getElementById('groupIsActive').checked;
    
    const id = document.getElementById('accountGroupId').value;
    
    const url = id ? `/financial-settings/account-groups/${id}` : '/financial-settings/account-groups';
    const method = id ? 'PUT' : 'POST';
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            let errorMessage = `خطأ ${response.status}: ${errorData.message}`;
            if(errorData.errors) {
                errorMessage += '\n' + Object.values(errorData.errors).map(e => e.join(', ')).join('\n');
            }
            alert(errorMessage);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            closeAccountGroupModal();
            loadAccountGroups(); // Reload the table data
        } else {
            alert(result.message || 'حدث خطأ');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    } catch (error) {
        console.error('Exception:', error);
        alert('حدث خطأ: ' + error.message);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Close modal on outside click
document.getElementById('accountGroupModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAccountGroupModal();
    }
});
</script>
<!-- Enhanced Add/Edit Account Type Modal -->
<div id="accountTypeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative w-full max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 modal-content">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-2xl p-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tags text-white text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white" id="modalTitle">
                        إضافة نوع حساب جديد
                    </h3>
                </div>
                <button onclick="closeAccountTypeModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-all duration-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="accountTypeForm" class="p-8 space-y-6">
            @csrf
            <input type="hidden" id="accountTypeId" name="id">

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-key text-indigo-600 ml-2"></i>
                        المفتاح (Key) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="key" id="key" required
                           placeholder="مثال: customer"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        استخدم حروف إنجليزية صغيرة وشرطة سفلية فقط
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-sort-numeric-down text-indigo-600 ml-2"></i>
                        الترتيب
                    </label>
                    <input type="number" name="sort_order" id="sort_order" value="0"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-language text-indigo-600 ml-2"></i>
                        الاسم بالعربي <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name_ar" id="name_ar" required
                           placeholder="مثال: عميل"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-globe-americas text-indigo-600 ml-2"></i>
                        الاسم بالإنجليزي
                    </label>
                    <input type="text" name="name_en" id="name_en"
                           placeholder="Example: Customer"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                    <i class="fas fa-icons text-indigo-600 ml-2"></i>
                    الأيقونة (Font Awesome)
                </label>
                <input type="text" name="icon" id="icon"
                       placeholder="مثال: fas fa-user"
                       class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                <p class="text-xs text-gray-500 mt-2 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    ابحث عن أيقونات في <a href="https://fontawesome.com/icons" target="_blank" class="text-indigo-600 hover:underline">مكتبة Font Awesome</a>
                </p>
            </div>

            <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 p-4 rounded-xl">
                <label for="is_active" class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" id="is_active" name="is_active" class="sr-only">
                        <div class="block bg-gray-300 dark:bg-gray-600 w-14 h-8 rounded-full"></div>
                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition"></div>
                    </div>
                    <div class="ml-3 text-gray-700 dark:text-gray-300 font-semibold">
                        تفعيل النوع
                    </div>
                </label>
                <p class="text-sm text-gray-500 dark:text-gray-400">هل هذا النوع مفعل وجاهز للاستخدام؟</p>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end items-center pt-6 border-t border-gray-200 dark:border-gray-700 space-x-4 space-x-reverse">
                <button type="button" onclick="closeAccountTypeModal()" 
                        class="px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-xl hover:bg-gray-300 dark:hover:bg-gray-500 transition-all duration-300 font-semibold">
                    إلغاء
                </button>
                <button type="submit" id="submitBtn"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl transition-all duration-300 flex items-center gap-3 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-save text-xl"></i>
                    <span class="font-semibold">حفظ التغييرات</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Account Group Modal -->
<div id="accountGroupModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div id="accountGroupModalContent" class="relative w-full max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all duration-300 scale-95">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-t-2xl p-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-layer-group text-white text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white" id="accountGroupModalTitle">
                        إضافة مجموعة حسابات جديدة
                    </h3>
                </div>
                <button onclick="closeAccountGroupModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-all duration-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="accountGroupForm" class="p-8 space-y-6" action="javascript:void(0);">
            @csrf
            <input type="hidden" id="accountGroupId" name="id">

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-tag text-green-600 ml-2"></i>
                        اسم المجموعة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="groupName" required
                           placeholder="مثال: أصول متداولة"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-all duration-200 read-only:bg-gray-100 dark:read-only:bg-gray-700/50">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-barcode text-green-600 ml-2"></i>
                        الكود
                    </label>
                    <input type="text" name="code" id="groupCode"
                           placeholder="مثال: 101"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-all duration-200 read-only:bg-gray-100 dark:read-only:bg-gray-700/50">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                    <i class="fas fa-align-left text-green-600 ml-2"></i>
                    الوصف
                </label>
                <textarea name="description" id="groupDescription" rows="3"
                          placeholder="أدخل وصفاً للمجموعة"
                          class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-all duration-200 read-only:bg-gray-100 dark:read-only:bg-gray-700/50"></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                    <i class="fas fa-sort-numeric-down text-green-600 ml-2"></i>
                    الترتيب
                </label>
                <input type="number" name="sort_order" id="groupSortOrder" value="0"
                       class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-all duration-200 read-only:bg-gray-100 dark:read-only:bg-gray-700/50">
            </div>

            <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 p-4 rounded-xl">
                <label for="groupIsActive" class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" id="groupIsActive" name="is_active" class="sr-only disabled:opacity-50">
                        <div class="block bg-gray-300 dark:bg-gray-600 w-14 h-8 rounded-full"></div>
                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition"></div>
                    </div>
                    <div class="ml-3 text-gray-700 dark:text-gray-300 font-semibold">
                        تفعيل المجموعة
                    </div>
                </label>
                <p class="text-sm text-gray-500 dark:text-gray-400">هل هذه المجموعة مفعلة وجاهزة للاستخدام؟</p>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end items-center pt-6 border-t border-gray-200 dark:border-gray-700 space-x-4 space-x-reverse">
                <button type="button" onclick="closeAccountGroupModal()" 
                        class="px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-xl hover:bg-gray-300 dark:hover:bg-gray-500 transition-all duration-300 font-semibold">
                    إلغاء
                </button>
                <button type="submit" id="submitAccountGroupBtn"
                        class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl transition-all duration-300 flex items-center gap-3 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-save text-xl"></i>
                    <span class="font-semibold">حفظ</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Custom checkbox styling */
    input[type="checkbox"]:checked ~ .dot {
        transform: translateX(100%);
        background-color: #10B981; /* Emerald-500 */
    }
    input[type="checkbox"]:checked ~ .block {
        background-color: #A7F3D0; /* Emerald-200 */
    }
    input[type="checkbox"]:disabled + .block {
        background-color: #D1D5DB; /* Gray-300 */
        cursor: not-allowed;
    }
    input[type="checkbox"]:disabled ~ .dot {
        background-color: #9CA3AF; /* Gray-400 */
        cursor: not-allowed;
    }
</style>

@endpush

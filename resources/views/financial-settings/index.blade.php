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
                                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-700 dark:text-gray-300 font-semibold">{{ $type->sort_order }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editAccountType({{ $type->id }})" 
                                                    class="p-2.5 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 text-white shadow-md hover:shadow-lg transform hover:scale-110 transition-all duration-200">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if(!$type->is_system)
                                            <button onclick="deleteAccountType({{ $type->id }})" 
                                                    class="p-2.5 rounded-lg bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white shadow-md hover:shadow-lg transform hover:scale-110 transition-all duration-200">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-inbox text-4xl text-gray-400"></i>
                                            </div>
                                            <p class="text-gray-500 dark:text-gray-400 text-lg font-semibold">لا توجد أنواع حسابات</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">ابدأ بإضافة نوع حساب جديد</p>
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
                    <button onclick="openAddAccountGroupModal()" 
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
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($accountGroups as $group)
                                <tr class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 dark:hover:from-gray-700 dark:hover:to-gray-600 transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($group->code)
                                        <code class="px-3 py-1.5 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-lg text-sm font-mono font-semibold text-green-600 dark:text-green-400">{{ $group->code }}</code>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-gray-900 dark:text-white font-semibold text-base">{{ $group->name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-gray-600 dark:text-gray-400">{{ $group->description ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1.5 bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900 dark:to-cyan-900 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-semibold">
                                            {{ $group->accounts_count }} حساب
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($group->is_active)
                                        <span class="px-3 py-1.5 bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900 dark:to-emerald-900 text-green-700 dark:text-green-300 rounded-lg text-sm font-semibold">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            مفعل
                                        </span>
                                        @else
                                        <span class="px-3 py-1.5 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 text-gray-600 dark:text-gray-400 rounded-lg text-sm font-semibold">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            معطل
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-gray-600 dark:text-gray-400 font-mono">{{ $group->sort_order }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <button onclick="editAccountGroup({{ $group->id }})" 
                                                    class="p-2.5 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white shadow-md hover:shadow-lg transform hover:scale-110 transition-all duration-200">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteAccountGroup({{ $group->id }})" 
                                                    class="p-2.5 rounded-lg bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white shadow-md hover:shadow-lg transform hover:scale-110 transition-all duration-200">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-layer-group text-4xl text-gray-400"></i>
                                            </div>
                                            <p class="text-gray-500 dark:text-gray-400 text-lg font-semibold">لا توجد مجموعات حسابات</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">ابدأ بإضافة مجموعة جديدة لتصنيف الحسابات</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Chart Groups Tab -->
            <div id="content-chart-groups" class="tab-content hidden">
                <!-- Enhanced Header -->
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                            <div class="w-2 h-8 bg-gradient-to-b from-blue-500 to-cyan-600 rounded-full"></div>
                            مجموعات الحسابات (الأدلة المحاسبية)
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 mt-2 mr-5">عرض وإدارة الأدلة المحاسبية المختلفة</p>
                    </div>
                    <a href="{{ route('chart-of-accounts.index') }}" 
                       class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl transition-all duration-300 flex items-center gap-3 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-sitemap text-xl"></i>
                        <span class="font-semibold">إدارة المجموعات</span>
                    </a>
                </div>

                <!-- Enhanced Chart Groups Table -->
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                                <tr>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الاسم</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الوحدة</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">عدد الحسابات</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الحالة</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">تاريخ الإنشاء</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($chartGroups as $group)
                                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 dark:hover:from-gray-700 dark:hover:to-gray-600 transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-gray-900 dark:text-white font-semibold text-base">{{ $group->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-gray-600 dark:text-gray-400">{{ $group->unit->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-indigo-900 dark:to-purple-900 text-indigo-700 dark:text-indigo-300 rounded-lg font-semibold">
                                            {{ $group->accounts_count ?? 0 }} حساب
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($group->is_active)
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
                                        <span class="text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="fas fa-calendar-alt text-gray-400"></i>
                                            {{ $group->created_at->format('Y-m-d') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('chart-of-accounts.show', $group->id) }}" 
                                           class="p-2.5 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-md hover:shadow-lg transform hover:scale-110 transition-all duration-200 inline-block">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-inbox text-4xl text-gray-400"></i>
                                            </div>
                                            <p class="text-gray-500 dark:text-gray-400 text-lg font-semibold">لا توجد مجموعات حسابات</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">ابدأ بإنشاء دليل محاسبي جديد</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Add/Edit Account Group Modal -->
<div id="accountGroupModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="accountGroupModalContent">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-6 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-layer-group text-white text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white" id="accountGroupModalTitle">إضافة مجموعة حسابات جديدة</h3>
                </div>
                <button onclick="closeAccountGroupModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Modal Body -->
        <form id="accountGroupForm" class="p-6 space-y-6">
            <input type="hidden" id="accountGroupId" name="id">
            
            <div class="grid grid-cols-2 gap-6">
                <!-- Name -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-tag text-green-500 mr-2"></i>
                        اسم المجموعة *
                    </label>
                    <input type="text" id="groupName" name="name" required
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                           placeholder="مثال: أعمال الموظفين">
                </div>
                
                <!-- Code -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-code text-green-500 mr-2"></i>
                        الكود
                    </label>
                    <input type="text" id="groupCode" name="code"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                           placeholder="مثال: EMP">
                </div>
                
                <!-- Sort Order -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-sort-numeric-down text-green-500 mr-2"></i>
                        الترتيب
                    </label>
                    <input type="number" id="groupSortOrder" name="sort_order" value="0"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                </div>
                
                <!-- Description -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-align-right text-green-500 mr-2"></i>
                        الوصف
                    </label>
                    <textarea id="groupDescription" name="description" rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-all duration-200"
                              placeholder="وصف مختصر للمجموعة..."></textarea>
                </div>
                
                <!-- Is Active -->
                <div class="col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="groupIsActive" name="is_active" checked
                               class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <i class="fas fa-toggle-on text-green-500 mr-2"></i>
                            مفعل
                        </span>
                    </label>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" id="submitAccountGroupBtn"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-save mr-2"></i>
                    حفظ
                </button>
                <button type="button" onclick="closeAccountGroupModal()"
                        class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold transition-all duration-200">
                    <i class="fas fa-times mr-2"></i>
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Account Groups Functions
function openAddAccountGroupModal() {
    document.getElementById('accountGroupModalTitle').textContent = 'إضافة مجموعة حسابات جديدة';
    document.getElementById('accountGroupForm').reset();
    document.getElementById('accountGroupId').value = '';
    document.getElementById('groupIsActive').checked = true;
    
    const modal = document.getElementById('accountGroupModal');
    const content = document.getElementById('accountGroupModalContent');
    
    modal.style.display = 'flex';
    setTimeout(() => {
        content.style.transform = 'scale(1)';
        content.style.opacity = '1';
    }, 10);
}

function closeAccountGroupModal() {
    const modal = document.getElementById('accountGroupModal');
    const content = document.getElementById('accountGroupModalContent');
    
    content.style.transform = 'scale(0.95)';
    content.style.opacity = '0';
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

async function editAccountGroup(id) {
    try {
        const response = await fetch(`/financial-settings/account-groups/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) {
            alert('حدث خطأ أثناء جلب بيانات المجموعة');
            return;
        }
        
        const result = await response.json();
        
        if (result.success && result.data) {
            const group = result.data;
            
            // ملء النموذج بالبيانات
            document.getElementById('accountGroupId').value = group.id;
            document.getElementById('groupName').value = group.name;
            document.getElementById('groupCode').value = group.code || '';
            document.getElementById('groupDescription').value = group.description || '';
            document.getElementById('groupIsActive').checked = group.is_active;
            document.getElementById('groupSortOrder').value = group.sort_order;
            
            // تغيير عنوان النموذج
            document.getElementById('accountGroupModalTitle').textContent = 'تعديل مجموعة حسابات';
            
            // فتح النموذج
            openAccountGroupModal(group.id);
        } else {
            alert('حدث خطأ أثناء جلب بيانات المجموعة');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('حدث خطأ أثناء جلب بيانات المجموعة');
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
            location.reload();
        } else {
            alert(result.message || 'حدث خطأ');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('حدث خطأ أثناء الحذف');
    }
}

async function editAccountGroup(id) {
    try {
        const response = await fetch(`/financial-settings/account-groups/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const group = result.data;
            
            // إعادة تفعيل جميع الحقول (في حالة كانت معطلة من وضع العرض)
            document.getElementById('groupName').disabled = false;
            document.getElementById('groupCode').disabled = false;
            document.getElementById('groupDescription').disabled = false;
            document.getElementById('groupSortOrder').disabled = false;
            document.getElementById('groupIsActive').disabled = false;
            
            // إظهار زر الحفظ
            document.getElementById('submitAccountGroupBtn').style.display = 'block';
            
            // ملء النموذج بالبيانات
            document.getElementById('accountGroupId').value = group.id;
            document.getElementById('groupName').value = group.name;
            document.getElementById('groupCode').value = group.code || '';
            document.getElementById('groupDescription').value = group.description || '';
            document.getElementById('groupSortOrder').value = group.sort_order;
            document.getElementById('groupIsActive').checked = group.is_active;
            
            // فتح النموذج
            openAccountGroupModal(id);
            document.getElementById('accountGroupModalTitle').textContent = 'تعديل مجموعة حسابات';disabled = true;
            document.getElementById('groupDescription').disabled = true;
            document.getElementById('groupSortOrder').disabled = true;
            document.getElementById('groupIsActive').disabled = true;
            
            // إخفاء زر الحفظ
            document.getElementById('submitAccountGroupBtn').style.display = 'none';
            
            // فتح النموذج
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

// Account Group Form Submit
document.getElementById('accountGroupForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitAccountGroupBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> جاري الحفظ...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // تحويل checkbox إلى boolean
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
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('HTTP Error:', response.status, errorText);
            alert(`خطأ HTTP ${response.status}`);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        const result = await response.json();
        console.log('Result:', result);
        
        if (result.success) {
            alert(result.message);
            // إغلاق النموذج
            closeAccountGroupModal();
            // حفظ التبويب النشط قبل إعادة التحميل
            sessionStorage.setItem('activeTab', 'account-groups');
            location.reload();
        } else {
            console.error('API Error:', result);
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
                        <i class="fas fa-font text-indigo-600 ml-2"></i>
                        الاسم بالعربي <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name_ar" id="name_ar" required
                           placeholder="مثال: عميل"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-language text-indigo-600 ml-2"></i>
                        الاسم بالإنجليزي
                    </label>
                    <input type="text" name="name_en" id="name_en"
                           placeholder="مثال: Customer"
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
                    <i class="fas fa-external-link-alt"></i>
                    ابحث عن الأيقونة في <a href="https://fontawesome.com/icons" target="_blank" class="text-indigo-600 hover:text-indigo-700 font-semibold">Font Awesome</a>
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                    <i class="fas fa-align-right text-indigo-600 ml-2"></i>
                    الوصف
                </label>
                <textarea name="description" id="description" rows="4"
                          placeholder="وصف مختصر للنوع..."
                          class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-all duration-200"></textarea>
            </div>

            <div class="flex items-center p-4 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 rounded-xl">
                <input type="checkbox" name="is_active" id="is_active" checked
                       class="w-6 h-6 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_active" class="mr-3 text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-toggle-on text-indigo-600"></i>
                    تفعيل النوع
                </label>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeAccountTypeModal()" 
                        class="px-8 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition-all duration-200 font-semibold flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    إلغاء
                </button>
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl transition-all duration-200 font-semibold flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-save"></i>
                    حفظ النوع
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
/* Enhanced Tab Styles */
.tab-button:not(.active) .absolute {
    transform: scaleX(0);
}

.tab-button.active .absolute {
    transform: scaleX(1);
}

.tab-button:not(.active) span {
    color: #6b7280;
}

.tab-button.active span {
    color: #4f46e5;
}

/* Modal Animation */
.modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Table Row Hover Effect */
tbody tr {
    transition: all 0.2s ease;
}

tbody tr:hover {
    transform: translateX(-4px);
}

/* Smooth Transitions */
* {
    transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
@endpush

@push('scripts')
<script>
// Enhanced Tab Switching with Animation
function switchTab(tabName) {
    // Hide all tab contents with fade out
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.opacity = '0';
        setTimeout(() => {
            content.classList.add('hidden');
        }, 150);
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
        const indicator = button.querySelector('.absolute');
        if (indicator) {
            indicator.style.transform = 'scaleX(0)';
        }
    });
    
    // Show selected tab content with fade in
    setTimeout(() => {
        const selectedContent = document.getElementById('content-' + tabName);
        selectedContent.classList.remove('hidden');
        setTimeout(() => {
            selectedContent.style.opacity = '1';
        }, 50);
    }, 150);
    
    // Add active class to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active');
    const activeIndicator = activeButton.querySelector('.absolute');
    if (activeIndicator) {
        activeIndicator.style.transform = 'scaleX(1)';
    }
    
    // تحميل مجموعات الحسابات عند فتح التبويب
    if (tabName === 'account-groups') {
        loadAccountGroups();
    }
}

// Enhanced Modal Functions
function openAddAccountTypeModal() {
    document.getElementById('modalTitle').textContent = 'إضافة نوع حساب جديد';
    document.getElementById('accountTypeForm').reset();
    document.getElementById('accountTypeId').value = '';
    const modal = document.getElementById('accountTypeModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.modal-content').style.transform = 'scale(1)';
    }, 10);
}

function closeAccountTypeModal() {
    const modal = document.getElementById('accountTypeModal');
    modal.querySelector('.modal-content').style.transform = 'scale(0.95)';
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

function editAccountType(id) {
    // TODO: Load account type data and show modal
    console.log('Edit account type:', id);
    openAddAccountTypeModal();
    document.getElementById('modalTitle').textContent = 'تعديل نوع الحساب';
}

function deleteAccountType(id) {
    if (confirm('هل أنت متأكد من حذف هذا النوع؟ لا يمكن التراجع عن هذا الإجراء.')) {
        fetch(`/financial-settings/account-types/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء الحذف');
        });
    }
}

// Form Submit with Loading State
document.getElementById('accountTypeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    const id = document.getElementById('accountTypeId').value;
    
    const url = id ? `/financial-settings/account-types/${id}` : '/financial-settings/account-types';
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
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('HTTP Error:', response.status, errorText);
            alert(`خطأ HTTP ${response.status}`);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        const result = await response.json();
        console.log('Result:', result);
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            console.error('API Error:', result);
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
document.getElementById('accountTypeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAccountTypeModal();
    }
});

// Account Group Modal Functions
function openAccountGroupModal(id = null) {
    const modal = document.getElementById('accountGroupModal');
    const modalContent = document.getElementById('accountGroupModalContent');
    const form = document.getElementById('accountGroupForm');
    const title = document.getElementById('accountGroupModalTitle');
    
    // إظهار النموذج
    modal.style.display = 'flex';
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    if (id) {
        // وضع التعديل - لا تعيد تعيين النموذج
        title.textContent = 'تعديل مجموعة حسابات';
    } else {
        // وضع الإضافة - إعادة تعيين النموذج
        form.reset();
        document.getElementById('accountGroupId').value = '';
        document.getElementById('groupIsActive').checked = true;
        title.textContent = 'إضافة مجموعة حسابات جديدة';
    }
}

function closeAccountGroupModal() {
    const modal = document.getElementById('accountGroupModal');
    const modalContent = document.getElementById('accountGroupModalContent');
    
    // إعادة تفعيل جميع الحقول (في حالة كانت معطلة من وضع العرض)
    document.getElementById('groupName').disabled = false;
    document.getElementById('groupCode').disabled = false;
    document.getElementById('groupDescription').disabled = false;
    document.getElementById('groupSortOrder').disabled = false;
    document.getElementById('groupIsActive').disabled = false;
    
    // إظهار زر الحفظ
    document.getElementById('submitAccountGroupBtn').style.display = 'block';
    
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Close modal on outside click
document.getElementById('accountGroupModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAccountGroupModal();
    }
});

// دالة تحميل مجموعات الحسابات
async function loadAccountGroups() {
    try {
        const response = await fetch('/financial-settings/account-groups', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) {
            console.error('Failed to load account groups');
            return;
        }
        
        const result = await response.json();
        
        if (result.success && result.data) {
            renderAccountGroups(result.data);
        }
    } catch (error) {
        console.error('Error loading account groups:', error);
    }
}

// دالة عرض مجموعات الحسابات
function renderAccountGroups(groups) {
    const tbody = document.querySelector('#content-account-groups tbody');
    
    if (!groups || groups.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-layer-group text-4xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-lg font-semibold">لا توجد مجموعات حسابات</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">ابدأ بإضافة مجموعة جديدة لتصنيف الحسابات</p>
                        </div>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = groups.map(group => `
        <tr class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 dark:hover:from-gray-700 dark:hover:to-gray-600 transition-all duration-200">
            <td class="px-6 py-4 whitespace-nowrap">
                ${group.code ? `<code class="px-3 py-1.5 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-lg text-sm font-mono font-semibold text-green-600 dark:text-green-400">${group.code}</code>` : '<span class="text-gray-400">-</span>'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-gray-900 dark:text-white font-semibold text-base">${group.name}</span>
            </td>
            <td class="px-6 py-4">
                <span class="text-gray-600 dark:text-gray-400">${group.description || '-'}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-3 py-1.5 bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900 dark:to-cyan-900 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-semibold">
                    ${group.accounts_count} حساب
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${group.is_active ? 
                    '<span class="px-3 py-1.5 bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900 dark:to-emerald-900 text-green-700 dark:text-green-300 rounded-lg text-sm font-semibold"><i class="fas fa-check-circle mr-1"></i>مفعل</span>' : 
                    '<span class="px-3 py-1.5 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 text-gray-600 dark:text-gray-400 rounded-lg text-sm font-semibold"><i class="fas fa-times-circle mr-1"></i>معطل</span>'
                }
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-gray-600 dark:text-gray-400 font-mono">${group.sort_order}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-2">
                    <button onclick="viewAccountGroup(${group.id})" 
                            class="p-2.5 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-md hover:shadow-lg transform hover:scale-110 transition-all duration-200"
                            title="عرض">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="editAccountGroup(${group.id})" 
                            class="p-2.5 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white shadow-md hover:shadow-lg transform hover:scale-110 transition-all duration-200"
                            title="تعديل">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteAccountGroup(${group.id})" 
                            class="p-2.5 rounded-lg bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white shadow-md hover:shadow-lg transform hover:scale-110 transition-all duration-200"
                            title="حذف">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // استعادة التبويب النشط بعد إعادة التحميل
    const activeTab = sessionStorage.getItem('activeTab');
    if (activeTab) {
        sessionStorage.removeItem('activeTab');
        const tabButton = document.getElementById('tab-' + activeTab);
        if (tabButton) {
            tabButton.click();
            // تحميل مجموعات الحسابات إذا كان التبويب النشط هو مجموعات الحسابات
            if (activeTab === 'account-groups') {
                loadAccountGroups();
            }
        }
    } else {
        // Set initial opacity for visible tab
        document.getElementById('content-account-types').style.opacity = '1';
    }
});
</script>
@endpush
@endsection

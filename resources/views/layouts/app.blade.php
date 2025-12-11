<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SEMOP - نظام إدارة المؤسسات')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');
        
        body {
            font-family: 'Cairo', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .sidebar {
            transition: all 0.3s ease;
            max-height: calc(100vh - 64px);
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        
        .sidebar-collapsed {
            width: 80px;
        }
        
        .sidebar-expanded {
            width: 280px;
        }
        
        /* Gradient Icon Styles */
        .icon-gradient-blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .icon-gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .icon-gradient-orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .icon-gradient-teal {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .icon-gradient-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .icon-gradient-indigo {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .icon-gradient-gray {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .icon-gradient-pink {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .icon-gradient-purple {
            background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .icon-gradient-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .icon-gradient-yellow {
            background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Sidebar Item Hover Effects */
        .sidebar-item {
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .sidebar-item:hover::before {
            transform: scaleY(1);
        }
        
        /* Icon Box Shadow */
        .icon-shadow {
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        
        /* Active State */
        .sidebar-item-active {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.1) 0%, transparent 100%);
            border-right: 3px solid #667eea;
        }
    </style>
    
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/dark_mode.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <script src="{{ asset('js/dark_mode.js') }}" defer></script>
    <script src="{{ asset('js/dark-mode.js') }}" defer></script>
</head>
<body class="bg-gray-50">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-lg fixed w-full top-0 z-50">
        <div class="mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-4 space-x-reverse">
                    <button id="sidebarToggle" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-2xl font-bold gradient-bg bg-clip-text text-transparent">
                        SEMOP
                    </h1>
                    <span class="text-sm text-gray-500">نظام إدارة المؤسسات</span>
                </div>
                
                <!-- Right Menu -->
                <div class="flex items-center space-x-4 space-x-reverse">
                    <!-- Dark Mode Toggle -->
                    <button id="darkModeToggle" class="text-gray-600 hover:text-gray-900 transition-colors" title="تبديل الوضع الليلي">
                        <i class="fas fa-moon text-xl"></i>
                    </button>
                    
                    <!-- Notifications -->
                    <button class="relative text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                    </button>
                    
                    <!-- User Menu -->
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=667eea&color=fff" 
                             alt="User" 
                             class="h-10 w-10 rounded-full border-2 border-purple-500">
                        <div class="hidden md:block">
                            <p class="text-sm font-semibold text-gray-800">مدير النظام</p>
                            <p class="text-xs text-gray-500">admin@semop.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="flex pt-16">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar sidebar-expanded bg-white shadow-lg fixed right-0 h-full overflow-y-auto">
            <div class="p-4">
                <nav class="space-y-2">
                    <a href="{{ url('/') }}" class="sidebar-item flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gradient-to-r hover:from-purple-50 hover:to-transparent text-gray-700 hover:text-purple-600 transition-all duration-300">
                        <i class="fas fa-home text-2xl icon-gradient-purple icon-shadow"></i>
                        <span class="sidebar-text font-semibold">الرئيسية</span>
                    </a>
                    
                    <!-- النظام المحاسبي - Dropdown -->
                    <div class="relative">
                        <button onclick="toggleDropdown('accounting')" class="sidebar-item w-full flex items-center justify-between space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-700 hover:text-blue-600 transition-all duration-300">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <i class="fas fa-calculator text-2xl icon-gradient-blue icon-shadow"></i>
                                <span class="sidebar-text font-semibold">النظام المالي</span>
                            </div>
                            <i class="fas fa-chevron-down sidebar-text transition-transform duration-300" id="accounting-arrow"></i>
                        </button>
                        <div id="accounting-dropdown" class="hidden pr-6 space-y-1 mt-1">
                            <a href="{{ route('chart-of-accounts.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-purple-50 hover:to-transparent text-gray-600 hover:text-purple-600 text-sm transition-all duration-200">
                                <i class="fas fa-calculator text-lg icon-gradient-purple"></i>
                                <span class="sidebar-text">المحاسبة</span>
                            </a>
                            <a href="{{ route('chart-of-accounts.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-indigo-50 hover:to-transparent text-gray-600 hover:text-indigo-600 text-sm transition-all duration-200">
                                <i class="fas fa-book text-lg icon-gradient-indigo"></i>
                                <span class="sidebar-text">الأدلة المحاسبية</span>
                            </a>
                            <a href="{{ route('intermediate-accounts.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-pink-50 hover:to-transparent text-gray-600 hover:text-pink-600 text-sm transition-all duration-200">
                                <i class="fas fa-exchange-alt text-lg icon-gradient-pink"></i>
                                <span class="sidebar-text">الحسابات الوسيطة</span>
                            </a>

                            <a href="{{ route('journal-entries.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-600 hover:text-blue-600 text-sm transition-all duration-200">
                                <i class="fas fa-file-invoice text-lg icon-gradient-blue"></i>
                                <span class="sidebar-text">القيود المحاسبية</span>
                            </a>
                            <a href="{{ route('journal-templates.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-yellow-50 hover:to-transparent text-gray-600 hover:text-yellow-600 text-sm transition-all duration-200">
                                <i class="fas fa-layer-group text-lg icon-gradient-yellow"></i>
                                <span class="sidebar-text">قوالب القيود الذكية</span>
                            </a>
                            <a href="{{ route('auto-numbering.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-teal-50 hover:to-transparent text-gray-600 hover:text-teal-600 text-sm transition-all duration-200">
                                <i class="fas fa-hashtag text-lg icon-gradient-teal"></i>
                                <span class="sidebar-text">نظام الترقيم التلقائي</span>
                            </a>
                            <a href="{{ route('financial-settings.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-orange-50 hover:to-transparent text-gray-600 hover:text-orange-600 text-sm transition-all duration-200">
                                <i class="fas fa-cog text-lg icon-gradient-orange"></i>
                                <span class="sidebar-text">إعدادات النظام المالي</span>
                            </a>
                            <!-- Separator -->
                            <div class="border-t border-gray-200 my-2"></div>
                            
                            <!-- البنوك والصناديق -->
                            <div class="px-2 py-1">
                                <span class="text-xs font-bold text-gray-500 uppercase">البنوك والصناديق</span>
                            </div>
                            
                            <a href="{{ route('bank-accounts.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-600 hover:text-blue-600 text-sm transition-all duration-200">
                                <i class="fas fa-university text-lg icon-gradient-blue"></i>
                                <span class="sidebar-text">البنوك</span>
                            </a>
                            
                            <a href="{{ route('cash-receipts.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-transparent text-gray-600 hover:text-green-600 text-sm transition-all duration-200">
                                <i class="fas fa-money-bill-wave text-lg icon-gradient-green"></i>
                                <span class="sidebar-text">سندات القبض</span>
                            </a>
                            
                            <a href="{{ route('cash-payments.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent text-gray-600 hover:text-red-600 text-sm transition-all duration-200">
                                <i class="fas fa-hand-holding-usd text-lg icon-gradient-red"></i>
                                <span class="sidebar-text">سندات الصرف</span>
                            </a>
                            
                            <a href="{{ route('cash-boxes.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-purple-50 hover:to-transparent text-gray-600 hover:text-purple-600 text-sm transition-all duration-200">
                                <i class="fas fa-cash-register text-lg icon-gradient-purple"></i>
                                <span class="sidebar-text">الصناديق</span>
                            </a>
                            
                            <!-- Separator -->
                            <div class="border-t border-gray-200 my-2"></div>
                            
                            <!-- التقارير المحاسبية -->
                            <div class="px-2 py-1">
                                <span class="text-xs font-bold text-gray-500 uppercase">التقارير المحاسبية</span>
                            </div>
                            
                            <a href="{{ route('accounting-reports.dashboard') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-indigo-50 hover:to-transparent text-gray-600 hover:text-indigo-600 text-sm transition-all duration-200">
                                <i class="fas fa-chart-line text-lg icon-gradient-indigo"></i>
                                <span class="sidebar-text">لوحة التقارير</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- العملاء - Dropdown -->
                    <div class="relative">
                        <button onclick="toggleDropdown('customers')" class="sidebar-item w-full flex items-center justify-between space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-700 hover:text-blue-600 transition-all duration-300">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <i class="fas fa-users text-2xl icon-gradient-blue icon-shadow"></i>
                                <span class="sidebar-text font-semibold">العملاء</span>
                            </div>
                            <i class="fas fa-chevron-down sidebar-text transition-transform duration-300" id="customers-arrow"></i>
                        </button>
                        <div id="customers-dropdown" class="hidden pr-6 space-y-1 mt-1">
                            <a href="{{ route('customers.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-600 hover:text-blue-600 text-sm transition-all duration-200">
                                <i class="fas fa-list-alt text-lg icon-gradient-blue"></i>
                                <span class="sidebar-text">قائمة العملاء</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-transparent text-gray-600 hover:text-green-600 text-sm transition-all duration-200">
                                <i class="fas fa-user-plus text-lg icon-gradient-green"></i>
                                <span class="sidebar-text">إضافة عميل جديد</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-yellow-50 hover:to-transparent text-gray-600 hover:text-yellow-600 text-sm transition-all duration-200">
                                <i class="fas fa-layer-group text-lg icon-gradient-yellow"></i>
                                <span class="sidebar-text">مجموعات العملاء</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-indigo-50 hover:to-transparent text-gray-600 hover:text-indigo-600 text-sm transition-all duration-200">
                                <i class="fas fa-history text-lg icon-gradient-indigo"></i>
                                <span class="sidebar-text">سجل النشاط</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent text-gray-600 hover:text-red-600 text-sm transition-all duration-200">
                                <i class="fas fa-chart-bar text-lg icon-gradient-red"></i>
                                <span class="sidebar-text">تقارير العملاء</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- المخزون - Dropdown -->
                    <div class="relative">
                        <button onclick="toggleDropdown('inventory')" class="sidebar-item w-full flex items-center justify-between space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-transparent text-gray-700 hover:text-green-600 transition-all duration-300">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <i class="fas fa-box text-2xl icon-gradient-green icon-shadow"></i>
                                <span class="sidebar-text font-semibold">المخزون</span>
                            </div>
                            <i class="fas fa-chevron-down sidebar-text transition-transform" id="inventory-arrow"></i>
                        </button>
                        <div id="inventory-dropdown" class="hidden pr-6 space-y-1 mt-1">
                            <a href="{{ route('inventory.dashboard') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-transparent text-gray-600 hover:text-green-600 text-sm transition-all duration-200">
                                <i class="fas fa-tachometer-alt text-lg"></i>
                                <span class="sidebar-text">لوحة التحكم</span>
                            </a>
                            <a href="{{ route('inventory.items.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-transparent text-gray-600 hover:text-green-600 text-sm transition-all duration-200">
                                <i class="fas fa-boxes text-lg"></i>
                                <span class="sidebar-text">الأصناف</span>
                            </a>
                            <a href="{{ route('inventory.warehouses.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-600 hover:text-blue-600 text-sm transition-all duration-200">
                                <i class="fas fa-warehouse text-lg"></i>
                                <span class="sidebar-text">المخازن</span>
                            </a>
                            <a href="{{ route('inventory.warehouse-groups.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-purple-50 hover:to-transparent text-gray-600 hover:text-purple-600 text-sm transition-all duration-200">
                                <i class="fas fa-layer-group text-lg"></i>
                                <span class="sidebar-text">مجموعات المخازن</span>
                            </a>
                            <a href="{{ route('inventory.stock-movements.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-yellow-50 hover:to-transparent text-gray-600 hover:text-yellow-600 text-sm transition-all duration-200">
                                <i class="fas fa-exchange-alt text-lg"></i>
                                <span class="sidebar-text">حركات المخزون</span>
                            </a>
                            <a href="{{ route('inventory.reports.current-stock') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent text-gray-600 hover:text-red-600 text-sm transition-all duration-200">
                                <i class="fas fa-chart-pie text-lg"></i>
                                <span class="sidebar-text">تقارير المخزون</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- المشتريات - Dropdown -->
                    <div class="relative">
                        <button onclick="toggleDropdown('purchases')" class="sidebar-item w-full flex items-center justify-between space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gradient-to-r hover:from-orange-50 hover:to-transparent text-gray-700 hover:text-orange-600 transition-all duration-300">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <i class="fas fa-shopping-cart text-2xl icon-gradient-orange icon-shadow"></i>
                                <span class="sidebar-text font-semibold">المشتريات</span>
                            </div>
                            <i class="fas fa-chevron-down sidebar-text transition-transform" id="purchases-arrow"></i>
                        </button>
                        <div id="purchases-dropdown" class="hidden pr-6 space-y-1 mt-1">
                            <a href="{{ route('purchases.dashboard') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-600 hover:text-blue-600 text-sm transition-all duration-200">
                                <i class="fas fa-tachometer-alt text-lg"></i>
                                <span class="sidebar-text">لوحة التحكم</span>
                            </a>
                            <a href="{{ route('purchases.suppliers.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-transparent text-gray-600 hover:text-green-600 text-sm transition-all duration-200">
                                <i class="fas fa-truck text-lg"></i>
                                <span class="sidebar-text">الموردين</span>
                            </a>
                            <a href="{{ route('purchases.orders.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-600 hover:text-blue-600 text-sm transition-all duration-200">
                                <i class="fas fa-clipboard-list text-lg"></i>
                                <span class="sidebar-text">أوامر الشراء</span>
                            </a>
                            <a href="{{ route('purchases.receipts.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-purple-50 hover:to-transparent text-gray-600 hover:text-purple-600 text-sm transition-all duration-200">
                                <i class="fas fa-box-open text-lg"></i>
                                <span class="sidebar-text">استلام البضائع</span>
                            </a>
                            <a href="{{ route('purchases.invoices.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-orange-50 hover:to-transparent text-gray-600 hover:text-orange-600 text-sm transition-all duration-200">
                                <i class="fas fa-file-invoice-dollar text-lg"></i>
                                <span class="sidebar-text">فواتير الموردين</span>
                            </a>
                            <a href="{{ route('purchases.reports.orders') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent text-gray-600 hover:text-red-600 text-sm transition-all duration-200">
                                <i class="fas fa-chart-line text-lg"></i>
                                <span class="sidebar-text">تقارير المشتريات</span>
                            </a>
                            <a href="{{ route('purchases.settings.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-yellow-50 hover:to-transparent text-gray-600 hover:text-yellow-600 text-sm transition-all duration-200">
                                <i class="fas fa-cog text-lg"></i>
                                <span class="sidebar-text">إعدادات المشتريات</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- المبيعات - Dropdown -->
                    <div class="relative">
                        <button onclick="toggleDropdown('sales')" class="sidebar-item w-full flex items-center justify-between space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gradient-to-r hover:from-teal-50 hover:to-transparent text-gray-700 hover:text-teal-600 transition-all duration-300">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <i class="fas fa-chart-line text-2xl icon-gradient-teal icon-shadow"></i>
                                <span class="sidebar-text font-semibold">المبيعات</span>
                            </div>
                            <i class="fas fa-chevron-down sidebar-text transition-transform" id="sales-arrow"></i>
                        </button>
                        <div id="sales-dropdown" class="hidden pr-6 space-y-1 mt-1">
                            <a href="{{ route('sales.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-teal-50 hover:to-transparent text-gray-600 hover:text-teal-600 text-sm transition-all duration-200">
                                <i class="fas fa-file-invoice text-lg"></i>
                                <span class="sidebar-text">فواتير المبيعات</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-600 hover:text-blue-600 text-sm transition-all duration-200">
                                <i class="fas fa-file-contract text-lg"></i>
                                <span class="sidebar-text">عروض الأسعار</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-transparent text-gray-600 hover:text-green-600 text-sm transition-all duration-200">
                                <i class="fas fa-receipt text-lg"></i>
                                <span class="sidebar-text">نقاط البيع</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-yellow-50 hover:to-transparent text-gray-600 hover:text-yellow-600 text-sm transition-all duration-200">
                                <i class="fas fa-redo text-lg"></i>
                                <span class="sidebar-text">مرتجعات المبيعات</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent text-gray-600 hover:text-red-600 text-sm transition-all duration-200">
                                <i class="fas fa-chart-area text-lg"></i>
                                <span class="sidebar-text">تقارير المبيعات</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- الموارد البشرية - Dropdown -->
                    <div class="relative">
                        <button onclick="toggleDropdown('hr')" class="sidebar-item w-full flex items-center justify-between space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gradient-to-r hover:from-indigo-50 hover:to-transparent text-gray-700 hover:text-indigo-600 transition-all duration-300">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <i class="fas fa-user-tie text-2xl icon-gradient-indigo icon-shadow"></i>
                                <span class="sidebar-text font-semibold">الموارد البشرية</span>
                            </div>
                            <i class="fas fa-chevron-down sidebar-text transition-transform" id="hr-arrow"></i>
                        </button>
                        <div id="hr-dropdown" class="hidden pr-6 space-y-1 mt-1">
                            <a href="{{ route('hr.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-indigo-50 hover:to-transparent text-gray-600 hover:text-indigo-600 text-sm transition-all duration-200">
                                <i class="fas fa-users text-lg"></i>
                                <span class="sidebar-text">الموظفين</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-transparent text-gray-600 hover:text-green-600 text-sm transition-all duration-200">
                                <i class="fas fa-money-bill-wave text-lg"></i>
                                <span class="sidebar-text">الرواتب</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-600 hover:text-blue-600 text-sm transition-all duration-200">
                                <i class="fas fa-clock text-lg"></i>
                                <span class="sidebar-text">الحضور والانصراف</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-yellow-50 hover:to-transparent text-gray-600 hover:text-yellow-600 text-sm transition-all duration-200">
                                <i class="fas fa-calendar-alt text-lg"></i>
                                <span class="sidebar-text">الإجازات</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-purple-50 hover:to-transparent text-gray-600 hover:text-purple-600 text-sm transition-all duration-200">
                                <i class="fas fa-user-graduate text-lg"></i>
                                <span class="sidebar-text">التدريب</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent text-gray-600 hover:text-red-600 text-sm transition-all duration-200">
                                <i class="fas fa-chart-bar text-lg"></i>
                                <span class="sidebar-text">تقارير الموارد البشرية</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- التصنيع - Dropdown -->
                    <div class="relative">
                        <button onclick="toggleDropdown('manufacturing')" class="sidebar-item w-full flex items-center justify-between space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gradient-to-r hover:from-gray-50 hover:to-transparent text-gray-700 hover:text-gray-600 transition-all duration-300">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <i class="fas fa-industry text-2xl icon-gradient-gray icon-shadow"></i>
                                <span class="sidebar-text font-semibold">التصنيع</span>
                            </div>
                            <i class="fas fa-chevron-down sidebar-text transition-transform" id="manufacturing-arrow"></i>
                        </button>
                        <div id="manufacturing-dropdown" class="hidden pr-6 space-y-1 mt-1">
                            <a href="{{ route('manufacturing.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-gray-50 hover:to-transparent text-gray-600 hover:text-gray-600 text-sm transition-all duration-200">
                                <i class="fas fa-tasks text-lg"></i>
                                <span class="sidebar-text">أوامر الإنتاج</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-600 hover:text-blue-600 text-sm transition-all duration-200">
                                <i class="fas fa-cubes text-lg"></i>
                                <span class="sidebar-text">المواد الخام</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-transparent text-gray-600 hover:text-green-600 text-sm transition-all duration-200">
                                <i class="fas fa-cogs text-lg"></i>
                                <span class="sidebar-text">خطوط الإنتاج</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-yellow-50 hover:to-transparent text-gray-600 hover:text-yellow-600 text-sm transition-all duration-200">
                                <i class="fas fa-clipboard-check text-lg"></i>
                                <span class="sidebar-text">مراقبة الجودة</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent text-gray-600 hover:text-red-600 text-sm transition-all duration-200">
                                <i class="fas fa-chart-line text-lg"></i>
                                <span class="sidebar-text">تقارير الإنتاج</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- الأصول - Dropdown -->
                    <div class="relative">
                        <button onclick="toggleDropdown('assets')" class="sidebar-item w-full flex items-center justify-between space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gradient-to-r hover:from-pink-50 hover:to-transparent text-gray-700 hover:text-pink-600 transition-all duration-300">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <i class="fas fa-archive text-2xl icon-gradient-pink icon-shadow"></i>
                                <span class="sidebar-text font-semibold">الأصول</span>
                            </div>
                            <i class="fas fa-chevron-down sidebar-text transition-transform" id="assets-arrow"></i>
                        </button>
                        <div id="assets-dropdown" class="hidden pr-6 space-y-1 mt-1">
                            <a href="{{ route('assets.index') }}" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-pink-50 hover:to-transparent text-gray-600 hover:text-pink-600 text-sm transition-all duration-200">
                                <i class="fas fa-building text-lg"></i>
                                <span class="sidebar-text">الأصول الثابتة</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent text-gray-600 hover:text-blue-600 text-sm transition-all duration-200">
                                <i class="fas fa-tools text-lg"></i>
                                <span class="sidebar-text">الصيانة</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-yellow-50 hover:to-transparent text-gray-600 hover:text-yellow-600 text-sm transition-all duration-200">
                                <i class="fas fa-chart-line-down text-lg"></i>
                                <span class="sidebar-text">الاستهلاك</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-transparent text-gray-600 hover:text-green-600 text-sm transition-all duration-200">
                                <i class="fas fa-exchange-alt text-lg"></i>
                                <span class="sidebar-text">النقل والتحويل</span>
                            </a>
                            <a href="#" class="flex items-center space-x-3 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-red-50 hover:to-transparent text-gray-600 hover:text-red-600 text-sm transition-all duration-200">
                                <i class="fas fa-file-alt text-lg"></i>
                                <span class="sidebar-text">تقارير الأصول</span>
                            </a>
                        </div>
                    </div>
                    
                    <a href="{{ route('loyalty.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-star w-6"></i>
                        <span class="sidebar-text">نقاط الولاء</span>
                    </a>
                    
                    <a href="{{ route('insurance.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-heartbeat w-6"></i>
                        <span class="sidebar-text">التأمين الطبي</span>
                    </a>
                    
                    <a href="{{ route('genes.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-dna w-6"></i>
                        <span class="sidebar-text">الجينات</span>
                    </a>
                    
    
                    <a href="{{ route('partnership.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-orange-50 text-gray-700 hover:text-orange-600">
                        <i class="fas fa-handshake w-6"></i>
                        <span class="sidebar-text">محاسبة الشراكات</span>
                    </a>
                    

                    <!-- الهيكل التنظيمي -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="px-3 mb-2">
                            <span class="sidebar-text text-xs font-semibold text-gray-500 uppercase">الهيكل التنظيمي</span>
                        </div>
                        
                        <a href="{{ route('holdings.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600">
                            <i class="fas fa-building w-6"></i>
                            <span class="sidebar-text">الشركات القابضة</span>
                        </a>
                        
                        <a href="{{ route('units.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-green-50 text-gray-700 hover:text-green-600">
                            <i class="fas fa-sitemap w-6"></i>
                            <span class="sidebar-text">الوحدات</span>
                        </a>
                        
                        <a href="{{ route('departments.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                            <i class="fas fa-layer-group w-6"></i>
                            <span class="sidebar-text">الأقسام</span>
                        </a>
                        
                        <a href="{{ route('organization.projects.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-yellow-50 text-gray-700 hover:text-yellow-600">
                            <i class="fas fa-project-diagram w-6"></i>
                            <span class="sidebar-text">المشاريع</span>
                        </a>
                    </div>
                    <!-- نظام المطور -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="px-3 mb-2">
                            <span class="sidebar-text text-xs font-semibold text-gray-500 uppercase">أدوات التطوير</span>
                        </div>
                        
                        <!-- نظام المطور الرئيسي -->
                        <button onclick="toggleDeveloperMenu()" class="w-full flex items-center justify-between space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gradient-to-r hover:from-purple-500 hover:to-pink-500 text-gray-700 hover:text-white transition-all duration-300">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <i class="fas fa-code w-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                <span class="sidebar-text">نظام المطور</span>
                            </div>
                            <i id="developerMenuIcon" class="fas fa-chevron-down sidebar-text transition-transform duration-300"></i>
                        </button>
                        
                        <!-- القائمة الفرعية لنظام المطور -->
                        <div id="developerMenu" class="hidden mt-2 mr-6 space-y-1">
                            
                            <!-- لوحة التحكم -->
                            <a href="{{ route('developer.index') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 text-gray-600 hover:text-purple-600 transition-all duration-200">
                                <i class="fas fa-tachometer-alt w-5 text-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                <span class="sidebar-text text-sm font-medium">لوحة التحكم</span>
                            </a>
                            
                            <!-- 1. الذكاء الاصطناعي -->
                            <div class="mb-2">
                                <button onclick="toggleSubMenu('ai-menu')" class="w-full flex items-center justify-between space-x-2 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-pink-50 hover:to-purple-50 text-gray-700 transition-all duration-200">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <i class="fas fa-robot w-5 text-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                        <span class="sidebar-text text-sm font-medium">الذكاء الاصطناعي</span>
                                    </div>
                                    <i id="ai-menu-icon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                </button>
                                <div id="ai-menu" class="hidden mr-4 mt-1 space-y-1">
                                    <a href="{{ route('developer.ai.code-generator') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-pink-50 text-gray-600 hover:text-pink-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>مولد الأكواد</span>
                                    </a>
                                    <a href="{{ route('developer.ai.code-refactor') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-purple-50 text-gray-600 hover:text-purple-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>تحسين الكود</span>
                                    </a>
                                    <a href="{{ route('developer.ai.code-review') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-blue-50 text-gray-600 hover:text-blue-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>مراجعة الكود</span>
                                    </a>
                                    <a href="{{ route('developer.ai.bug-detector') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-red-50 text-gray-600 hover:text-red-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>كشف الأخطاء</span>
                                    </a>
                                    <a href="{{ route('developer.ai.documentation-generator') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-green-50 text-gray-600 hover:text-green-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>توليد التوثيق</span>
                                    </a>
                                    <a href="{{ route('developer.ai.test-generator') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-teal-50 text-gray-600 hover:text-teal-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>مولد الاختبارات</span>
                                    </a>
                                    <a href="{{ route('developer.ai.performance-analyzer') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-orange-50 text-gray-600 hover:text-orange-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>تحليل الأداء</span>
                                    </a>
                                    <a href="{{ route('developer.ai.security-scanner') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-red-50 text-gray-600 hover:text-red-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>فحص الأمان</span>
                                    </a>
                                    <a href="{{ route('developer.ai.api-generator') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>مولد API</span>
                                    </a>
                                    <a href="{{ route('developer.ai.database-optimizer') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-cyan-50 text-gray-600 hover:text-cyan-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>محسن قاعدة البيانات</span>
                                    </a>
                                    <a href="{{ route('developer.ai.code-translator') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-yellow-50 text-gray-600 hover:text-yellow-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>مترجم الأكواد</span>
                                    </a>
                                    <a href="{{ route('developer.ai.assistant') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-pink-50 text-gray-600 hover:text-pink-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>المساعد الذكي</span>
                                    </a>
                                    <a href="{{ route('developer.ai.settings') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-gray-50 text-gray-600 hover:text-gray-800 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>إعدادات AI</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- 2. قاعدة البيانات -->
                            <div class="mb-2">
                                <button onclick="toggleSubMenu('database-menu')" class="w-full flex items-center justify-between space-x-2 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 text-gray-700 transition-all duration-200">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <i class="fas fa-database w-5 text-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                        <span class="sidebar-text text-sm font-medium">قاعدة البيانات</span>
                                    </div>
                                    <i id="database-menu-icon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                </button>
                                <div id="database-menu" class="hidden mr-4 mt-1 space-y-1">
                                    <a href="{{ route('developer.migrations') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-blue-50 text-gray-600 hover:text-blue-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>Migrations</span>
                                    </a>
                                    <a href="{{ route('developer.seeders') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-green-50 text-gray-600 hover:text-green-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>Seeders</span>
                                    </a>
                                    <a href="{{ route('developer.database-info') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-cyan-50 text-gray-600 hover:text-cyan-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>معلومات القاعدة</span>
                                    </a>
                                    <a href="{{ route('developer.database-optimize') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-purple-50 text-gray-600 hover:text-purple-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>تحسين القاعدة</span>
                                    </a>
                                    <a href="{{ route('developer.database-backup') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-teal-50 text-gray-600 hover:text-teal-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>نسخ احتياطي</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- 3. أدوات الكود -->
                            <div class="mb-2">
                                <button onclick="toggleSubMenu('code-menu')" class="w-full flex items-center justify-between space-x-2 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-orange-50 hover:to-yellow-50 text-gray-700 transition-all duration-200">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <i class="fas fa-tools w-5 text-sm" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                        <span class="sidebar-text text-sm font-medium">أدوات الكود</span>
                                    </div>
                                    <i id="code-menu-icon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                </button>
                                <div id="code-menu" class="hidden mr-4 mt-1 space-y-1">
                                    <a href="{{ route('developer.cache') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-orange-50 text-gray-600 hover:text-orange-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>مسح Cache</span>
                                    </a>
                                    <a href="{{ route('developer.routes-list') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-purple-50 text-gray-600 hover:text-purple-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>قائمة Routes</span>
                                    </a>
                                    <a href="{{ route('developer.pint') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-cyan-50 text-gray-600 hover:text-cyan-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>تنسيق الكود (Pint)</span>
                                    </a>
                                    <a href="{{ route('developer.tests') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-green-50 text-gray-600 hover:text-green-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>تشغيل الاختبارات</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- 4. المراقبة والتصحيح -->
                            <div class="mb-2">
                                <button onclick="toggleSubMenu('monitoring-menu')" class="w-full flex items-center justify-between space-x-2 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-teal-50 hover:to-green-50 text-gray-700 transition-all duration-200">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <i class="fas fa-search w-5 text-sm" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                        <span class="sidebar-text text-sm font-medium">المراقبة والتصحيح</span>
                                    </div>
                                    <i id="monitoring-menu-icon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                </button>
                                <div id="monitoring-menu" class="hidden mr-4 mt-1 space-y-1">
                                    <a href="/telescope" target="_blank" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-blue-50 text-gray-600 hover:text-blue-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>Telescope</span>
                                    </a>
                                    <a href="{{ route('developer.debugbar') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-purple-50 text-gray-600 hover:text-purple-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>Debugbar</span>
                                    </a>
                                    <a href="/horizon" target="_blank" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-red-50 text-gray-600 hover:text-red-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>Horizon</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- 5. معلومات النظام -->
                            <div class="mb-2">
                                <button onclick="toggleSubMenu('system-menu')" class="w-full flex items-center justify-between space-x-2 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 text-gray-700 transition-all duration-200">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <i class="fas fa-info-circle w-5 text-sm" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                        <span class="sidebar-text text-sm font-medium">معلومات النظام</span>
                                    </div>
                                    <i id="system-menu-icon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                </button>
                                <div id="system-menu" class="hidden mr-4 mt-1 space-y-1">
                                    <a href="{{ route('developer.system-info') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-blue-50 text-gray-600 hover:text-blue-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>معلومات النظام</span>
                                    </a>
                                    <a href="{{ route('developer.server-info') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-orange-50 text-gray-600 hover:text-orange-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>معلومات الخادم</span>
                                    </a>
                                    <a href="{{ route('developer.logs-viewer') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-red-50 text-gray-600 hover:text-red-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>السجلات (Logs)</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- 6. Git والنشر -->
                            <div class="mb-2">
                                <button onclick="toggleSubMenu('git-menu')" class="w-full flex items-center justify-between space-x-2 space-x-reverse p-2 rounded-lg hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 text-gray-700 transition-all duration-200">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <i class="fab fa-git-alt w-5 text-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                        <span class="sidebar-text text-sm font-medium">Git والنشر</span>
                                    </div>
                                    <i id="git-menu-icon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                                </button>
                                <div id="git-menu" class="hidden mr-4 mt-1 space-y-1">
                                    <a href="{{ route('developer.git.dashboard') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-green-50 text-gray-600 hover:text-green-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>لوحة Git</span>
                                    </a>
                                    <a href="{{ route('developer.git.commit') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-emerald-50 text-gray-600 hover:text-emerald-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>Commit & Push</span>
                                    </a>
                                    <a href="{{ route('developer.git.history') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-teal-50 text-gray-600 hover:text-teal-600 transition-colors text-sm">
                                        <i class="far fa-circle text-xs"></i>
                                        <span>سجل التغييرات</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <main id="mainContent" class="flex-1 mr-[280px] transition-all duration-300 p-6">
            @yield('content')
        </main>
    </div>
    
    <!-- Scripts -->
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-collapsed');
            sidebar.classList.toggle('sidebar-expanded');
            
            if (sidebar.classList.contains('sidebar-collapsed')) {
                mainContent.classList.remove('mr-[280px]');
                mainContent.classList.add('mr-[80px]');
                document.querySelectorAll('.sidebar-text').forEach(el => {
                    el.classList.add('hidden');
                });
            } else {
                mainContent.classList.remove('mr-[80px]');
                mainContent.classList.add('mr-[280px]');
                document.querySelectorAll('.sidebar-text').forEach(el => {
                    el.classList.remove('hidden');
                });
            }
        });
        
        // Developer Menu Toggle
        function toggleDeveloperMenu() {
            const menu = document.getElementById('developerMenu');
            const icon = document.getElementById('developerMenuIcon');
            
            if (menu && icon) {
                menu.classList.toggle('hidden');
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            }
        }
        
        // Sub Menu Toggle (للتبويبات الفرعية)
        function toggleSubMenu(menuId) {
            const menu = document.getElementById(menuId);
            const icon = document.getElementById(menuId + '-icon');
            
            if (menu && icon) {
                menu.classList.toggle('hidden');
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            }
        }
        
        // Developer Tools Functions
        async function runMigrations() {
            if(!confirm('هل تريد تشغيل Migrations?')) return;
            
            try {
                const response = await fetch('/developer/migrations/run', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                alert(data.message + '\n\n' + (data.output || ''));
            } catch (error) {
                alert('حدث خطأ: ' + error.message);
            }
        }
        
        async function runSeeders() {
            if(!confirm('هل تريد تشغيل Seeders?')) return;
            
            try {
                const response = await fetch('/developer/seeders/run', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                alert(data.message + '\n\n' + (data.output || ''));
            } catch (error) {
                alert('حدث خطأ: ' + error.message);
            }
        }
        
        function showDatabaseInfo() {
            alert('معلومات قاعدة البيانات:\n\nالنوع: MySQL\nالإصدار: 8.0\nالقاعدة: u306850950_magic_system');
        }
        
        async function optimizeDatabase() {
            if(!confirm('هل تريد تحسين قاعدة البيانات?')) return;
            
            try {
                const response = await fetch('/developer/database/optimize', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                alert(data.message);
            } catch (error) {
                alert('حدث خطأ: ' + error.message);
            }
        }
        
        async function backupDatabase() {
            if(!confirm('هل تريد إنشاء نسخة احتياطية?')) return;
            
            try {
                const response = await fetch('/developer/database/backup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                alert(data.message + (data.file ? '\n\nالملف: ' + data.file : ''));
            } catch (error) {
                alert('حدث خطأ: ' + error.message);
            }
        }
        
        async function clearAllCache() {
            if(!confirm('هل تريد مسح جميع أنواع Cache?')) return;
            
            try {
                const response = await fetch('/developer/cache/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                alert(data.message);
                location.reload();
            } catch (error) {
                alert('حدث خطأ: ' + error.message);
            }
        }
        
        async function runPint() {
            if(!confirm('هل تريد تنسيق الكود باستخدام Pint?')) return;
            
            try {
                const response = await fetch('/developer/pint/run', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                alert(data.message + '\n\n' + (data.output || ''));
            } catch (error) {
                alert('حدث خطأ: ' + error.message);
            }
        }
        
        async function runTests() {
            if(!confirm('هل تريد تشغيل الاختبارات?')) return;
            
            try {
                const response = await fetch('/developer/tests/run', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                alert(data.message + '\n\n' + (data.output || ''));
            } catch (error) {
                alert('حدث خطأ: ' + error.message);
            }
        }
        
        function showRoutes() {
            window.open('/routes-list', '_blank');
        }
        
        function showSystemInfo() {
            alert('معلومات النظام:\n\nPHP: 8.2\nLaravel: 12.40.2\nالإصدار: ' + '{{ config('version.number') }}' + '');
        }
        
        function showLogs() {
            window.open('/logs', '_blank');
        }
        
        // Dropdown functionality for sidebar
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id + '-dropdown');
            const arrow = document.getElementById(id + '-arrow');
            
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                dropdown.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>

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
        }
        
        .sidebar-collapsed {
            width: 80px;
        }
        
        .sidebar-expanded {
            width: 280px;
        }
    </style>
    
    @stack('styles')
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
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-home w-6"></i>
                        <span class="sidebar-text">الرئيسية</span>
                    </a>
                    
                    <a href="{{ route('accounting.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-calculator w-6"></i>
                        <span class="sidebar-text">المحاسبة</span>
                    </a>
                    
                    <a href="{{ route('customers.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-users w-6"></i>
                        <span class="sidebar-text">العملاء</span>
                    </a>
                    
                    <a href="{{ route('inventory.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-box w-6"></i>
                        <span class="sidebar-text">المخزون</span>
                    </a>
                    
                    <a href="{{ route('purchases.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-shopping-cart w-6"></i>
                        <span class="sidebar-text">المشتريات</span>
                    </a>
                    
                    <a href="{{ route('sales.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-chart-line w-6"></i>
                        <span class="sidebar-text">المبيعات</span>
                    </a>
                    
                    <a href="{{ route('projects.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-briefcase w-6"></i>
                        <span class="sidebar-text">المشاريع</span>
                    </a>
                    
                    <a href="{{ route('hr.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-user-tie w-6"></i>
                        <span class="sidebar-text">الموارد البشرية</span>
                    </a>
                    
                    <a href="{{ route('manufacturing.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-industry w-6"></i>
                        <span class="sidebar-text">التصنيع</span>
                    </a>
                    
                    <a href="{{ route('assets.index') }}" class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-purple-50 text-gray-700 hover:text-purple-600">
                        <i class="fas fa-archive w-6"></i>
                        <span class="sidebar-text">الأصول</span>
                    </a>
                    
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
    </script>
    
    @stack('scripts')
</body>
</html>

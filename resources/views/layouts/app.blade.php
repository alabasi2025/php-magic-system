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
    </style>
    
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/dark_mode.css') }}">
    <script src="{{ asset('js/dark_mode.js') }}" defer></script>
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
                    
                    <!-- نظام المطور -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="px-3 mb-2">
                            <span class="sidebar-text text-xs font-semibold text-gray-500 uppercase">أدوات التطوير</span>
                        </div>
                        
                        <!-- نظام المطور الرئيسي -->
                        <button onclick="toggleDeveloperMenu()" class="w-full flex items-center justify-between space-x-3 space-x-reverse p-3 rounded-lg hover:bg-gradient-to-r hover:from-purple-500 hover:to-pink-500 text-gray-700 hover:text-white transition-all duration-300">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <i class="fas fa-code w-6"></i>
                                <span class="sidebar-text">نظام المطور</span>
                            </div>
                            <i id="developerMenuIcon" class="fas fa-chevron-down sidebar-text transition-transform duration-300"></i>
                        </button>
                        
                        <!-- القائمة الفرعية لنظام المطور -->
                        <div id="developerMenu" class="hidden mt-2 mr-6 space-y-1">
                            <!-- أدوات المراقبة والتصحيح -->
                            <div class="mb-3">
                                <span class="sidebar-text text-xs font-semibold text-gray-400 px-3">المراقبة والتصحيح</span>
                                <div class="mt-1 space-y-1">
                                    <a href="/telescope" target="_blank" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-blue-50 text-gray-600 hover:text-blue-600 transition-colors">
                                        <i class="fas fa-telescope w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">Telescope</span>
                                    </a>
                                    <a href="#" onclick="alert('Debugbar يظهر تلقائياً في أسفل الصفحة')" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-green-50 text-gray-600 hover:text-green-600 transition-colors">
                                        <i class="fas fa-bug w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">Debugbar</span>
                                    </a>
                                    <a href="/horizon" target="_blank" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-purple-50 text-gray-600 hover:text-purple-600 transition-colors">
                                        <i class="fas fa-layer-group w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">Horizon</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- أدوات قاعدة البيانات -->
                            <div class="mb-3">
                                <span class="sidebar-text text-xs font-semibold text-gray-400 px-3">قاعدة البيانات</span>
                                <div class="mt-1 space-y-1">
                                    <a href="javascript:void(0)" onclick="runMigrations()" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 transition-colors">
                                        <i class="fas fa-database w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">Migrations</span>
                                    </a>
                                    <a href="#" onclick="runSeeders()" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-yellow-50 text-gray-600 hover:text-yellow-600 transition-colors">
                                        <i class="fas fa-seedling w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">Seeders</span>
                                    </a>
                                    <a href="{{ route('developer.database.info') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-teal-50 text-gray-600 hover:text-teal-600 transition-colors">
                                        <i class="fas fa-info-circle w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">معلومات القاعدة</span>
                                    </a>
                                    <a href="#" onclick="optimizeDatabase()" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-orange-50 text-gray-600 hover:text-orange-600 transition-colors">
                                        <i class="fas fa-bolt w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">تحسين القاعدة</span>
                                    </a>
                                    <a href="#" onclick="backupDatabase()" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-red-50 text-gray-600 hover:text-red-600 transition-colors">
                                        <i class="fas fa-download w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">نسخ احتياطي</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- أدوات الكود -->
                            <div class="mb-3">
                                <span class="sidebar-text text-xs font-semibold text-gray-400 px-3">أدوات الكود</span>
                                <div class="mt-1 space-y-1">
                                    <a href="#" onclick="clearAllCache()" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-pink-50 text-gray-600 hover:text-pink-600 transition-colors">
                                        <i class="fas fa-broom w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">مسح Cache</span>
                                    </a>
                                    <a href="#" onclick="runPint()" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-cyan-50 text-gray-600 hover:text-cyan-600 transition-colors">
                                        <i class="fas fa-magic w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">تنسيق الكود (Pint)</span>
                                    </a>
                                    <a href="#" onclick="runTests()" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-lime-50 text-gray-600 hover:text-lime-600 transition-colors">
                                        <i class="fas fa-vial w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">تشغيل الاختبارات</span>
                                    </a>
                                    <a href="{{ route('developer.routes') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-violet-50 text-gray-600 hover:text-violet-600 transition-colors">
                                        <i class="fas fa-route w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">قائمة Routes</span>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- معلومات النظام -->
                            <div class="mb-3">
                                <span class="sidebar-text text-xs font-semibold text-gray-400 px-3">معلومات النظام</span>
                                <div class="mt-1 space-y-1">
                                    <a href="{{ route('developer.system.info') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-gray-800 transition-colors">
                                        <i class="fas fa-server w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">معلومات الخادم</span>
                                    </a>
                                    <a href="{{ route('developer.logs') }}" class="flex items-center space-x-2 space-x-reverse p-2 rounded-lg hover:bg-red-50 text-gray-600 hover:text-red-600 transition-colors">
                                        <i class="fas fa-file-alt w-5 text-sm"></i>
                                        <span class="sidebar-text text-sm">السجلات (Logs)</span>
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
            alert('معلومات النظام:\n\nPHP: 8.2\nLaravel: 12.40.2\nالإصدار: v2.8.0');
        }
        
        function showLogs() {
            window.open('/logs', '_blank');
        }
    </script>
    
    @stack('scripts')
</body>
</html>

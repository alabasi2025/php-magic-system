<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم نظام العباسي</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Custom styles for RTL and general look */
        body {
            background-color: #f3f4f6; /* Light gray background */
        }
    </style>
</head>
<body>

<div class="min-h-screen bg-gray-100 p-6">
    <header class="bg-white shadow-md rounded-lg p-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-800 text-right">لوحة تحكم نظام العباسي المحاسبي</h1>
    </header>

    <!-- Quick Statistics Section -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Card 1: Intermediate Accounts -->
        <div class="bg-white p-6 rounded-lg shadow-lg border-r-4 border-blue-500">
            <p class="text-sm font-medium text-gray-500">الحسابات الوسيطة</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">15</p>
            <p class="text-sm text-gray-400 mt-2">إجمالي الحسابات النشطة</p>
        </div>

        <!-- Card 2: Funds (الصناديق) -->
        <div class="bg-white p-6 rounded-lg shadow-lg border-r-4 border-green-500">
            <p class="text-sm font-medium text-gray-500">إجمالي الصناديق</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">5</p>
            <p class="text-sm text-gray-400 mt-2">صندوق رئيسي و 4 فرعية</p>
        </div>

        <!-- Card 3: Partners (الشركاء) -->
        <div class="bg-white p-6 rounded-lg shadow-lg border-r-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-500">الشركاء</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">8</p>
            <p class="text-sm text-gray-400 mt-2">شركاء نشطون في النظام</p>
        </div>
    </section>

    <!-- Charts Section -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Chart 1: Balances -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 text-right">أرصدة الحسابات الرئيسية (آخر 6 أشهر)</h2>
            <canvas id="balancesChart"></canvas>
        </div>

        <!-- Chart 2: Transactions -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 text-right">حركة المعاملات (إيرادات ومصروفات)</h2>
            <canvas id="transactionsChart"></canvas>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="bg-white p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 text-right">روابط سريعة</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-right">
            <a href="#" class="block p-4 bg-blue-50 hover:bg-blue-100 rounded-lg text-blue-600 font-medium transition duration-150 ease-in-out">
                <i class="fas fa-users ml-2"></i> إدارة الحسابات
            </a>
            <a href="#" class="block p-4 bg-green-50 hover:bg-green-100 rounded-lg text-green-600 font-medium transition duration-150 ease-in-out">
                <i class="fas fa-plus-circle ml-2"></i> إضافة معاملة جديدة
            </a>
            <a href="#" class="block p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg text-yellow-600 font-medium transition duration-150 ease-in-out">
                <i class="fas fa-file-alt ml-2"></i> تقارير الأرباح والخسائر
            </a>
            <a href="#" class="block p-4 bg-red-50 hover:bg-red-100 rounded-lg text-red-600 font-medium transition duration-150 ease-in-out">
                <i class="fas fa-cog ml-2"></i> إعدادات النظام
            </a>
        </div>
    </section>

    <!-- Alpine.js Example (Dropdown for user profile) -->
    <div x-data="{ open: false }" class="relative inline-block text-right float-left">
        <button @click="open = !open" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500">
            المستخدم (Alpine.js)
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <div x-show="open" @click.outside="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 text-right" role="menuitem">الملف الشخصي</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 text-right" role="menuitem">تسجيل الخروج</a>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js Initialization Script -->
<script>
    // Balances Chart Data
    const balancesCtx = document.getElementById('balancesChart').getContext('2d');
    new Chart(balancesCtx, {
        type: 'line',
        data: {
            labels: ['يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر'],
            datasets: [{
                label: 'إجمالي الأرصدة',
                data: [12000, 19000, 15000, 22000, 28000, 35000],
                borderColor: 'rgb(59, 130, 246)', // blue-500
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    rtl: true,
                    textDirection: 'rtl'
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('ar-EG') + ' ر.س';
                        }
                    }
                },
                x: {
                    reverse: true, // For RTL
                }
            }
        }
    });

    // Transactions Chart Data
    const transactionsCtx = document.getElementById('transactionsChart').getContext('2d');
    new Chart(transactionsCtx, {
        type: 'bar',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [
                {
                    label: 'الإيرادات',
                    data: [1500, 2000, 1800, 2500, 3000, 2200],
                    backgroundColor: 'rgb(16, 185, 129)', // green-500
                },
                {
                    label: 'المصروفات',
                    data: [800, 1200, 900, 1500, 1000, 1300],
                    backgroundColor: 'rgb(239, 68, 68)', // red-500
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    rtl: true,
                    textDirection: 'rtl'
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('ar-EG');
                        }
                    }
                },
                x: {
                    reverse: true, // For RTL
                }
            }
        }
    });
</script>

</body>
</html>

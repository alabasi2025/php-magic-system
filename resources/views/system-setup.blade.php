<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Setup - إعداد النظام</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-tools text-blue-600"></i>
                    إعداد النظام - System Setup
                </h1>
                <p class="text-gray-600">صفحة إعداد وتشخيص نظام المخازن</p>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-heartbeat text-green-600"></i>
                    حالة النظام
                </h2>
                
                <!-- Database Connection -->
                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2">قاعدة البيانات</h3>
                    <div class="flex items-center">
                        @if($status['database'])
                            <i class="fas fa-check-circle text-green-500 text-2xl ml-2"></i>
                            <span class="text-green-600">متصل بنجاح</span>
                        @else
                            <i class="fas fa-times-circle text-red-500 text-2xl ml-2"></i>
                            <span class="text-red-600">فشل الاتصال: {{ $status['database_error'] ?? '' }}</span>
                        @endif
                    </div>
                </div>

                <!-- Tables Status -->
                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2">الجداول</h3>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($status['tables'] as $table => $exists)
                            <div class="flex items-center">
                                @if($exists)
                                    <i class="fas fa-check-circle text-green-500 ml-2"></i>
                                    <span class="text-green-600">{{ $table }}</span>
                                @else
                                    <i class="fas fa-times-circle text-red-500 ml-2"></i>
                                    <span class="text-red-600">{{ $table }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Migrations -->
                <div class="mb-4">
                    <h3 class="font-semibold text-lg mb-2">Migrations</h3>
                    <p class="text-gray-600">عدد migrations نظام المخازن: <strong>{{ $status['migrations_count'] ?? 0 }}</strong></p>
                    @if(isset($status['migrations']) && count($status['migrations']) > 0)
                        <ul class="list-disc list-inside text-sm text-gray-600 mt-2">
                            @foreach($status['migrations'] as $migration)
                                <li>{{ $migration }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Data Count -->
                @if(isset($status['warehouses_count']))
                    <div class="mb-4">
                        <h3 class="font-semibold text-lg mb-2">البيانات</h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded">
                                <p class="text-sm text-gray-600">المخازن</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $status['warehouses_count'] }}</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded">
                                <p class="text-sm text-gray-600">الأصناف</p>
                                <p class="text-2xl font-bold text-green-600">{{ $status['items_count'] }}</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded">
                                <p class="text-sm text-gray-600">الحركات</p>
                                <p class="text-2xl font-bold text-purple-600">{{ $status['movements_count'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-cogs text-purple-600"></i>
                    الإجراءات
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Run Migrations -->
                    <button onclick="runMigrations()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg transition duration-300">
                        <i class="fas fa-database text-2xl mb-2"></i>
                        <p>تشغيل Migrations</p>
                    </button>

                    <!-- Run Seeders -->
                    <button onclick="runSeeders()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition duration-300">
                        <i class="fas fa-seedling text-2xl mb-2"></i>
                        <p>تشغيل Seeders</p>
                    </button>

                    <!-- Clear Cache -->
                    <button onclick="clearCache()" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 px-6 rounded-lg transition duration-300">
                        <i class="fas fa-broom text-2xl mb-2"></i>
                        <p>مسح Cache</p>
                    </button>
                </div>

                <!-- Output -->
                <div id="output" class="mt-6 hidden">
                    <h3 class="font-semibold text-lg mb-2">النتيجة</h3>
                    <div id="output-content" class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm overflow-x-auto"></div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-link text-indigo-600"></i>
                    روابط سريعة
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="/inventory/dashboard" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-3 px-4 rounded-lg text-center hover:from-blue-600 hover:to-blue-700 transition duration-300">
                        <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                    </a>
                    <a href="/inventory/warehouses" class="bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-3 px-4 rounded-lg text-center hover:from-green-600 hover:to-green-700 transition duration-300">
                        <i class="fas fa-warehouse"></i> المخازن
                    </a>
                    <a href="/inventory/items" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white font-bold py-3 px-4 rounded-lg text-center hover:from-purple-600 hover:to-purple-700 transition duration-300">
                        <i class="fas fa-boxes"></i> الأصناف
                    </a>
                    <a href="/inventory/stock-movements" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold py-3 px-4 rounded-lg text-center hover:from-orange-600 hover:to-orange-700 transition duration-300">
                        <i class="fas fa-exchange-alt"></i> الحركات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showOutput(message, isError = false) {
            const output = document.getElementById('output');
            const outputContent = document.getElementById('output-content');
            output.classList.remove('hidden');
            outputContent.innerHTML = message;
            outputContent.className = isError 
                ? 'bg-red-800 text-white p-4 rounded font-mono text-sm overflow-x-auto'
                : 'bg-gray-800 text-green-400 p-4 rounded font-mono text-sm overflow-x-auto';
        }

        async function runMigrations() {
            showOutput('جاري تشغيل Migrations...');
            try {
                const response = await fetch('/system-setup/run-migrations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showOutput('✅ تم تشغيل Migrations بنجاح!\n\n' + data.output);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showOutput('❌ خطأ: ' + data.error, true);
                }
            } catch (error) {
                showOutput('❌ خطأ: ' + error.message, true);
            }
        }

        async function runSeeders() {
            showOutput('جاري تشغيل Seeders...');
            try {
                const response = await fetch('/system-setup/run-seeders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showOutput('✅ تم تشغيل Seeders بنجاح!\n\n' + data.output);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showOutput('❌ خطأ: ' + data.error, true);
                }
            } catch (error) {
                showOutput('❌ خطأ: ' + error.message, true);
            }
        }

        async function clearCache() {
            showOutput('جاري مسح Cache...');
            try {
                const response = await fetch('/system-setup/clear-cache', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showOutput('✅ تم مسح Cache بنجاح!');
                } else {
                    showOutput('❌ خطأ: ' + data.error, true);
                }
            } catch (error) {
                showOutput('❌ خطأ: ' + error.message, true);
            }
        }
    </script>
</body>
</html>

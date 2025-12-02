@extends('layouts.app')

@section('title', 'قائمة Routes - نظام المطور')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-violet-900 to-slate-900 pt-20 pb-10">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-route text-violet-400 mr-3"></i>قائمة المسارات (Routes)
                </h1>
                <p class="text-gray-400">عرض جميع مسارات التطبيق المسجلة</p>
            </div>
            <a href="{{ route('developer.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">إجمالي المسارات</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $total }}</p>
                </div>
                <i class="fas fa-map-signs text-violet-400 text-5xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg p-6 border border-white/20">
            <div class="flex gap-4">
                <input type="text" id="searchRoutes" placeholder="ابحث في المسارات..." class="flex-1 px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-violet-500">
                <button onclick="window.location.reload()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-sync-alt mr-2"></i>تحديث
                </button>
            </div>
        </div>
    </div>

    <!-- Routes Table -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/10 backdrop-blur-md rounded-lg border border-white/20 overflow-hidden">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-list text-violet-400 mr-2"></i>جميع المسارات ({{ count($routes) }})
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" id="routesTable">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الطريقة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">المسار</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">الإجراء</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Middleware</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($routes as $route)
                        <tr class="hover:bg-white/5 transition route-row">
                            <td class="px-6 py-4">
                                @foreach(explode('|', $route['method']) as $method)
                                    <span class="inline-block px-2 py-1 rounded text-xs mr-1 mb-1
                                        @if($method == 'GET') bg-green-500/20 text-green-400
                                        @elseif($method == 'POST') bg-blue-500/20 text-blue-400
                                        @elseif($method == 'PUT' || $method == 'PATCH') bg-yellow-500/20 text-yellow-400
                                        @elseif($method == 'DELETE') bg-red-500/20 text-red-400
                                        @else bg-gray-500/20 text-gray-400
                                        @endif">
                                        {{ $method }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-white font-mono text-sm">{{ $route['uri'] }}</code>
                            </td>
                            <td class="px-6 py-4">
                                @if($route['name'])
                                    <span class="text-violet-400 font-mono text-sm">{{ $route['name'] }}</span>
                                @else
                                    <span class="text-gray-500 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-gray-300 text-xs">{{ $route['action'] }}</code>
                            </td>
                            <td class="px-6 py-4">
                                @if($route['middleware'])
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(explode(', ', $route['middleware']) as $middleware)
                                            <span class="px-2 py-1 bg-purple-500/20 text-purple-400 rounded text-xs">{{ $middleware }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-500 text-sm">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-6">
            <h3 class="text-white font-semibold mb-2">
                <i class="fas fa-info-circle text-blue-400 mr-2"></i>معلومات مفيدة
            </h3>
            <p class="text-gray-400">
                يمكنك استخدام البحث للعثور على مسارات محددة. 
                الألوان تشير إلى نوع HTTP method: أخضر (GET)، أزرق (POST)، أصفر (PUT/PATCH)، أحمر (DELETE).
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('searchRoutes').addEventListener('keyup', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.route-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endsection

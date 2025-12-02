@extends('layouts.app')

@section('content')
    {{-- Tailwind CSS structure for the reports page --}}
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800 text-right">تقارير الصناديق</h1>

        {{-- Filters Section --}}
        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 text-right">فلاتر التقرير</h2>
            <form id="report-filters" class="grid grid-cols-1 md:grid-cols-3 gap-4" dir="rtl">
                {{-- Date Filter (Placeholder for a date range picker) --}}
                <div>
                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1 text-right">نطاق التاريخ</label>
                    <input type="text" id="date_range" name="date_range" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right" placeholder="اختر نطاق التاريخ">
                </div>

                {{-- Cash Box Filter (Placeholder for dynamic data) --}}
                <div>
                    <label for="cash_box" class="block text-sm font-medium text-gray-700 mb-1 text-right">الصندوق</label>
                    <select id="cash_box" name="cash_box" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right">
                        <option value="">جميع الصناديق</option>
                        <option value="1">الصندوق الرئيسي</option>
                        <option value="2">صندوق الفرع أ</option>
                        {{-- @foreach ($cashBoxes as $box)
                            <option value="{{ $box->id }}">{{ $box->name }}</option>
                        @endforeach --}}
                    </select>
                </div>

                {{-- Apply Button --}}
                <div class="flex items-end">
                    <button type="submit" class="w-full md:w-auto px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        تطبيق الفلاتر
                    </button>
                </div>
            </form>
        </div>

        {{-- Report Tabs/Sections --}}
        <div x-data="{ activeTab: 'daily' }" class="bg-white shadow-lg rounded-lg p-6">
            {{-- Tabs Navigation --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8 space-x-reverse" dir="rtl">
                    <a href="#" @click.prevent="activeTab = 'daily'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'daily', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'daily' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out">
                        التقرير اليومي
                    </a>
                    <a href="#" @click.prevent="activeTab = 'monthly'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'monthly', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'monthly' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out">
                        التقرير الشهري
                    </a>
                    <a href="#" @click.prevent="activeTab = 'summary'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'summary', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'summary' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out">
                        ملخص التقارير
                    </a>
                </nav>
            </div>

            {{-- Daily Report Content --}}
            <div x-show="activeTab === 'daily'" x-cloak>
                <h3 class="text-2xl font-semibold mb-4 text-gray-800 text-right">التقرير اليومي</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>

            {{-- Monthly Report Content --}}
            <div x-show="activeTab === 'monthly'" x-cloak>
                <h3 class="text-2xl font-semibold mb-4 text-gray-800 text-right">التقرير الشهري</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            {{-- Summary Report Content --}}
            <div x-show="activeTab === 'summary'" x-cloak>
                <h3 class="text-2xl font-semibold mb-4 text-gray-800 text-right">ملخص التقارير</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <canvas id="summaryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    {{-- Alpine.js for tab switching (assuming it's included in layouts.app or will be added) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Placeholder data for charts
            const dailyData = {
                labels: ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
                datasets: [{
                    label: 'إجمالي الإيرادات اليومية',
                    data: [1200, 1900, 3000, 5000, 2000, 3000, 4500],
                    backgroundColor: 'rgba(79, 70, 229, 0.5)', // indigo-600 with opacity
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1
                }]
            };

            const monthlyData = {
                labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
                datasets: [{
                    label: 'إجمالي الإيرادات الشهرية',
                    data: [15000, 22000, 35000, 48000, 30000, 40000, 55000, 60000, 45000, 52000, 65000, 70000],
                    backgroundColor: 'rgba(16, 185, 129, 0.5)', // emerald-500 with opacity
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
                }]
            };

            const summaryData = {
                labels: ['إيرادات', 'مصروفات', 'صافي'],
                datasets: [{
                    label: 'ملخص الصندوق',
                    data: [500000, 150000, 350000],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.5)', // blue-500
                        'rgba(239, 68, 68, 0.5)', // red-500
                        'rgba(34, 197, 94, 0.5)' // green-500
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(34, 197, 94, 1)'
                    ],
                    borderWidth: 1
                }]
            };

            // Chart Initialization Function
            function initChart(chartId, type, data, options = {}) {
                const ctx = document.getElementById(chartId);
                if (ctx) {
                    new Chart(ctx, {
                        type: type,
                        data: data,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    rtl: true, // Enable RTL for legend
                                    labels: {
                                        font: {
                                            family: 'Tajawal, sans-serif' // Assuming a suitable Arabic font
                                        }
                                    }
                                },
                                title: {
                                    display: false,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString('ar-EG', { style: 'currency', currency: 'SAR' });
                                        }
                                    }
                                }
                            },
                            ...options
                        }
                    });
                }
            }

            // Initialize Charts
            initChart('dailyChart', 'bar', dailyData);
            initChart('monthlyChart', 'line', monthlyData);
            initChart('summaryChart', 'pie', summaryData, { scales: { y: { display: false } } }); // Pie chart doesn't need Y-axis

            // Filter Form Submission Handler (Placeholder for AJAX call)
            document.getElementById('report-filters').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const dateRange = formData.get('date_range');
                const cashBox = formData.get('cash_box');

                console.log('Filtering reports with:', { dateRange, cashBox });

                // TODO: Implement AJAX call to fetch new data and update charts
                // Example:
                // fetch('/api/reports', { method: 'POST', body: formData })
                //     .then(response => response.json())
                //     .then(data => {
                //         // Update charts with new data
                //     });
            });
        });
    </script>
@endpush

{{-- Note: This file assumes that 'layouts.app' includes Tailwind CSS and a place for scripts via @stack('scripts'). --}}

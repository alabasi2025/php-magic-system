@extends('layouts.app')

{{-- تعليق: هذا الملف هو واجهة التخطيط والميزانية لـ BUDGET_PLANNING --}}
{{-- يعرض تحليلات ورسوم بيانية للمقارنة بين الميزانية المخططة والميزانية الفعلية --}}
{{-- يفترض أن البيانات ($plannedBudgetTotal, $actualExpensesTotal, $budgetCategories, $budgetChartData) يتم تمريرها من المتحكم --}}
{{-- يفترض أن التخطيط الأساسي (layouts.app) يتضمن Tailwind CSS و Chart.js --}}

@section('title', 'تخطيط وتحليل الميزانية')

@section('content')
    {{-- حاوية رئيسية بتنسيق Tailwind CSS --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- عنوان الصفحة --}}
        <h1 class="text-3xl font-extrabold text-gray-900 mb-6 border-b-2 border-indigo-500 pb-2 text-right">
            {{-- تعليق: عنوان الواجهة --}}
            تخطيط وتحليل الميزانية
        </h1>

        {{-- قسم الإحصائيات الرئيسية (KPIs) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- تعليق: بطاقة إجمالي الميزانية المخططة --}}
            <div class="bg-white overflow-hidden shadow-lg rounded-xl p-6 transition duration-300 hover:shadow-2xl">
                <p class="text-sm font-medium text-gray-500 truncate text-right">إجمالي الميزانية المخططة</p>
                <p class="mt-1 text-4xl font-bold text-indigo-600 text-right">
                    {{-- افتراض متغير من المتحكم --}}
                    {{ number_format($plannedBudgetTotal ?? 0, 2) }} <span class="text-xl font-normal">ريال</span>
                </p>
            </div>

            {{-- تعليق: بطاقة إجمالي المصروفات الفعلية (قد تأتي من CASH_BOXES) --}}
            <div class="bg-white overflow-hidden shadow-lg rounded-xl p-6 transition duration-300 hover:shadow-2xl">
                <p class="text-sm font-medium text-gray-500 truncate text-right">إجمالي المصروفات الفعلية</p>
                <p class="mt-1 text-4xl font-bold text-red-600 text-right">
                    {{-- افتراض متغير من المتحكم --}}
                    {{ number_format($actualExpensesTotal ?? 0, 2) }} <span class="text-xl font-normal">ريال</span>
                </p>
            </div>

            {{-- تعليق: بطاقة الفرق (المتبقي/العجز) --}}
            <div class="bg-white overflow-hidden shadow-lg rounded-xl p-6 transition duration-300 hover:shadow-2xl">
                <p class="text-sm font-medium text-gray-500 truncate text-right">الفرق (المتبقي/العجز)</p>
                @php
                    $difference = ($plannedBudgetTotal ?? 0) - ($actualExpensesTotal ?? 0);
                    $color = $difference >= 0 ? 'text-green-600' : 'text-red-600';
                @endphp
                <p class="mt-1 text-4xl font-bold {{ $color }} text-right">
                    {{ number_format(abs($difference), 2) }} <span class="text-xl font-normal">ريال</span>
                </p>
            </div>

            {{-- تعليق: بطاقة نسبة الإنجاز (قد تستخدم بيانات من INTERMEDIATE_ACCOUNTS) --}}
            <div class="bg-white overflow-hidden shadow-lg rounded-xl p-6 transition duration-300 hover:shadow-2xl">
                <p class="text-sm font-medium text-gray-500 truncate text-right">نسبة الإنجاز</p>
                @php
                    $completionRate = ($plannedBudgetTotal > 0) ? (($actualExpensesTotal ?? 0) / $plannedBudgetTotal) * 100 : 0;
                    $rateColor = $completionRate <= 100 ? 'text-blue-600' : 'text-orange-600';
                @endphp
                <p class="mt-1 text-4xl font-bold {{ $rateColor }} text-right">
                    {{ number_format($completionRate, 1) }}%
                </p>
            </div>
        </div>

        {{-- قسم الرسوم البيانية والتحليل --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- تعليق: الرسم البياني الرئيسي (مقارنة الميزانية المخططة مقابل الفعلية) --}}
            <div class="lg:col-span-2 bg-white shadow-lg rounded-xl p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 text-right">مقارنة الميزانية المخططة والفعلية</h2>
                <div class="h-96">
                    <canvas id="budgetComparisonChart"></canvas>
                </div>
            </div>

            {{-- تعليق: جدول/ملخص فئات الميزانية (قد يستخدم بيانات من PARTNER_ACCOUNTING لبعض الفئات) --}}
            <div class="lg:col-span-1 bg-white shadow-lg rounded-xl p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 text-right">ملخص فئات الميزانية</h2>
                <div class="overflow-y-auto max-h-96">
                    <table class="min-w-full divide-y divide-gray-200 text-right">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">الفئة</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">المخطط</th>
                                <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">الفعلي</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- حلقة تكرارية لبيانات الفئات --}}
                            @forelse ($budgetCategories ?? [] as $category)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category['name'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ number_format($category['planned'], 2) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ number_format($category['actual'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-center text-sm text-gray-500">لا توجد فئات ميزانية متاحة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- تعليق: قسم خاص بالـ JavaScript لتشغيل Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        // تعليق: تهيئة بيانات الرسم البياني من متغير Blade
        const chartData = @json($budgetChartData ?? [
            'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            'planned' => [12000, 19000, 3000, 5000, 2000, 3000],
            'actual' => [10000, 15000, 2500, 4500, 1800, 2800],
        ]);

        // تعليق: إعداد الرسم البياني للمقارنة
        const ctx = document.getElementById('budgetComparisonChart').getContext('2d');
        new Chart(ctx, {
            type: 'line', // نوع الرسم البياني: خطي
            data: {
                labels: chartData.labels, // تسميات المحور السيني (الأشهر/الفترات)
                datasets: [
                    {
                        label: 'الميزانية المخططة',
                        data: chartData.planned,
                        borderColor: 'rgb(79, 70, 229)', // لون أزرق/بنفسجي لـ Indigo-600
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'المصروفات الفعلية',
                        data: chartData.actual,
                        borderColor: 'rgb(220, 38, 38)', // لون أحمر لـ Red-600
                        backgroundColor: 'rgba(220, 38, 38, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // للسماح بالتحكم في الارتفاع عبر Tailwind
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                family: 'Tajawal, sans-serif' // افتراض خط عربي
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
                        title: {
                            display: true,
                            text: 'المبلغ (ريال)',
                            font: {
                                family: 'Tajawal, sans-serif'
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'الفترة الزمنية',
                            font: {
                                family: 'Tajawal, sans-serif'
                            }
                        }
                    }
                },
                // تعليق: دعم اللغة العربية في التلميحات (Tooltips)
                tooltips: {
                    rtl: true,
                    bodyFontFamily: 'Tajawal, sans-serif',
                    titleFontFamily: 'Tajawal, sans-serif',
                }
            }
        });
    </script>
@endpush

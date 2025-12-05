@extends('layouts.app')

@section('title', 'لوحة تحكم المخازن')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">لوحة تحكم المخازن</h1>

    {{-- رسائل التنبيهات (معالجة الأخطاء) --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- 1. الإحصائيات السريعة (Quick Stats) --}}
    <div class="row">
        {{-- إجمالي المنتجات --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي المنتجات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($quickStats['total_products']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- إجمالي المخازن --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                المخازن النشطة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($quickStats['total_warehouses']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- حركات الإدخال الشهرية --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                إدخال الشهر الحالي
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ number_format($quickStats['in_movements_this_month']) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-circle-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- حركات الإخراج الشهرية --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                إخراج الشهر الحالي
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($quickStats['out_movements_this_month']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-circle-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 2. الرسوم البيانية (Stock Movements Chart) --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">حركات المخزون (آخر 30 يوماً)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="stockMovementChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. التنبيهات النشطة (Active Alerts) --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">التنبيهات النشطة</h6>
                </div>
                <div class="card-body">
                    @forelse ($alerts as $alert)
                        <div class="mb-3">
                            <div class="font-weight-bold text-{{ $alert->type == 'low_stock' ? 'warning' : 'danger' }}">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                {{ $alert->message }}
                            </div>
                            <span class="text-muted small">{{ $alert->created_at->diffForHumans() }}</span>
                        </div>
                        @if (!$loop->last)
                            <hr class="sidebar-divider my-0">
                        @endif
                    @empty
                        <p class="text-center text-success">لا توجد تنبيهات نشطة حالياً. <i class="fas fa-check-circle"></i></p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- 4. آخر الحركات (Recent Movements) --}}
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">آخر 10 حركات مخزون</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>المنتج</th>
                                    <th>النوع</th>
                                    <th>الكمية</th>
                                    <th>من مخزن</th>
                                    <th>إلى مخزن</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentMovements as $movement)
                                    <tr>
                                        <td>{{ $movement->movement_date->format('Y-m-d H:i') }}</td>
                                        <td>{{ $movement->product->name }}</td>
                                        <td>
                                            @if ($movement->type == 'in')
                                                <span class="badge badge-success">إدخال</span>
                                            @elseif ($movement->type == 'out')
                                                <span class="badge badge-danger">إخراج</span>
                                            @else
                                                <span class="badge badge-info">تحويل</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($movement->quantity) }}</td>
                                        <td>{{ $movement->fromWarehouse->name }}</td>
                                        <td>{{ $movement->toWarehouse ? $movement->toWarehouse->name : '-' }}</td>
                                        <td>{{ $movement->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- تضمين مكتبة Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

<script>
    // تهيئة بيانات الرسم البياني من المتحكم
    var chartData = @json($chartData);

    // إعداد الرسم البياني
    var ctx = document.getElementById("stockMovementChart");
    var stockMovementChart = new Chart(ctx, {
        type: 'bar', // يمكن تغييرها إلى 'line' حسب التفضيل
        data: {
            labels: chartData.labels,
            datasets: chartData.datasets
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'التاريخ'
                    }
                },
                y: {
                    stacked: false,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'الكمية'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Tajawal, sans-serif' // افتراض خط عربي
                        }
                    }
                },
                tooltip: {
                    rtl: true, // دعم اللغة العربية في التلميحات
                    callbacks: {
                        title: function(context) {
                            return 'التاريخ: ' + context[0].label;
                        },
                        label: function(context) {
                            var label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('ar-EG').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush

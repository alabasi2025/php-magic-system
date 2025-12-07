@extends('layouts.app')

@section('title', 'تقرير أداء الموردين')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        تقرير أداء الموردين
                    </h4>
                    <div>
                        <button class="btn btn-light btn-sm me-2" onclick="window.print()">
                            <i class="fas fa-print"></i> طباعة
                        </button>
                        <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                            <i class="fas fa-file-excel"></i> تصدير Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="GET" action="{{ route('purchases.reports.supplier-performance') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="supplier_id" class="form-label">المورد</label>
                            <select class="form-select" id="supplier_id" name="supplier_id">
                                <option value="">جميع الموردين</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="rating_filter" class="form-label">التقييم</label>
                            <select class="form-select" id="rating_filter" name="rating_filter">
                                <option value="">جميع التقييمات</option>
                                <option value="excellent" {{ request('rating_filter') == 'excellent' ? 'selected' : '' }}>ممتاز (4.5+)</option>
                                <option value="good" {{ request('rating_filter') == 'good' ? 'selected' : '' }}>جيد (3.5-4.5)</option>
                                <option value="average" {{ request('rating_filter') == 'average' ? 'selected' : '' }}>متوسط (2.5-3.5)</option>
                                <option value="poor" {{ request('rating_filter') == 'poor' ? 'selected' : '' }}>ضعيف (أقل من 2.5)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-dark">
                                <i class="fas fa-filter"></i> تصفية
                            </button>
                            <a href="{{ route('purchases.reports.supplier-performance') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> إعادة تعيين
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إجمالي الموردين</h6>
                            <h3 class="mb-0">{{ $statistics['total_suppliers'] ?? 0 }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">متوسط التقييم</h6>
                            <h3 class="mb-0">
                                {{ number_format($statistics['avg_rating'] ?? 0, 1) }}
                                <i class="fas fa-star text-warning"></i>
                            </h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">معدل التسليم في الوقت</h6>
                            <h3 class="mb-0">{{ number_format($statistics['on_time_delivery_rate'] ?? 0, 1) }}%</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-shipping-fast fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">معدل جودة المنتجات</h6>
                            <h3 class="mb-0">{{ number_format($statistics['quality_rate'] ?? 0, 1) }}%</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-certificate fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-radar me-2"></i>
                        مقارنة أداء الموردين
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceRadarChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        معدل التسليم في الوقت المحدد
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="deliveryChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-top border-success border-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy text-warning me-2"></i>
                        أفضل الموردين
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($topPerformers ?? [] as $index => $supplier)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-warning rounded-pill me-2">{{ $index + 1 }}</span>
                                <strong>{{ $supplier->name }}</strong>
                            </div>
                            <span class="badge bg-success rounded-pill">
                                {{ number_format($supplier->performance_score, 1) }}
                                <i class="fas fa-star"></i>
                            </span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">
                            لا توجد بيانات
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-top border-info border-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-shipping-fast text-info me-2"></i>
                        أسرع تسليم
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($fastestDelivery ?? [] as $index => $supplier)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-info rounded-pill me-2">{{ $index + 1 }}</span>
                                <strong>{{ $supplier->name }}</strong>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                {{ number_format($supplier->avg_delivery_days, 1) }} يوم
                            </span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">
                            لا توجد بيانات
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-top border-danger border-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        يحتاج تحسين
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($needsImprovement ?? [] as $index => $supplier)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-danger rounded-pill me-2">{{ $index + 1 }}</span>
                                <strong>{{ $supplier->name }}</strong>
                            </div>
                            <span class="badge bg-danger rounded-pill">
                                {{ number_format($supplier->performance_score, 1) }}
                                <i class="fas fa-star"></i>
                            </span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">
                            لا توجد بيانات
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Performance Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        تفاصيل أداء الموردين
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>المورد</th>
                                    <th>التقييم العام</th>
                                    <th>عدد الأوامر</th>
                                    <th>معدل التسليم في الوقت</th>
                                    <th>متوسط وقت التسليم</th>
                                    <th>معدل الجودة</th>
                                    <th>معدل الإرجاع</th>
                                    <th>الاستجابة</th>
                                    <th>التقييم النهائي</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($performanceData ?? [] as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $data->supplier_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $data->supplier_code ?? '' }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($data->overall_rating))
                                                    <i class="fas fa-star text-warning"></i>
                                                @elseif($i - 0.5 <= $data->overall_rating)
                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-warning"></i>
                                                @endif
                                            @endfor
                                            <span class="ms-2">{{ number_format($data->overall_rating, 1) }}</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-primary">{{ $data->total_orders }}</span></td>
                                    <td>
                                        <div class="progress" style="height: 20px; min-width: 80px;">
                                            <div class="progress-bar {{ $data->on_time_rate >= 80 ? 'bg-success' : ($data->on_time_rate >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $data->on_time_rate }}%;" 
                                                 aria-valuenow="{{ $data->on_time_rate }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ number_format($data->on_time_rate, 0) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($data->avg_delivery_days, 1) }} يوم</td>
                                    <td>
                                        <span class="badge {{ $data->quality_rate >= 90 ? 'bg-success' : ($data->quality_rate >= 70 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($data->quality_rate, 0) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $data->return_rate <= 5 ? 'bg-success' : ($data->return_rate <= 10 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($data->return_rate, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $data->responsiveness_rating)
                                                <i class="fas fa-circle text-success" style="font-size: 8px;"></i>
                                            @else
                                                <i class="far fa-circle text-muted" style="font-size: 8px;"></i>
                                            @endif
                                        @endfor
                                    </td>
                                    <td>
                                        @if($data->performance_score >= 4.5)
                                            <span class="badge bg-success">ممتاز</span>
                                        @elseif($data->performance_score >= 3.5)
                                            <span class="badge bg-info">جيد</span>
                                        @elseif($data->performance_score >= 2.5)
                                            <span class="badge bg-warning">متوسط</span>
                                        @else
                                            <span class="badge bg-danger">ضعيف</span>
                                        @endif
                                        <br>
                                        <small>{{ number_format($data->performance_score, 2) }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('purchases.suppliers.show', $data->supplier_id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-primary" 
                                                onclick="showPerformanceDetails({{ $data->supplier_id }})" 
                                                title="التفاصيل">
                                            <i class="fas fa-chart-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد بيانات</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(isset($performanceData) && method_exists($performanceData, 'hasPages') && $performanceData->hasPages())
                <div class="card-footer">
                    {{ $performanceData->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Performance Radar Chart
const radarCtx = document.getElementById('performanceRadarChart');
if (radarCtx) {
    new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: ['التسليم في الوقت', 'الجودة', 'السعر', 'الاستجابة', 'الموثوقية'],
            datasets: {!! json_encode(array_map(function($supplier) {
                return [
                    'label' => $supplier->supplier_name ?? '',
                    'data' => [
                        $supplier->on_time_rate ?? 0,
                        $supplier->quality_rate ?? 0,
                        $supplier->price_rating ?? 0,
                        $supplier->responsiveness_rating * 20 ?? 0,
                        $supplier->reliability_rating ?? 0
                    ],
                    'borderWidth' => 2
                ];
            }, array_slice($performanceData->toArray() ?? [], 0, 5))) !!}
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

// Delivery Chart
const deliveryCtx = document.getElementById('deliveryChart');
if (deliveryCtx) {
    new Chart(deliveryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($performanceData->toArray() ?? [], 'supplier_name')) !!},
            datasets: [{
                label: 'معدل التسليم في الوقت (%)',
                data: {!! json_encode(array_column($performanceData->toArray() ?? [], 'on_time_rate')) !!},
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

function exportToExcel() {
    alert('جاري تصدير البيانات إلى Excel...');
}

function showPerformanceDetails(supplierId) {
    // Show detailed performance modal or redirect to details page
    window.location.href = `/purchases/suppliers/${supplierId}/performance`;
}
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'تقرير المشتريات حسب الصنف')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        تقرير المشتريات حسب الصنف
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
                    <form method="GET" action="{{ route('purchases.reports.by-item') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="category_id" class="form-label">الفئة</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">جميع الفئات</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="item_id" class="form-label">الصنف</label>
                            <select class="form-select" id="item_id" name="item_id">
                                <option value="">جميع الأصناف</option>
                                @foreach($items ?? [] as $item)
                                    <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sort_by" class="form-label">ترتيب حسب</label>
                            <select class="form-select" id="sort_by" name="sort_by">
                                <option value="total_amount" {{ request('sort_by') == 'total_amount' ? 'selected' : '' }}>إجمالي المبلغ</option>
                                <option value="total_quantity" {{ request('sort_by') == 'total_quantity' ? 'selected' : '' }}>الكمية</option>
                                <option value="item_name" {{ request('sort_by') == 'item_name' ? 'selected' : '' }}>اسم الصنف</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-info text-white">
                                <i class="fas fa-filter"></i> تصفية
                            </button>
                            <a href="{{ route('purchases.reports.by-item') }}" class="btn btn-secondary">
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
            <div class="card shadow-sm border-0 border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">عدد الأصناف</h6>
                            <h3 class="mb-0">{{ $statistics['total_items'] ?? 0 }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إجمالي الكمية</h6>
                            <h3 class="mb-0">{{ number_format($statistics['total_quantity'] ?? 0) }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-cubes fa-2x"></i>
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
                            <h6 class="text-muted mb-2">إجمالي المبلغ</h6>
                            <h3 class="mb-0">{{ number_format($statistics['total_amount'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-dollar-sign fa-2x"></i>
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
                            <h6 class="text-muted mb-2">متوسط السعر</h6>
                            <h3 class="mb-0">{{ number_format($statistics['avg_price'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-tag fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        أكثر الأصناف شراءً (حسب الكمية)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="quantityChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        أكثر الأصناف شراءً (حسب المبلغ)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="amountChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        تفاصيل المشتريات حسب الصنف
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>كود الصنف</th>
                                    <th>اسم الصنف</th>
                                    <th>الفئة</th>
                                    <th>الكمية المشتراة</th>
                                    <th>الوحدة</th>
                                    <th>متوسط السعر</th>
                                    <th>إجمالي المبلغ</th>
                                    <th>عدد المرات</th>
                                    <th>آخر شراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($itemData ?? [] as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $data->item_code }}</code></td>
                                    <td>
                                        <strong>{{ $data->item_name }}</strong>
                                        @if($data->item_description)
                                        <br>
                                        <small class="text-muted">{{ $data->item_description }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $data->category_name ?? 'غير محدد' }}</span></td>
                                    <td><strong class="text-primary">{{ number_format($data->total_quantity) }}</strong></td>
                                    <td>{{ $data->unit ?? 'قطعة' }}</td>
                                    <td>{{ number_format($data->avg_price, 2) }}</td>
                                    <td><strong class="text-success">{{ number_format($data->total_amount, 2) }}</strong></td>
                                    <td><span class="badge bg-info">{{ $data->purchase_count }}</span></td>
                                    <td>{{ $data->last_purchase_date ?? 'غير محدد' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد بيانات</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(isset($itemData) && count($itemData) > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">الإجمالي:</th>
                                    <th><strong class="text-primary">{{ number_format(array_sum(array_column($itemData->toArray(), 'total_quantity'))) }}</strong></th>
                                    <th></th>
                                    <th></th>
                                    <th><strong class="text-success">{{ number_format(array_sum(array_column($itemData->toArray(), 'total_amount')), 2) }}</strong></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
                @if(isset($itemData) && method_exists($itemData, 'hasPages') && $itemData->hasPages())
                <div class="card-footer">
                    {{ $itemData->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Category Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        ملخص حسب الفئات
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-secondary">
                                <tr>
                                    <th>الفئة</th>
                                    <th>عدد الأصناف</th>
                                    <th>إجمالي الكمية</th>
                                    <th>إجمالي المبلغ</th>
                                    <th>النسبة المئوية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categoryData ?? [] as $category)
                                <tr>
                                    <td><strong>{{ $category->category_name }}</strong></td>
                                    <td><span class="badge bg-info">{{ $category->item_count }}</span></td>
                                    <td>{{ number_format($category->total_quantity) }}</td>
                                    <td><strong class="text-success">{{ number_format($category->total_amount, 2) }}</strong></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" role="progressbar" 
                                                 style="width: {{ $category->percentage ?? 0 }}%;" 
                                                 aria-valuenow="{{ $category->percentage ?? 0 }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ number_format($category->percentage ?? 0, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">
                                        لا توجد بيانات
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Quantity Chart
const quantityCtx = document.getElementById('quantityChart');
if (quantityCtx) {
    new Chart(quantityCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_slice(array_column($itemData->toArray() ?? [], 'item_name'), 0, 10)) !!},
            datasets: [{
                label: 'الكمية المشتراة',
                data: {!! json_encode(array_slice(array_column($itemData->toArray() ?? [], 'total_quantity'), 0, 10)) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Amount Chart
const amountCtx = document.getElementById('amountChart');
if (amountCtx) {
    new Chart(amountCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_slice(array_column($itemData->toArray() ?? [], 'item_name'), 0, 10)) !!},
            datasets: [{
                label: 'إجمالي المبلغ',
                data: {!! json_encode(array_slice(array_column($itemData->toArray() ?? [], 'total_amount'), 0, 10)) !!},
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
                    beginAtZero: true
                }
            }
        }
    });
}

function exportToExcel() {
    alert('جاري تصدير البيانات إلى Excel...');
}
</script>
@endpush
@endsection

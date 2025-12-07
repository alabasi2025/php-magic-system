@extends('layouts.app')

@section('title', 'تقرير المشتريات حسب المورد')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-truck me-2"></i>
                        تقرير المشتريات حسب المورد
                    </h4>
                    <div>
                        <button class="btn btn-light btn-sm me-2" onclick="window.print()">
                            <i class="fas fa-print"></i> طباعة
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="exportToExcel()">
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
                    <form method="GET" action="{{ route('purchases.reports.by-supplier') }}" class="row g-3">
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
                            <label for="sort_by" class="form-label">ترتيب حسب</label>
                            <select class="form-select" id="sort_by" name="sort_by">
                                <option value="total_amount" {{ request('sort_by') == 'total_amount' ? 'selected' : '' }}>إجمالي المبلغ</option>
                                <option value="total_orders" {{ request('sort_by') == 'total_orders' ? 'selected' : '' }}>عدد الأوامر</option>
                                <option value="supplier_name" {{ request('sort_by') == 'supplier_name' ? 'selected' : '' }}>اسم المورد</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-filter"></i> تصفية
                            </button>
                            <a href="{{ route('purchases.reports.by-supplier') }}" class="btn btn-secondary">
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
            <div class="card shadow-sm border-0 border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">عدد الموردين</h6>
                            <h3 class="mb-0">{{ $statistics['total_suppliers'] ?? 0 }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-truck fa-2x"></i>
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
                            <h6 class="text-muted mb-2">إجمالي المشتريات</h6>
                            <h3 class="mb-0">{{ number_format($statistics['total_purchases'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-dollar-sign fa-2x"></i>
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
                            <h6 class="text-muted mb-2">متوسط الشراء</h6>
                            <h3 class="mb-0">{{ number_format($statistics['avg_purchase'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-chart-bar fa-2x"></i>
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
                            <h6 class="text-muted mb-2">أعلى مورد</h6>
                            <h6 class="mb-0 text-truncate" title="{{ $statistics['top_supplier'] ?? 'غير محدد' }}">
                                {{ $statistics['top_supplier'] ?? 'غير محدد' }}
                            </h6>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-trophy fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        توزيع المشتريات حسب المورد
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="supplierChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>
                        أفضل 5 موردين
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($topSuppliers ?? [] as $index => $supplier)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-success rounded-pill me-2">{{ $index + 1 }}</span>
                                <strong>{{ $supplier->name }}</strong>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ number_format($supplier->total_amount, 2) }}</span>
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

    <!-- Suppliers Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        تفاصيل المشتريات حسب المورد
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>اسم المورد</th>
                                    <th>عدد الأوامر</th>
                                    <th>إجمالي المبلغ</th>
                                    <th>متوسط قيمة الأمر</th>
                                    <th>آخر عملية شراء</th>
                                    <th>النسبة المئوية</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplierData ?? [] as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $data->supplier_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $data->supplier_code ?? '' }}</small>
                                    </td>
                                    <td><span class="badge bg-info">{{ $data->total_orders }}</span></td>
                                    <td><strong class="text-success">{{ number_format($data->total_amount, 2) }}</strong></td>
                                    <td>{{ number_format($data->avg_order_value, 2) }}</td>
                                    <td>{{ $data->last_purchase_date ?? 'غير محدد' }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $data->percentage ?? 0 }}%;" 
                                                 aria-valuenow="{{ $data->percentage ?? 0 }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ number_format($data->percentage ?? 0, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('purchases.reports.supplier-details', $data->supplier_id) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد بيانات</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(isset($supplierData) && count($supplierData) > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">الإجمالي:</th>
                                    <th><span class="badge bg-info">{{ array_sum(array_column($supplierData->toArray(), 'total_orders')) }}</span></th>
                                    <th><strong class="text-success">{{ number_format(array_sum(array_column($supplierData->toArray(), 'total_amount')), 2) }}</strong></th>
                                    <th colspan="4"></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
                @if(isset($supplierData) && method_exists($supplierData, 'hasPages') && $supplierData->hasPages())
                <div class="card-footer">
                    {{ $supplierData->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Supplier Chart
const ctx = document.getElementById('supplierChart');
if (ctx) {
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_column($supplierData->toArray() ?? [], 'supplier_name')) !!},
            datasets: [{
                label: 'إجمالي المبلغ',
                data: {!! json_encode(array_column($supplierData->toArray() ?? [], 'total_amount')) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(199, 199, 199, 0.7)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
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

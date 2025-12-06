@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-0">
                <i class="fas fa-tachometer-alt me-2"></i>
                لوحة تحكم المخازن
            </h2>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي المخازن</h6>
                            <h3 class="mb-0">{{ $stats['total_warehouses'] }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-warehouse fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي الأصناف</h6>
                            <h3 class="mb-0">{{ $stats['total_items'] }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-boxes fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">حركات اليوم</h6>
                            <h3 class="mb-0">{{ $stats['total_movements_today'] }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-exchange-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">بانتظار الاعتماد</h6>
                            <h3 class="mb-0">{{ $stats['pending_approvals'] }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if($stats['items_below_min_stock'] > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>تنبيه!</strong> يوجد {{ $stats['items_below_min_stock'] }} صنف أقل من الحد الأدنى للمخزون.
            <a href="{{ route('inventory.reports.below-min-stock') }}" class="alert-link">عرض التفاصيل</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Recent Movements -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        آخر الحركات
                    </h5>
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                        <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>رقم الحركة</th>
                                    <th>النوع</th>
                                    <th>المخزن</th>
                                    <th>الصنف</th>
                                    <th>الكمية</th>
                                    <th>المستخدم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMovements as $movement)
                                    <tr>
                                        <td><strong>{{ $movement->movement_number }}</strong></td>
                                        <td>
                                            @if($movement->movement_type == 'stock_in')
                                                <span class="badge bg-success">إدخال</span>
                                            @elseif($movement->movement_type == 'stock_out')
                                                <span class="badge bg-danger">إخراج</span>
                                            @elseif($movement->movement_type == 'transfer')
                                                <span class="badge bg-primary">نقل</span>
                                            @elseif($movement->movement_type == 'adjustment')
                                                <span class="badge bg-warning">تسوية</span>
                                            @else
                                                <span class="badge bg-info">إرجاع</span>
                                            @endif
                                        </td>
                                        <td>{{ $movement->warehouse->name }}</td>
                                        <td>{{ $movement->item->name }}</td>
                                        <td>{{ number_format($movement->quantity, 2) }}</td>
                                        <td>{{ $movement->creator->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">
                                            لا توجد حركات حديثة
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Value by Warehouse -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>
                        قيمة المخزون حسب المخزن
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($stockValueByWarehouse as $warehouse)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold">{{ $warehouse->name }}</span>
                                <span class="text-success fw-bold">{{ number_format($warehouse->total_value, 2) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ ($warehouse->total_value / $stockValueByWarehouse->max('total_value')) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-link me-2"></i>
                        روابط سريعة
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('warehouses.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-warehouse me-2 text-primary"></i>
                        إدارة المخازن
                    </a>
                    <a href="{{ route('items.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-boxes me-2 text-success"></i>
                        إدارة الأصناف
                    </a>
                    <a href="{{ route('stock-movements.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-exchange-alt me-2 text-info"></i>
                        حركات المخزون
                    </a>
                    <a href="{{ route('inventory.reports.current-stock') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-bar me-2 text-warning"></i>
                        تقرير المخزون الحالي
                    </a>
                    <a href="{{ route('inventory.reports.below-min-stock') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                        الأصناف الناقصة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

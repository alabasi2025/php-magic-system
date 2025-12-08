@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/inventory-enhanced.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header محسن -->
    <div class="dashboard-header fade-in">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1>
                    <i class="fas fa-tachometer-alt me-3"></i>
                    لوحة تحكم المخازن
                </h1>
                <p class="mb-0">مرحباً بك في نظام إدارة المخزون - تتبع وإدارة مخزونك بكل سهولة</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="text-white">
                    <i class="fas fa-calendar-alt me-2"></i>
                    {{ now()->format('Y-m-d') }}
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقات الإحصائيات المحسنة -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3 fade-in" style="animation-delay: 0.1s">
            <div class="inventory-card">
                <div class="inventory-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
                <h3 class="mb-1">{{ $stats['total_warehouses'] }}</h3>
                <p class="mb-0 opacity-90">إجمالي المخازن</p>
                <div class="mt-3">
                    <a href="{{ route('inventory.warehouses.index') }}" class="text-white text-decoration-none">
                        <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3 fade-in" style="animation-delay: 0.2s">
            <div class="inventory-card success">
                <div class="inventory-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <h3 class="mb-1">{{ $stats['total_items'] }}</h3>
                <p class="mb-0 opacity-90">إجمالي الأصناف</p>
                <div class="mt-3">
                    <a href="{{ route('inventory.items.index') }}" class="text-white text-decoration-none">
                        <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3 fade-in" style="animation-delay: 0.3s">
            <div class="inventory-card info">
                <div class="inventory-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <h3 class="mb-1">{{ $stats['total_movements_today'] }}</h3>
                <p class="mb-0 opacity-90">حركات اليوم</p>
                <div class="mt-3">
                    <a href="{{ route('inventory.stock-movements.index') }}" class="text-white text-decoration-none">
                        <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3 fade-in" style="animation-delay: 0.4s">
            <div class="inventory-card warning">
                <div class="inventory-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="mb-1">{{ $stats['pending_approvals'] }}</h3>
                <p class="mb-0 opacity-90">بانتظار الاعتماد</p>
                <div class="mt-3">
                    <a href="{{ route('inventory.stock-movements.index') }}?status=pending" class="text-white text-decoration-none">
                        <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- تنبيهات محسنة -->
    @if($stats['items_below_min_stock'] > 0)
        <div class="alert-inventory alert-inventory-warning alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold">تنبيه مخزون منخفض!</h6>
                    <p class="mb-0">يوجد <strong>{{ $stats['items_below_min_stock'] }}</strong> صنف أقل من الحد الأدنى للمخزون.</p>
                </div>
                <div>
                    <a href="{{ route('inventory.reports.below-min-stock') }}" class="btn btn-sm btn-inventory-danger">
                        <i class="fas fa-eye me-1"></i>
                        عرض التفاصيل
                    </a>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- آخر الحركات -->
        <div class="col-md-8 mb-4">
            <div class="inventory-table">
                <div class="d-flex justify-content-between align-items-center p-3 bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>
                        آخر الحركات
                    </h5>
                    <a href="{{ route('inventory.stock-movements.index') }}" class="btn btn-sm btn-inventory-primary">
                        عرض الكل
                        <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
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
                                    <td><strong class="text-primary">{{ $movement->movement_number }}</strong></td>
                                    <td>
                                        @if($movement->movement_type == 'stock_in')
                                            <span class="badge-inventory badge-inventory-active">
                                                <i class="fas fa-arrow-down me-1"></i>
                                                إدخال
                                            </span>
                                        @elseif($movement->movement_type == 'stock_out')
                                            <span class="badge-inventory" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                                                <i class="fas fa-arrow-up me-1"></i>
                                                إخراج
                                            </span>
                                        @elseif($movement->movement_type == 'transfer')
                                            <span class="badge-inventory" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                <i class="fas fa-exchange-alt me-1"></i>
                                                نقل
                                            </span>
                                        @elseif($movement->movement_type == 'adjustment')
                                            <span class="badge-inventory" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                                <i class="fas fa-balance-scale me-1"></i>
                                                تسوية
                                            </span>
                                        @else
                                            <span class="badge-inventory" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                                <i class="fas fa-undo me-1"></i>
                                                إرجاع
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $movement->warehouse->name }}</td>
                                    <td>{{ $movement->item->name }}</td>
                                    <td><strong>{{ number_format($movement->quantity, 2) }}</strong></td>
                                    <td>
                                        <i class="fas fa-user-circle me-1 text-muted"></i>
                                        {{ $movement->creator->name }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state py-4">
                                            <div class="empty-state-icon">
                                                <i class="fas fa-inbox"></i>
                                            </div>
                                            <h5>لا توجد حركات حديثة</h5>
                                            <p class="text-muted">ابدأ بإضافة حركات مخزون جديدة</p>
                                            <a href="{{ route('inventory.stock-movements.create') }}" class="btn btn-inventory-primary">
                                                <i class="fas fa-plus me-2"></i>
                                                إضافة حركة جديدة
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="col-md-4">
            <!-- قيمة المخزون -->
            <div class="stat-card mb-4">
                <h5 class="mb-4">
                    <i class="fas fa-dollar-sign me-2 text-success"></i>
                    قيمة المخزون حسب المخزن
                </h5>
                @forelse($stockValueByWarehouse as $warehouse)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark">
                                <i class="fas fa-warehouse me-2 text-primary"></i>
                                {{ $warehouse->name }}
                            </span>
                            <span class="stat-number" style="font-size: 18px;">{{ number_format($warehouse->total_value, 2) }}</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar" 
                                 style="background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%); width: {{ ($warehouse->total_value / $stockValueByWarehouse->max('total_value')) * 100 }}%">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-chart-line fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0">لا توجد بيانات</p>
                    </div>
                @endforelse
            </div>

            <!-- روابط سريعة -->
            <div class="stat-card">
                <h5 class="mb-4">
                    <i class="fas fa-link me-2 text-info"></i>
                    روابط سريعة
                </h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('inventory.warehouses.index') }}" class="btn btn-outline-primary text-start">
                        <i class="fas fa-warehouse me-2"></i>
                        إدارة المخازن
                        <i class="fas fa-arrow-left float-end mt-1"></i>
                    </a>
                    <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-success text-start">
                        <i class="fas fa-boxes me-2"></i>
                        إدارة الأصناف
                        <i class="fas fa-arrow-left float-end mt-1"></i>
                    </a>
                    <a href="{{ route('inventory.stock-movements.index') }}" class="btn btn-outline-info text-start">
                        <i class="fas fa-exchange-alt me-2"></i>
                        حركات المخزون
                        <i class="fas fa-arrow-left float-end mt-1"></i>
                    </a>
                    <a href="{{ route('inventory.reports.current-stock') }}" class="btn btn-outline-warning text-start">
                        <i class="fas fa-chart-bar me-2"></i>
                        تقرير المخزون الحالي
                        <i class="fas fa-arrow-left float-end mt-1"></i>
                    </a>
                    <a href="{{ route('inventory.reports.below-min-stock') }}" class="btn btn-outline-danger text-start">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        الأصناف الناقصة
                        <i class="fas fa-arrow-left float-end mt-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

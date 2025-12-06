@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.warehouses.index') }}">المخازن</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.warehouses.show', $warehouse) }}">{{ $warehouse->name }}</a></li>
                    <li class="breadcrumb-item active">تقرير المخزون</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    تقرير المخزون - {{ $warehouse->name }}
                </h2>
                <div>
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fas fa-print me-1"></i>
                        طباعة
                    </button>
                    <a href="{{ route('inventory.warehouses.show', $warehouse) }}" class="btn btn-primary">
                        <i class="fas fa-arrow-right me-1"></i>
                        رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات المخزن -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong class="text-muted">رمز المخزن:</strong>
                            <p class="mb-0">{{ $warehouse->code }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong class="text-muted">الموقع:</strong>
                            <p class="mb-0">{{ $warehouse->location ?? 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong class="text-muted">المسؤول:</strong>
                            <p class="mb-0">{{ $warehouse->manager->name ?? 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong class="text-muted">تاريخ التقرير:</strong>
                            <p class="mb-0">{{ now()->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات ملخصة -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-2x text-primary mb-2"></i>
                    <h3 class="mb-0">{{ $stockData->count() }}</h3>
                    <small class="text-muted">عدد الأصناف</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <i class="fas fa-layer-group fa-2x text-success mb-2"></i>
                    <h3 class="mb-0">{{ number_format($stockData->sum('current_stock'), 0) }}</h3>
                    <small class="text-muted">إجمالي الكميات</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <h3 class="mb-0">{{ $stockData->filter(function($item) { return $item->current_stock <= $item->min_stock; })->count() }}</h3>
                    <small class="text-muted">أصناف أقل من الحد الأدنى</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                    <h3 class="mb-0">{{ number_format($stockData->sum('stock_value'), 2) }}</h3>
                    <small class="text-muted">القيمة الإجمالية</small>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول تفاصيل المخزون -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        تفاصيل المخزون
                    </h5>
                </div>
                <div class="card-body">
                    @if($stockData->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            لا توجد أصناف في المخزن حالياً
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>رمز الصنف</th>
                                        <th>اسم الصنف</th>
                                        <th>الوحدة</th>
                                        <th>الكمية الحالية</th>
                                        <th>الحد الأدنى</th>
                                        <th>الحد الأقصى</th>
                                        <th>سعر الوحدة</th>
                                        <th>القيمة الإجمالية</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockData as $index => $item)
                                        @php
                                            $isLow = $item->min_stock && $item->current_stock <= $item->min_stock;
                                            $isHigh = $item->max_stock && $item->current_stock >= $item->max_stock;
                                            $rowClass = $isLow ? 'table-danger' : ($isHigh ? 'table-warning' : '');
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->sku }}</td>
                                            <td>
                                                <a href="{{ route('inventory.items.show', $item->id) }}">
                                                    {{ $item->name }}
                                                </a>
                                            </td>
                                            <td>{{ $item->unit_name }}</td>
                                            <td class="text-center">
                                                <strong>{{ number_format($item->current_stock, 2) }}</strong>
                                            </td>
                                            <td class="text-center">{{ $item->min_stock ?? '-' }}</td>
                                            <td class="text-center">{{ $item->max_stock ?? '-' }}</td>
                                            <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="text-end">
                                                <strong>{{ number_format($item->stock_value, 2) }}</strong>
                                            </td>
                                            <td class="text-center">
                                                @if($isLow)
                                                    <span class="badge bg-danger">منخفض</span>
                                                @elseif($isHigh)
                                                    <span class="badge bg-warning">مرتفع</span>
                                                @else
                                                    <span class="badge bg-success">طبيعي</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">الإجمالي:</th>
                                        <th class="text-center">{{ number_format($stockData->sum('current_stock'), 2) }}</th>
                                        <th colspan="3"></th>
                                        <th class="text-end">
                                            <strong>{{ number_format($stockData->sum('stock_value'), 2) }}</strong>
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .breadcrumb, nav {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection

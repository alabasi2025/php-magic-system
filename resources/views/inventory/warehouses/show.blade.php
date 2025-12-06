@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.warehouses.index') }}">المخازن</a></li>
                    <li class="breadcrumb-item active">تفاصيل المخزن</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-warehouse me-2"></i>
                    {{ $warehouse->name }}
                </h2>
                <div>
                    <a href="{{ route('inventory.warehouses.edit', $warehouse) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>
                        تعديل
                    </a>
                    <a href="{{ route('inventory.warehouses.stock-report', $warehouse) }}" class="btn btn-info">
                        <i class="fas fa-chart-bar me-1"></i>
                        تقرير المخزون
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- معلومات المخزن -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات المخزن
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted"><strong>رمز المخزن:</strong></td>
                            <td>{{ $warehouse->code }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>اسم المخزن:</strong></td>
                            <td>{{ $warehouse->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>الموقع:</strong></td>
                            <td>{{ $warehouse->location ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>المسؤول:</strong></td>
                            <td>
                                @if($warehouse->manager)
                                    <span class="badge bg-info">{{ $warehouse->manager->name }}</span>
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>الحالة:</strong></td>
                            <td>
                                @if($warehouse->status == 'active')
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">معطل</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>تاريخ الإنشاء:</strong></td>
                            <td>{{ $warehouse->created_at->format('Y-m-d') }}</td>
                        </tr>
                    </table>

                    @if($warehouse->description)
                        <hr>
                        <div>
                            <strong class="text-muted">الوصف:</strong>
                            <p class="mt-2">{{ $warehouse->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- إحصائيات سريعة -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        إحصائيات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-0">{{ $stockLevels->count() }}</h3>
                                <small class="text-muted">أصناف في المخزن</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h3 class="text-success mb-0">{{ $warehouse->stockMovements->count() }}</h3>
                                <small class="text-muted">إجمالي الحركات</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <h3 class="text-info mb-0">{{ number_format($stockLevels->sum('current_stock'), 2) }}</h3>
                                <small class="text-muted">إجمالي الكميات</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- حركات المخزون الأخيرة -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        آخر حركات المخزون
                    </h5>
                </div>
                <div class="card-body">
                    @if($warehouse->stockMovements->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            لا توجد حركات مخزون حتى الآن
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الصنف</th>
                                        <th>نوع الحركة</th>
                                        <th>الكمية</th>
                                        <th>الحالة</th>
                                        <th>المستخدم</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($warehouse->stockMovements->take(10) as $movement)
                                        <tr>
                                            <td>{{ $movement->movement_date->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('inventory.items.show', $movement->item) }}">
                                                    {{ $movement->item->name }}
                                                </a>
                                            </td>
                                            <td>
                                                @php
                                                    $typeLabels = [
                                                        'stock_in' => 'إدخال',
                                                        'stock_out' => 'إخراج',
                                                        'transfer_in' => 'تحويل وارد',
                                                        'transfer_out' => 'تحويل صادر',
                                                        'adjustment' => 'تسوية',
                                                        'return' => 'مرتجع'
                                                    ];
                                                    $typeColors = [
                                                        'stock_in' => 'success',
                                                        'stock_out' => 'danger',
                                                        'transfer_in' => 'info',
                                                        'transfer_out' => 'warning',
                                                        'adjustment' => 'secondary',
                                                        'return' => 'primary'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $typeColors[$movement->movement_type] ?? 'secondary' }}">
                                                    {{ $typeLabels[$movement->movement_type] ?? $movement->movement_type }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($movement->quantity, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($movement->status == 'approved')
                                                    <span class="badge bg-success">معتمد</span>
                                                @elseif($movement->status == 'pending')
                                                    <span class="badge bg-warning">معلق</span>
                                                @else
                                                    <span class="badge bg-danger">مرفوض</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $movement->user->name ?? 'N/A' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($warehouse->stockMovements->count() > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('inventory.reports.movements', ['warehouse_id' => $warehouse->id]) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-list me-1"></i>
                                    عرض جميع الحركات
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- مستويات المخزون الحالية -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes me-2"></i>
                        مستويات المخزون الحالية
                    </h5>
                </div>
                <div class="card-body">
                    @if($stockLevels->isEmpty())
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            لا توجد أصناف في المخزن حالياً
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>الصنف</th>
                                        <th>الكمية الحالية</th>
                                        <th>الحد الأدنى</th>
                                        <th>الحد الأقصى</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockLevels as $itemId => $stock)
                                        @php
                                            $item = \App\Models\Item::find($itemId);
                                            $currentStock = $stock->current_stock;
                                            $isLow = $item && $item->min_stock && $currentStock <= $item->min_stock;
                                            $isHigh = $item && $item->max_stock && $currentStock >= $item->max_stock;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->name ?? 'N/A' }}</td>
                                            <td><strong>{{ number_format($currentStock, 2) }}</strong></td>
                                            <td>{{ $item->min_stock ?? '-' }}</td>
                                            <td>{{ $item->max_stock ?? '-' }}</td>
                                            <td>
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
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

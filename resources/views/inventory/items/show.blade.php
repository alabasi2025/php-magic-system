@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.items.index') }}">الأصناف</a></li>
                    <li class="breadcrumb-item active">تفاصيل الصنف</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-boxes me-2"></i>
                    {{ $item->name }}
                </h2>
                <div>
                    <a href="{{ route('inventory.items.edit', $item) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>
                        تعديل
                    </a>
                    <a href="{{ route('inventory.reports.item-history', $item) }}" class="btn btn-info">
                        <i class="fas fa-history me-1"></i>
                        سجل الحركات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- معلومات الصنف -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات الصنف
                    </h5>
                </div>
                <div class="card-body">
                    @if($item->image_path)
                        <div class="text-center mb-3">
                            <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    @endif

                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted"><strong>رمز الصنف (SKU):</strong></td>
                            <td>{{ $item->sku }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>الباركود:</strong></td>
                            <td>{{ $item->barcode ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>الوحدة:</strong></td>
                            <td>
                                <span class="badge bg-info">{{ $item->unit->name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>سعر الوحدة:</strong></td>
                            <td><strong>{{ number_format($item->unit_price, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>الحد الأدنى:</strong></td>
                            <td>{{ $item->min_stock ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>الحد الأقصى:</strong></td>
                            <td>{{ $item->max_stock ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>الحالة:</strong></td>
                            <td>
                                @if($item->status == 'active')
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">معطل</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>تاريخ الإنشاء:</strong></td>
                            <td>{{ $item->created_at->format('Y-m-d') }}</td>
                        </tr>
                    </table>

                    @if($item->description)
                        <hr>
                        <div>
                            <strong class="text-muted">الوصف:</strong>
                            <p class="mt-2">{{ $item->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- إحصائيات سريعة -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        إحصائيات المخزون
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <h2 class="text-primary mb-0">{{ number_format($item->current_stock, 2) }}</h2>
                                <small class="text-muted">الكمية الإجمالية</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success mb-0">{{ $stockByWarehouse->count() }}</h4>
                                <small class="text-muted">عدد المخازن</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-info mb-0">{{ number_format($item->current_stock * $item->unit_price, 2) }}</h4>
                                <small class="text-muted">القيمة الإجمالية</small>
                            </div>
                        </div>
                    </div>

                    @if($item->min_stock && $item->current_stock <= $item->min_stock)
                        <div class="alert alert-danger mb-0 mt-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>تحذير:</strong> الكمية أقل من الحد الأدنى!
                        </div>
                    @elseif($item->max_stock && $item->current_stock >= $item->max_stock)
                        <div class="alert alert-warning mb-0 mt-2">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>ملاحظة:</strong> الكمية وصلت للحد الأقصى
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- المخزون حسب المخازن -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-warehouse me-2"></i>
                        المخزون حسب المخازن
                    </h5>
                </div>
                <div class="card-body">
                    @if($stockByWarehouse->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            لا يوجد مخزون لهذا الصنف في أي مخزن
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>المخزن</th>
                                        <th>الكمية المتاحة</th>
                                        <th>القيمة</th>
                                        <th>النسبة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockByWarehouse as $stock)
                                        @php
                                            $percentage = $item->current_stock > 0 ? ($stock->current_stock / $item->current_stock) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="{{ route('inventory.warehouses.show', $stock->id) }}">
                                                    {{ $stock->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($stock->current_stock, 2) }}</strong>
                                                {{ $item->unit->name }}
                                            </td>
                                            <td>{{ number_format($stock->current_stock * $item->unit_price, 2) }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                                                        {{ number_format($percentage, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>الإجمالي</th>
                                        <th>
                                            <strong>{{ number_format($item->current_stock, 2) }}</strong>
                                            {{ $item->unit->name }}
                                        </th>
                                        <th>{{ number_format($item->current_stock * $item->unit_price, 2) }}</th>
                                        <th>100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- آخر حركات الصنف -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        آخر حركات الصنف
                    </h5>
                </div>
                <div class="card-body">
                    @if($item->stockMovements->isEmpty())
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            لا توجد حركات لهذا الصنف حتى الآن
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>المخزن</th>
                                        <th>نوع الحركة</th>
                                        <th>الكمية</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->stockMovements->take(10) as $movement)
                                        <tr>
                                            <td>{{ $movement->movement_date->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('inventory.warehouses.show', $movement->warehouse) }}">
                                                    {{ $movement->warehouse->name }}
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($item->stockMovements->count() > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('inventory.reports.item-history', $item) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-list me-1"></i>
                                    عرض جميع الحركات
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

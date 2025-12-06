@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    تقرير المخزون الحالي
                </h2>
                <a href="{{ route('inventory.reports.export-current-stock', ['warehouse_id' => $warehouseId]) }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i>
                    تصدير Excel
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <form method="GET" action="{{ route('inventory.reports.current-stock') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="warehouse_id" class="form-label">المخزن</label>
                    <select name="warehouse_id" id="warehouse_id" class="form-select">
                        <option value="">جميع المخازن</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $warehouseId == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>
                        عرض
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>اسم الصنف</th>
                            <th>المخزن</th>
                            <th>الكمية الحالية</th>
                            <th>الوحدة</th>
                            <th>الحد الأدنى</th>
                            <th>الحد الأقصى</th>
                            <th>سعر الوحدة</th>
                            <th>القيمة الإجمالية</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalValue = 0;
                        @endphp
                        @forelse($stockData as $stock)
                            @php
                                $totalValue += $stock->stock_value;
                                $isLow = $stock->current_stock < $stock->min_stock;
                                $isHigh = $stock->current_stock > $stock->max_stock;
                            @endphp
                            <tr class="{{ $isLow ? 'table-warning' : ($isHigh ? 'table-info' : '') }}">
                                <td><strong>{{ $stock->sku }}</strong></td>
                                <td>{{ $stock->item_name }}</td>
                                <td>{{ $stock->warehouse_name }}</td>
                                <td><strong>{{ number_format($stock->current_stock, 2) }}</strong></td>
                                <td>{{ $stock->unit_name }}</td>
                                <td>{{ number_format($stock->min_stock, 2) }}</td>
                                <td>{{ number_format($stock->max_stock, 2) }}</td>
                                <td>{{ number_format($stock->unit_price, 2) }}</td>
                                <td><strong>{{ number_format($stock->stock_value, 2) }}</strong></td>
                                <td>
                                    @if($isLow)
                                        <span class="badge bg-warning">أقل من الحد الأدنى</span>
                                    @elseif($isHigh)
                                        <span class="badge bg-info">أعلى من الحد الأقصى</span>
                                    @else
                                        <span class="badge bg-success">طبيعي</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    لا توجد بيانات مخزون
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($stockData->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="8" class="text-end">الإجمالي:</th>
                                <th><strong>{{ number_format($totalValue, 2) }}</strong></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

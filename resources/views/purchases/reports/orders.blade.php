@extends('layouts.app')

@section('title', 'تقرير أوامر الشراء')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>
                        تقرير أوامر الشراء
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
                    <form method="GET" action="{{ route('purchases.reports.orders') }}" class="row g-3">
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
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                                <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>مستلم</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> تصفية
                            </button>
                            <a href="{{ route('purchases.reports.orders') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> إعادة تعيين
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إجمالي الأوامر</h6>
                            <h3 class="mb-0">{{ $statistics['total_orders'] ?? 0 }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-shopping-cart fa-2x"></i>
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
                            <h6 class="text-muted mb-2">قيد الانتظار</h6>
                            <h3 class="mb-0">{{ $statistics['pending_orders'] ?? 0 }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h6 class="text-muted mb-2">متوسط قيمة الأمر</h6>
                            <h3 class="mb-0">{{ number_format($statistics['avg_order_value'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        قائمة أوامر الشراء
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الأمر</th>
                                    <th>التاريخ</th>
                                    <th>المورد</th>
                                    <th>إجمالي المبلغ</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الاستلام المتوقع</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders ?? [] as $index => $order)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $order->order_number }}</strong></td>
                                    <td>{{ $order->order_date }}</td>
                                    <td>{{ $order->supplier->name ?? 'غير محدد' }}</td>
                                    <td><strong class="text-success">{{ number_format($order->total_amount, 2) }}</strong></td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning">قيد الانتظار</span>
                                        @elseif($order->status == 'approved')
                                            <span class="badge bg-info">معتمد</span>
                                        @elseif($order->status == 'received')
                                            <span class="badge bg-success">مستلم</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="badge bg-danger">ملغي</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->expected_delivery_date ?? 'غير محدد' }}</td>
                                    <td>
                                        <a href="{{ route('purchases.orders.show', $order->id) }}" class="btn btn-sm btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('purchases.orders.print', $order->id) }}" class="btn btn-sm btn-secondary" title="طباعة" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد أوامر شراء</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(isset($orders) && $orders->count() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">الإجمالي:</th>
                                    <th><strong class="text-success">{{ number_format($orders->sum('total_amount'), 2) }}</strong></th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
                @if(isset($orders) && $orders->hasPages())
                <div class="card-footer">
                    {{ $orders->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportToExcel() {
    // Export functionality - can be implemented using a library like SheetJS
    alert('جاري تصدير البيانات إلى Excel...');
}
</script>
@endpush
@endsection

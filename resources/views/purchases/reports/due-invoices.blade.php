@extends('layouts.app')

@section('title', 'تقرير الفواتير المستحقة')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        تقرير الفواتير المستحقة
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
                    <form method="GET" action="{{ route('purchases.reports.due-invoices') }}" class="row g-3">
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
                            <label for="due_status" class="form-label">حالة الاستحقاق</label>
                            <select class="form-select" id="due_status" name="due_status">
                                <option value="">الكل</option>
                                <option value="overdue" {{ request('due_status') == 'overdue' ? 'selected' : '' }}>متأخرة</option>
                                <option value="due_today" {{ request('due_status') == 'due_today' ? 'selected' : '' }}>مستحقة اليوم</option>
                                <option value="due_week" {{ request('due_status') == 'due_week' ? 'selected' : '' }}>مستحقة خلال أسبوع</option>
                                <option value="due_month" {{ request('due_status') == 'due_month' ? 'selected' : '' }}>مستحقة خلال شهر</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="amount_from" class="form-label">المبلغ من</label>
                            <input type="number" class="form-control" id="amount_from" name="amount_from" 
                                   value="{{ request('amount_from') }}" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-md-3">
                            <label for="amount_to" class="form-label">المبلغ إلى</label>
                            <input type="number" class="form-control" id="amount_to" name="amount_to" 
                                   value="{{ request('amount_to') }}" step="0.01" placeholder="0.00">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-filter"></i> تصفية
                            </button>
                            <a href="{{ route('purchases.reports.due-invoices') }}" class="btn btn-secondary">
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
            <div class="card shadow-sm border-0 border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إجمالي المستحق</h6>
                            <h3 class="mb-0 text-danger">{{ number_format($statistics['total_due'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
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
                            <h6 class="text-muted mb-2">فواتير متأخرة</h6>
                            <h3 class="mb-0 text-warning">{{ $statistics['overdue_count'] ?? 0 }}</h3>
                            <small class="text-muted">{{ number_format($statistics['overdue_amount'] ?? 0, 2) }}</small>
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
                            <h6 class="text-muted mb-2">مستحقة هذا الشهر</h6>
                            <h3 class="mb-0 text-info">{{ $statistics['due_month_count'] ?? 0 }}</h3>
                            <small class="text-muted">{{ number_format($statistics['due_month_amount'] ?? 0, 2) }}</small>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-calendar-alt fa-2x"></i>
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
                            <h6 class="text-muted mb-2">متوسط الفاتورة</h6>
                            <h3 class="mb-0 text-success">{{ number_format($statistics['avg_invoice'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert for Overdue Invoices -->
    @if(isset($statistics['overdue_count']) && $statistics['overdue_count'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>تنبيه!</strong> لديك <strong>{{ $statistics['overdue_count'] }}</strong> فاتورة متأخرة بإجمالي <strong>{{ number_format($statistics['overdue_amount'] ?? 0, 2) }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Due Invoices Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        قائمة الفواتير المستحقة
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>رقم الفاتورة</th>
                                    <th>المورد</th>
                                    <th>تاريخ الفاتورة</th>
                                    <th>تاريخ الاستحقاق</th>
                                    <th>أيام التأخير</th>
                                    <th>المبلغ الإجمالي</th>
                                    <th>المدفوع</th>
                                    <th>المتبقي</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices ?? [] as $index => $invoice)
                                <tr class="{{ $invoice->is_overdue ? 'table-danger' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                    <td>
                                        {{ $invoice->supplier->name ?? 'غير محدد' }}
                                        <br>
                                        <small class="text-muted">{{ $invoice->supplier->phone ?? '' }}</small>
                                    </td>
                                    <td>{{ $invoice->invoice_date }}</td>
                                    <td>
                                        <strong class="{{ $invoice->is_overdue ? 'text-danger' : 'text-info' }}">
                                            {{ $invoice->due_date }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if($invoice->days_overdue > 0)
                                            <span class="badge bg-danger">{{ $invoice->days_overdue }} يوم</span>
                                        @elseif($invoice->days_until_due == 0)
                                            <span class="badge bg-warning">اليوم</span>
                                        @else
                                            <span class="badge bg-info">{{ $invoice->days_until_due }} يوم</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td><span class="text-success">{{ number_format($invoice->paid_amount, 2) }}</span></td>
                                    <td>
                                        <strong class="text-danger">{{ number_format($invoice->remaining_amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($invoice->payment_status == 'unpaid')
                                            <span class="badge bg-danger">غير مدفوعة</span>
                                        @elseif($invoice->payment_status == 'partial')
                                            <span class="badge bg-warning">مدفوعة جزئياً</span>
                                        @elseif($invoice->payment_status == 'paid')
                                            <span class="badge bg-success">مدفوعة</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('purchases.invoices.show', $invoice->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('purchases.payments.create', ['invoice_id' => $invoice->id]) }}" 
                                           class="btn btn-sm btn-success" 
                                           title="دفع">
                                            <i class="fas fa-money-bill"></i>
                                        </a>
                                        <a href="{{ route('purchases.invoices.print', $invoice->id) }}" 
                                           class="btn btn-sm btn-secondary" 
                                           title="طباعة" 
                                           target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <p class="text-muted">لا توجد فواتير مستحقة</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(isset($invoices) && $invoices->count() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="6" class="text-end">الإجمالي:</th>
                                    <th>{{ number_format($invoices->sum('total_amount'), 2) }}</th>
                                    <th><span class="text-success">{{ number_format($invoices->sum('paid_amount'), 2) }}</span></th>
                                    <th><strong class="text-danger">{{ number_format($invoices->sum('remaining_amount'), 2) }}</strong></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
                @if(isset($invoices) && $invoices->hasPages())
                <div class="card-footer">
                    {{ $invoices->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Supplier Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        ملخص المستحقات حسب المورد
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-secondary">
                                <tr>
                                    <th>المورد</th>
                                    <th>عدد الفواتير</th>
                                    <th>إجمالي المستحق</th>
                                    <th>فواتير متأخرة</th>
                                    <th>مبلغ متأخر</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplierSummary ?? [] as $summary)
                                <tr>
                                    <td>
                                        <strong>{{ $summary->supplier_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $summary->supplier_email ?? '' }}</small>
                                    </td>
                                    <td><span class="badge bg-primary">{{ $summary->invoice_count }}</span></td>
                                    <td><strong class="text-danger">{{ number_format($summary->total_due, 2) }}</strong></td>
                                    <td>
                                        @if($summary->overdue_count > 0)
                                            <span class="badge bg-danger">{{ $summary->overdue_count }}</span>
                                        @else
                                            <span class="badge bg-success">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($summary->overdue_amount > 0)
                                            <strong class="text-danger">{{ number_format($summary->overdue_amount, 2) }}</strong>
                                        @else
                                            <span class="text-success">0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('purchases.reports.supplier-invoices', $summary->supplier_id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> عرض التفاصيل
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">
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
<script>
function exportToExcel() {
    alert('جاري تصدير البيانات إلى Excel...');
}

// Auto-refresh every 5 minutes to keep data up-to-date
setTimeout(function() {
    location.reload();
}, 300000);
</script>
@endpush
@endsection

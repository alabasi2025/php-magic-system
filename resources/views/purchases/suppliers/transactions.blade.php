@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1>معاملات المورد</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('purchases.dashboard') }}">نظام المشتريات</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.suppliers.index') }}">الموردين</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.suppliers.show', $supplier->id) }}">{{ $supplier->name }}</a></li>
                    <li class="breadcrumb-item active">المعاملات</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('purchases.suppliers.show', $supplier->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة لتفاصيل المورد
            </a>
            <a href="{{ route('purchases.suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> قائمة الموردين
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">الرصيد الحالي</h6>
                    <h3 class="{{ ($supplier->balance ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($supplier->balance ?? 0, 2) }}
                    </h3>
                    <small class="text-muted">ريال سعودي</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">إجمالي المشتريات</h6>
                    <h3 class="text-primary">{{ number_format($totalPurchases ?? 0, 2) }}</h3>
                    <small class="text-muted">ريال سعودي</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">إجمالي المدفوعات</h6>
                    <h3 class="text-success">{{ number_format($totalPayments ?? 0, 2) }}</h3>
                    <small class="text-muted">ريال سعودي</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">عدد المعاملات</h6>
                    <h3 class="text-info">{{ $transactionsCount ?? 0 }}</h3>
                    <small class="text-muted">معاملة</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> سجل المعاملات - {{ $supplier->name }}</h5>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filter_type" class="form-label">نوع المعاملة</label>
                            <select class="form-select" id="filter_type">
                                <option value="">الكل</option>
                                <option value="purchase">مشتريات</option>
                                <option value="payment">مدفوعات</option>
                                <option value="return">مرتجعات</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_date_from" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" id="filter_date_from">
                        </div>
                        <div class="col-md-3">
                            <label for="filter_date_to" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" id="filter_date_to">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> تصفية
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>نوع المعاملة</th>
                                    <th>رقم المستند</th>
                                    <th>الوصف</th>
                                    <th>المبلغ</th>
                                    <th>الرصيد بعد المعاملة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions ?? [] as $index => $transaction)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $transaction->date ?? 'غير محدد' }}</td>
                                    <td>
                                        @if($transaction->type == 'purchase')
                                            <span class="badge bg-primary">مشتريات</span>
                                        @elseif($transaction->type == 'payment')
                                            <span class="badge bg-success">مدفوعات</span>
                                        @elseif($transaction->type == 'return')
                                            <span class="badge bg-warning">مرتجعات</span>
                                        @else
                                            <span class="badge bg-secondary">أخرى</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->document_number ?? '-' }}</td>
                                    <td>{{ $transaction->description ?? '-' }}</td>
                                    <td class="{{ $transaction->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($transaction->amount ?? 0, 2) }}
                                    </td>
                                    <td>{{ number_format($transaction->balance_after ?? 0, 2) }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>لا توجد معاملات لهذا المورد حالياً</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($transactions) && method_exists($transactions, 'links'))
                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Export Section -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fas fa-download"></i> تصدير البيانات</h6>
                    <button type="button" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> تصدير Excel
                    </button>
                    <button type="button" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> تصدير PDF
                    </button>
                    <button type="button" class="btn btn-info">
                        <i class="fas fa-print"></i> طباعة
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // يمكن إضافة JavaScript هنا لتفعيل التصفية والبحث
    document.addEventListener('DOMContentLoaded', function() {
        // تفعيل التصفية
        const filterBtn = document.querySelector('.btn-primary');
        if (filterBtn) {
            filterBtn.addEventListener('click', function() {
                // منطق التصفية هنا
                console.log('تطبيق التصفية...');
            });
        }
    });
</script>
@endsection

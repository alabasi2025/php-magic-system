@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1>فواتير الموردين</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('purchases.dashboard') }}">نظام المشتريات</a></li>
                    <li class="breadcrumb-item active">فواتير الموردين</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('purchases.invoices.create') }}" class="btn btn-warning">
                <i class="fas fa-plus"></i> إضافة فاتورة جديدة
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">قائمة فواتير الموردين</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    <th>التاريخ</th>
                                    <th>المورد</th>
                                    <th>المبلغ الإجمالي</th>
                                    <th>المدفوع</th>
                                    <th>المتبقي</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                <tr>
                                    <td>
                                        <a href="{{ route('purchases.invoices.show', $invoice->id) }}" class="text-primary">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td>{{ $invoice->invoice_date }}</td>
                                    <td>{{ $invoice->supplier ? $invoice->supplier->name : 'غير محدد' }}</td>
                                    <td>{{ number_format($invoice->total_amount, 2) }} ريال</td>
                                    <td>{{ number_format($invoice->paid_amount, 2) }} ريال</td>
                                    <td>{{ number_format($invoice->remaining_amount, 2) }} ريال</td>
                                    <td>
                                        @if($invoice->status == 'draft')
                                            <span class="badge bg-secondary">مسودة</span>
                                        @elseif($invoice->status == 'pending')
                                            <span class="badge bg-warning">قيد المراجعة</span>
                                        @elseif($invoice->status == 'approved')
                                            <span class="badge bg-success">معتمدة</span>
                                        @elseif($invoice->status == 'rejected')
                                            <span class="badge bg-danger">مرفوضة</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $invoice->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('purchases.invoices.show', $invoice->id) }}" 
                                               class="btn btn-sm btn-info" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('purchases.invoices.edit', $invoice->id) }}" 
                                               class="btn btn-sm btn-primary" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('purchases.invoices.destroy', $invoice->id) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                        <p>لا توجد فواتير حالياً</p>
                                        <a href="{{ route('purchases.invoices.create') }}" class="btn btn-sm btn-warning">
                                            إضافة فاتورة جديدة
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($invoices->hasPages())
                    <div class="mt-3">
                        {{ $invoices->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

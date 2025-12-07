@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1>تفاصيل المورد</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('purchases.dashboard') }}">نظام المشتريات</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.suppliers.index') }}">الموردين</a></li>
                    <li class="breadcrumb-item active">تفاصيل المورد</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('purchases.suppliers.edit', $supplier->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('purchases.suppliers.transactions', $supplier->id) }}" class="btn btn-info">
                <i class="fas fa-exchange-alt"></i> المعاملات
            </a>
            <a href="{{ route('purchases.suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة للقائمة
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash"></i> حذف
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> المعلومات الأساسية</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>اسم المورد:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $supplier->name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>رقم الهاتف:</strong>
                        </div>
                        <div class="col-md-8">
                            <i class="fas fa-phone"></i> {{ $supplier->phone }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>البريد الإلكتروني:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($supplier->email)
                                <i class="fas fa-envelope"></i> {{ $supplier->email }}
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>الرقم الضريبي:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $supplier->tax_number ?? 'غير محدد' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>العنوان:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $supplier->address ?? 'غير محدد' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>الحالة:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($supplier->status == 1)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">غير نشط</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>تاريخ الإضافة:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $supplier->created_at ? $supplier->created_at->format('Y-m-d H:i') : 'غير محدد' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>آخر تحديث:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $supplier->updated_at ? $supplier->updated_at->format('Y-m-d H:i') : 'غير محدد' }}
                        </div>
                    </div>

                    @if($supplier->notes)
                    <div class="row">
                        <div class="col-12">
                            <strong>ملاحظات:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                {{ $supplier->notes }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-wallet"></i> الرصيد المالي</h5>
                </div>
                <div class="card-body text-center">
                    <h2 class="display-4 {{ ($supplier->balance ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($supplier->balance ?? 0, 2) }}
                    </h2>
                    <p class="text-muted">ريال سعودي</p>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="{{ route('purchases.suppliers.transactions', $supplier->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> عرض المعاملات
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> إحصائيات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">إجمالي المشتريات:</small>
                        </div>
                        <div class="col-6 text-end">
                            <strong>{{ $totalPurchases ?? 0 }}</strong>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">إجمالي المدفوعات:</small>
                        </div>
                        <div class="col-6 text-end">
                            <strong>{{ number_format($totalPayments ?? 0, 2) }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">آخر معاملة:</small>
                        </div>
                        <div class="col-6 text-end">
                            <strong>{{ $lastTransaction ?? 'لا توجد' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف المورد: <strong>{{ $supplier->name }}</strong>؟</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> هذا الإجراء لا يمكن التراجع عنه!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> إلغاء
                </button>
                <form action="{{ route('purchases.suppliers.destroy', $supplier->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

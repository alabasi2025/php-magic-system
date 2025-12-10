@extends('layouts.app')

@section('title', 'عرض أمر الصرف')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-white mb-2 fw-bold">
                        <i class="fas fa-file-invoice me-2"></i>
                        تفاصيل أمر الصرف
                    </h2>
                    <p class="text-white-50 mb-0">رقم الأمر: {{ $stockOut->movement_number }}</p>
                </div>
                <div>
                    <a href="{{ route('inventory.stock-out.index') }}" class="btn btn-light btn-lg rounded-pill px-4">
                        <i class="fas fa-arrow-right me-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- معلومات الأمر -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        المعلومات الأساسية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">رقم الأمر</label>
                                <div class="fw-bold fs-5">{{ $stockOut->movement_number }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">المخزن</label>
                                <div class="fw-bold fs-5">
                                    <i class="fas fa-warehouse text-primary me-2"></i>
                                    {{ $stockOut->warehouse->name ?? 'غير محدد' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">تاريخ الصرف</label>
                                <div class="fw-bold">
                                    <i class="fas fa-calendar text-success me-2"></i>
                                    {{ \Carbon\Carbon::parse($stockOut->movement_date)->format('Y-m-d') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">المنشئ</label>
                                <div class="fw-bold">
                                    <i class="fas fa-user text-info me-2"></i>
                                    {{ $stockOut->creator->name ?? 'غير محدد' }}
                                </div>
                            </div>
                        </div>
                        @if($stockOut->notes)
                        <div class="col-12">
                            <div class="info-item">
                                <label class="text-muted small mb-1">الملاحظات</label>
                                <div class="alert alert-light border">{{ $stockOut->notes }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- جدول الأصناف -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-boxes text-success me-2"></i>
                        الأصناف ({{ $stockOut->items->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <tr>
                                    <th class="text-white">#</th>
                                    <th class="text-white">الصنف</th>
                                    <th class="text-white">الكمية</th>
                                    <th class="text-white">تكلفة الوحدة</th>
                                    <th class="text-white">الإجمالي</th>
                                    <th class="text-white">رقم الدفعة</th>
                                    <th class="text-white">تاريخ الانتهاء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockOut->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $item->item->name ?? 'غير محدد' }}</div>
                                        <small class="text-muted">{{ $item->item->code ?? '' }}</small>
                                    </td>
                                    <td>{{ number_format($item->quantity, 3) }}</td>
                                    <td>{{ number_format($item->unit_cost, 2) }} ريال</td>
                                    <td class="fw-bold text-success">{{ number_format($item->total_cost, 2) }} ريال</td>
                                    <td>{{ $item->batch_number ?? '-' }}</td>
                                    <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d') : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        لا توجد أصناف
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot style="background-color: #f8f9fa;">
                                <tr>
                                    <th colspan="4" class="text-end">الإجمالي الكلي:</th>
                                    <th class="text-success fs-5">{{ number_format($stockOut->items->sum('total_cost'), 2) }} ريال</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- الحالة والإجراءات -->
        <div class="col-lg-4">
            <!-- بطاقة الحالة -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-flag text-warning me-2"></i>
                        الحالة
                    </h5>
                </div>
                <div class="card-body text-center">
                    @if($stockOut->status === 'pending')
                        <div class="status-badge bg-warning bg-opacity-10 p-4 rounded-4 mb-3">
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            <h4 class="text-warning mb-0">قيد الانتظار</h4>
                            <small class="text-muted">في انتظار الاعتماد</small>
                        </div>
                    @elseif($stockOut->status === 'approved')
                        <div class="status-badge bg-success bg-opacity-10 p-4 rounded-4 mb-3">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h4 class="text-success mb-0">معتمد</h4>
                            <small class="text-muted">تم الاعتماد بنجاح</small>
                        </div>
                        @if($stockOut->approver)
                        <div class="mt-3">
                            <small class="text-muted">اعتمد بواسطة:</small>
                            <div class="fw-bold">{{ $stockOut->approver->name }}</div>
                            <small class="text-muted">{{ $stockOut->approved_at ? \Carbon\Carbon::parse($stockOut->approved_at)->format('Y-m-d H:i') : '' }}</small>
                        </div>
                        @endif
                    @elseif($stockOut->status === 'rejected')
                        <div class="status-badge bg-danger bg-opacity-10 p-4 rounded-4 mb-3">
                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                            <h4 class="text-danger mb-0">مرفوض</h4>
                            <small class="text-muted">تم رفض الأمر</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- الإجراءات -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-cog text-primary me-2"></i>
                        الإجراءات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($stockOut->status === 'pending')
                            <a href="{{ route('inventory.stock-out.edit', $stockOut->id) }}" class="btn btn-primary btn-lg rounded-pill">
                                <i class="fas fa-edit me-2"></i>
                                تعديل الأمر
                            </a>
                            <button type="button" class="btn btn-success btn-lg rounded-pill" onclick="approveOrder()">
                                <i class="fas fa-check me-2"></i>
                                اعتماد الأمر
                            </button>
                            <button type="button" class="btn btn-danger btn-lg rounded-pill" onclick="deleteOrder()">
                                <i class="fas fa-trash me-2"></i>
                                حذف الأمر
                            </button>
                        @else
                            <button class="btn btn-secondary btn-lg rounded-pill" disabled>
                                <i class="fas fa-lock me-2"></i>
                                لا يمكن التعديل
                            </button>
                        @endif
                        <a href="{{ route('inventory.stock-out.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill">
                            <i class="fas fa-arrow-right me-2"></i>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.status-badge {
    transition: all 0.3s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}
</style>

<script>
function approveOrder() {
    Swal.fire({
        title: 'اعتماد أمر الصرف',
        text: 'هل أنت متأكد من اعتماد هذا الأمر؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، اعتماد',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            // TODO: إرسال طلب الاعتماد
            Swal.fire('تم الاعتماد!', 'تم اعتماد الأمر بنجاح', 'success');
        }
    });
}

function deleteOrder() {
    Swal.fire({
        title: 'حذف أمر الصرف',
        text: 'هل أنت متأكد من حذف هذا الأمر؟ لا يمكن التراجع عن هذا الإجراء!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، حذف',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            // TODO: إرسال طلب الحذف
            Swal.fire('تم الحذف!', 'تم حذف الأمر بنجاح', 'success');
        }
    });
}
</script>
@endsection

@extends('layouts.app')

@section('title', 'عرض تفاصيل الاستلام')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-info text-white shadow-lg">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="mb-2">
                                <i class="fas fa-file-invoice me-2"></i>
                                تفاصيل استلام البضائع
                            </h1>
                            <p class="mb-0 opacity-75">رقم الاستلام: <strong>{{ $receipt->receipt_number ?? 'N/A' }}</strong></p>
                        </div>
                        <div>
                            <a href="{{ route('purchases.receipts.edit', $receipt->id ?? 0) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-2"></i>
                                تعديل
                            </a>
                            <a href="{{ route('purchases.receipts.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-right me-2"></i>
                                رجوع
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('purchases.dashboard') }}">نظام المشتريات</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchases.receipts.index') }}">استلام البضائع</a></li>
                    <li class="breadcrumb-item active">عرض التفاصيل</li>
                </ol>
            </nav>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>نجح!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- القسم الأيمن: المعلومات الأساسية -->
        <div class="col-lg-8">
            <!-- معلومات الاستلام -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        معلومات الاستلام
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border-bottom pb-2">
                                <small class="text-muted d-block">رقم الاستلام</small>
                                <strong class="fs-5">{{ $receipt->receipt_number ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2">
                                <small class="text-muted d-block">تاريخ الاستلام</small>
                                <strong class="fs-5">
                                    <i class="fas fa-calendar-alt text-primary me-1"></i>
                                    {{ $receipt->receipt_date ?? date('Y-m-d') }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2">
                                <small class="text-muted d-block">أمر الشراء</small>
                                <strong class="fs-5">
                                    <a href="#" class="text-decoration-none">
                                        {{ $receipt->purchase_order->order_number ?? 'N/A' }}
                                    </a>
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2">
                                <small class="text-muted d-block">رقم إشعار التسليم</small>
                                <strong class="fs-5">{{ $receipt->delivery_note ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات المورد -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-truck text-success me-2"></i>
                        معلومات المورد
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border-bottom pb-2">
                                <small class="text-muted d-block">اسم المورد</small>
                                <strong class="fs-5">
                                    <i class="fas fa-building text-success me-1"></i>
                                    {{ $receipt->supplier->name ?? 'N/A' }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-bottom pb-2">
                                <small class="text-muted d-block">رقم الهاتف</small>
                                <strong class="fs-5">
                                    <i class="fas fa-phone text-success me-1"></i>
                                    {{ $receipt->supplier->phone ?? '-' }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border-bottom pb-2">
                                <small class="text-muted d-block">العنوان</small>
                                <strong>{{ $receipt->supplier->address ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الأصناف المستلمة -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes text-warning me-2"></i>
                        الأصناف المستلمة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>الصنف</th>
                                    <th class="text-center">الكمية المطلوبة</th>
                                    <th class="text-center">الكمية المستلمة</th>
                                    <th class="text-center">الوحدة</th>
                                    <th class="text-center">الحالة</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($receipt->items) && $receipt->items->count() > 0)
                                    @foreach($receipt->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $item->product->code ?? '' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $item->ordered_quantity ?? 0 }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $item->received_quantity ?? 0 }}</span>
                                        </td>
                                        <td class="text-center">{{ $item->unit ?? '-' }}</td>
                                        <td class="text-center">
                                            @if(($item->received_quantity ?? 0) >= ($item->ordered_quantity ?? 0))
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> مكتمل
                                                </span>
                                            @elseif(($item->received_quantity ?? 0) > 0)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> جزئي
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle"></i> لم يستلم
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-box-open fa-2x mb-2"></i>
                                        <p class="mb-0">لا توجد أصناف مستلمة</p>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- الملاحظات -->
            @if(isset($receipt->notes) && !empty($receipt->notes))
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note text-info me-2"></i>
                        ملاحظات
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $receipt->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- القسم الأيسر: معلومات إضافية وإجراءات -->
        <div class="col-lg-4">
            <!-- حالة الاستلام -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-flag text-danger me-2"></i>
                        حالة الاستلام
                    </h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $statusConfig = [
                            'partial' => ['class' => 'warning', 'icon' => 'exclamation-triangle', 'text' => 'استلام جزئي'],
                            'complete' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'استلام كامل'],
                            'damaged' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'تالف'],
                        ];
                        $status = $receipt->status ?? 'partial';
                        $config = $statusConfig[$status] ?? $statusConfig['partial'];
                    @endphp
                    <div class="badge bg-{{ $config['class'] }} p-3 fs-5 w-100">
                        <i class="fas fa-{{ $config['icon'] }} me-2"></i>
                        {{ $config['text'] }}
                    </div>
                </div>
            </div>

            <!-- إحصائيات الاستلام -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        إحصائيات الاستلام
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">إجمالي الأصناف</span>
                            <strong class="fs-5">{{ $receipt->items->count() ?? 0 }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">الأصناف المكتملة</span>
                            @php
                                $completedItems = $receipt->items->filter(function($item) {
                                    return ($item->received_quantity ?? 0) >= ($item->ordered_quantity ?? 0);
                                })->count();
                            @endphp
                            <strong class="fs-5 text-success">{{ $completedItems }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $receipt->items->count() > 0 ? ($completedItems / $receipt->items->count() * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">الأصناف الجزئية</span>
                            @php
                                $partialItems = $receipt->items->filter(function($item) {
                                    return ($item->received_quantity ?? 0) > 0 && ($item->received_quantity ?? 0) < ($item->ordered_quantity ?? 0);
                                })->count();
                            @endphp
                            <strong class="fs-5 text-warning">{{ $partialItems }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ $receipt->items->count() > 0 ? ($partialItems / $receipt->items->count() * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات النظام -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-cog text-secondary me-2"></i>
                        معلومات النظام
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">تاريخ الإنشاء</small>
                        <strong>
                            <i class="fas fa-calendar-plus text-success me-1"></i>
                            {{ $receipt->created_at ?? date('Y-m-d H:i:s') }}
                        </strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">آخر تحديث</small>
                        <strong>
                            <i class="fas fa-calendar-check text-warning me-1"></i>
                            {{ $receipt->updated_at ?? date('Y-m-d H:i:s') }}
                        </strong>
                    </div>
                    <div>
                        <small class="text-muted d-block">أنشئ بواسطة</small>
                        <strong>
                            <i class="fas fa-user text-primary me-1"></i>
                            {{ $receipt->user->name ?? 'النظام' }}
                        </strong>
                    </div>
                </div>
            </div>

            <!-- الإجراءات -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks text-info me-2"></i>
                        الإجراءات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('purchases.receipts.edit', $receipt->id ?? 0) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>
                            تعديل الاستلام
                        </a>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>
                            طباعة
                        </button>
                        <button type="button" class="btn btn-info">
                            <i class="fas fa-file-pdf me-2"></i>
                            تصدير PDF
                        </button>
                        <hr>
                        <form action="{{ route('purchases.receipts.destroy', $receipt->id ?? 0) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الاستلام؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>
                                حذف الاستلام
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .btn, .breadcrumb, nav, .card-header .fas {
        display: none !important;
    }
    .card {
        border: 1px solid #000 !important;
        page-break-inside: avoid;
    }
}
</style>
@endpush
@endsection

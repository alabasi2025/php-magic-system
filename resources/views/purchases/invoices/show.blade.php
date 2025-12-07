@extends('layouts.app')

@section('title', 'عرض فاتورة المشتريات')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        فاتورة مشتريات #{{ $invoice->invoice_number ?? '' }}
                    </h4>
                    <div>
                        <a href="{{ route('purchases.invoices.edit', $invoice->id ?? 0) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>
                            تعديل
                        </a>
                        <button type="button" class="btn btn-light btn-sm me-2" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>
                            طباعة
                        </button>
                        <a href="{{ route('purchases.invoices.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body" id="printableArea">
                    <!-- رأس الفاتورة -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h3 class="text-primary mb-3">
                                <i class="fas fa-building me-2"></i>
                                {{ config('app.name', 'نظام الإدارة') }}
                            </h3>
                            <p class="mb-1">
                                <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                <strong>العنوان:</strong> {{ $company->address ?? 'العنوان غير متوفر' }}
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-phone me-2 text-muted"></i>
                                <strong>الهاتف:</strong> {{ $company->phone ?? 'غير متوفر' }}
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-envelope me-2 text-muted"></i>
                                <strong>البريد الإلكتروني:</strong> {{ $company->email ?? 'غير متوفر' }}
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="mb-3">
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusLabels = [
                                        'draft' => 'مسودة',
                                        'pending' => 'معلقة',
                                        'approved' => 'معتمدة',
                                        'cancelled' => 'ملغاة'
                                    ];
                                    $status = $invoice->status ?? 'draft';
                                @endphp
                                <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }} fs-6 px-3 py-2">
                                    <i class="fas fa-flag me-1"></i>
                                    {{ $statusLabels[$status] ?? 'غير محدد' }}
                                </span>
                            </div>
                            <h4 class="text-muted mb-3">فاتورة مشتريات</h4>
                            <p class="mb-1">
                                <strong>رقم الفاتورة:</strong> 
                                <span class="text-primary">#{{ $invoice->invoice_number ?? '' }}</span>
                            </p>
                            <p class="mb-1">
                                <strong>التاريخ:</strong> 
                                {{ \Carbon\Carbon::parse($invoice->invoice_date ?? now())->format('Y-m-d') }}
                            </p>
                            <p class="mb-1">
                                <strong>تاريخ الإنشاء:</strong> 
                                {{ \Carbon\Carbon::parse($invoice->created_at ?? now())->format('Y-m-d H:i') }}
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- معلومات المورد -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary mb-3">
                                        <i class="fas fa-truck me-2"></i>
                                        معلومات المورد
                                    </h5>
                                    <p class="mb-2">
                                        <strong>الاسم:</strong> {{ $invoice->supplier->name ?? 'غير محدد' }}
                                    </p>
                                    <p class="mb-2">
                                        <strong>الهاتف:</strong> {{ $invoice->supplier->phone ?? 'غير متوفر' }}
                                    </p>
                                    <p class="mb-2">
                                        <strong>البريد الإلكتروني:</strong> {{ $invoice->supplier->email ?? 'غير متوفر' }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>العنوان:</strong> {{ $invoice->supplier->address ?? 'غير متوفر' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        تفاصيل الدفع
                                    </h5>
                                    <p class="mb-2">
                                        <strong>طريقة الدفع:</strong>
                                        @php
                                            $paymentMethods = [
                                                'cash' => 'نقداً',
                                                'credit' => 'آجل',
                                                'bank_transfer' => 'تحويل بنكي',
                                                'check' => 'شيك'
                                            ];
                                        @endphp
                                        <span class="badge bg-primary">
                                            {{ $paymentMethods[$invoice->payment_method ?? 'cash'] ?? 'غير محدد' }}
                                        </span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>الحالة:</strong>
                                        <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                            {{ $statusLabels[$status] ?? 'غير محدد' }}
                                        </span>
                                    </p>
                                    <p class="mb-0">
                                        <strong>المستخدم:</strong> {{ $invoice->user->name ?? 'غير محدد' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- جدول الأصناف -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-boxes text-info me-2"></i>
                                أصناف الفاتورة
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="35%">اسم الصنف</th>
                                            <th width="10%" class="text-center">الكمية</th>
                                            <th width="15%" class="text-end">السعر</th>
                                            <th width="15%" class="text-end">الخصم</th>
                                            <th width="20%" class="text-end">الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $subtotal = 0;
                                            $totalDiscount = 0;
                                        @endphp
                                        @forelse(($invoice->items ?? []) as $index => $item)
                                            @php
                                                $itemTotal = ($item->quantity * $item->price) - $item->discount;
                                                $subtotal += ($item->quantity * $item->price);
                                                $totalDiscount += $item->discount;
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $item->product->name ?? 'غير محدد' }}</strong>
                                                    @if(isset($item->product->code))
                                                        <br>
                                                        <small class="text-muted">كود: {{ $item->product->code }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                                <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                                <td class="text-end">{{ number_format($item->discount, 2) }}</td>
                                                <td class="text-end"><strong>{{ number_format($itemTotal, 2) }}</strong></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                    لا توجد أصناف في هذه الفاتورة
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- الإجماليات -->
                    <div class="row mb-4">
                        <div class="col-md-7">
                            @if($invoice->notes ?? '')
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-sticky-note text-warning me-2"></i>
                                            ملاحظات
                                        </h6>
                                        <p class="mb-0">{{ $invoice->notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-5">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-end"><strong>المجموع الفرعي:</strong></td>
                                                <td class="text-end" width="40%">
                                                    <strong>{{ number_format($subtotal, 2) }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-end"><strong>الخصم الإجمالي:</strong></td>
                                                <td class="text-end">
                                                    <strong class="text-danger">{{ number_format($totalDiscount, 2) }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-end">
                                                    <strong>الضريبة ({{ number_format($invoice->tax_rate ?? 0, 2) }}%):</strong>
                                                </td>
                                                <td class="text-end">
                                                    @php
                                                        $taxAmount = (($subtotal - $totalDiscount) * ($invoice->tax_rate ?? 0)) / 100;
                                                    @endphp
                                                    <strong>{{ number_format($taxAmount, 2) }}</strong>
                                                </td>
                                            </tr>
                                            <tr class="border-top">
                                                <td class="text-end">
                                                    <h5 class="text-primary mb-0">الإجمالي النهائي:</h5>
                                                </td>
                                                <td class="text-end">
                                                    <h5 class="text-primary mb-0">
                                                        <strong>{{ number_format($invoice->total_amount ?? 0, 2) }}</strong>
                                                    </h5>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- التوقيعات -->
                    <div class="row mt-5 pt-4 border-top d-print-block d-none">
                        <div class="col-md-4 text-center">
                            <div class="mb-5 pb-3"></div>
                            <div class="border-top pt-2">
                                <strong>المورد</strong>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="mb-5 pb-3"></div>
                            <div class="border-top pt-2">
                                <strong>المستلم</strong>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="mb-5 pb-3"></div>
                            <div class="border-top pt-2">
                                <strong>المدير المالي</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="card-footer bg-white d-print-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                آخر تحديث: {{ \Carbon\Carbon::parse($invoice->updated_at ?? now())->diffForHumans() }}
                            </span>
                        </div>
                        <div>
                            @if(($invoice->status ?? '') !== 'cancelled')
                                <a href="{{ route('purchases.invoices.edit', $invoice->id ?? 0) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i>
                                    تعديل الفاتورة
                                </a>
                            @endif
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-1"></i>
                                حذف الفاتورة
                            </button>
                            <form id="deleteForm" action="{{ route('purchases.invoices.destroy', $invoice->id ?? 0) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .d-print-none {
            display: none !important;
        }
        .d-print-block {
            display: block !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .card-header {
            background-color: #0dcaf0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            background: white !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('هل أنت متأكد من حذف هذه الفاتورة؟ لا يمكن التراجع عن هذا الإجراء.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush
@endsection

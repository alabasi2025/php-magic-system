@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-file-invoice"></i> تفاصيل أمر الشراء</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('purchases.dashboard') }}">نظام المشتريات</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('purchases.orders.index') }}">أوامر الشراء</a></li>
                            <li class="breadcrumb-item active">تفاصيل الأمر</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('purchases.orders.edit', $order->id ?? 1) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    <button type="button" class="btn btn-info" onclick="window.print()">
                        <i class="fas fa-print"></i> طباعة
                    </button>
                    <button type="button" class="btn btn-danger" onclick="deleteOrder()">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- معلومات الأمر -->
        <div class="col-lg-8">
            <!-- بطاقة المعلومات الأساسية -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> معلومات الأمر</h5>
                        @php
                            $statusColors = [
                                'draft' => 'secondary',
                                'pending' => 'warning',
                                'approved' => 'success',
                                'received' => 'info',
                                'cancelled' => 'danger'
                            ];
                            $statusLabels = [
                                'draft' => 'مسودة',
                                'pending' => 'قيد الانتظار',
                                'approved' => 'معتمد',
                                'received' => 'مستلم',
                                'cancelled' => 'ملغي'
                            ];
                            $status = $order->status ?? 'pending';
                        @endphp
                        <span class="badge bg-{{ $statusColors[$status] }} fs-6">
                            {{ $statusLabels[$status] }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="border-start border-primary border-4 ps-3">
                                <small class="text-muted d-block">رقم الأمر</small>
                                <h5 class="mb-0">{{ $order->order_number ?? 'PO-001' }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border-start border-primary border-4 ps-3">
                                <small class="text-muted d-block">تاريخ الأمر</small>
                                <h5 class="mb-0">{{ $order->order_date ?? date('Y-m-d') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border-start border-success border-4 ps-3">
                                <small class="text-muted d-block">المورد</small>
                                <h5 class="mb-0">{{ $order->supplier->name ?? 'اسم المورد' }}</h5>
                                @if(isset($order->supplier))
                                <small class="text-muted">
                                    <i class="fas fa-phone"></i> {{ $order->supplier->phone ?? 'لا يوجد' }}
                                </small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border-start border-success border-4 ps-3">
                                <small class="text-muted d-block">تاريخ التسليم المتوقع</small>
                                <h5 class="mb-0">{{ $order->delivery_date ?? 'غير محدد' }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border-start border-info border-4 ps-3">
                                <small class="text-muted d-block">شروط الدفع</small>
                                @php
                                    $paymentTermsLabels = [
                                        'cash' => 'نقدي',
                                        'credit_30' => 'آجل 30 يوم',
                                        'credit_60' => 'آجل 60 يوم',
                                        'credit_90' => 'آجل 90 يوم'
                                    ];
                                    $paymentTerms = $order->payment_terms ?? 'cash';
                                @endphp
                                <h5 class="mb-0">{{ $paymentTermsLabels[$paymentTerms] ?? 'غير محدد' }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border-start border-info border-4 ps-3">
                                <small class="text-muted d-block">الإجمالي الكلي</small>
                                <h4 class="mb-0 text-primary">{{ number_format($order->total ?? 0, 2) }} ريال</h4>
                            </div>
                        </div>
                    </div>
                    
                    @if(isset($order->notes) && $order->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <strong><i class="fas fa-sticky-note"></i> ملاحظات:</strong><br>
                                {{ $order->notes }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- أصناف الأمر -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> أصناف الأمر</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="35%">الصنف</th>
                                    <th width="15%">الكمية</th>
                                    <th width="15%">السعر</th>
                                    <th width="15%">الضريبة</th>
                                    <th width="15%">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($order->items) && count($order->items) > 0)
                                    @foreach($order->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->product->name ?? 'الصنف' }}</strong><br>
                                            <small class="text-muted">{{ $item->product->code ?? '' }}</small>
                                        </td>
                                        <td>{{ number_format($item->quantity, 0) }}</td>
                                        <td>{{ number_format($item->price, 2) }} ريال</td>
                                        <td>{{ number_format($item->tax_amount ?? 0, 2) }} ريال</td>
                                        <td><strong>{{ number_format($item->total, 2) }} ريال</strong></td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>1</td>
                                        <td><strong>صنف تجريبي</strong><br><small class="text-muted">ITEM-001</small></td>
                                        <td>10</td>
                                        <td>100.00 ريال</td>
                                        <td>15.00 ريال</td>
                                        <td><strong>1,015.00 ريال</strong></td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>المجموع الفرعي:</strong></td>
                                    <td><strong>{{ number_format($order->subtotal ?? 1000, 2) }} ريال</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>الضريبة:</strong></td>
                                    <td><strong>{{ number_format($order->tax_total ?? 15, 2) }} ريال</strong></td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="5" class="text-end"><strong>الإجمالي الكلي:</strong></td>
                                    <td><strong class="fs-5">{{ number_format($order->total ?? 1015, 2) }} ريال</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="col-lg-4">
            <!-- إجراءات سريعة -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($status == 'pending')
                        <button type="button" class="btn btn-success" onclick="approveOrder()">
                            <i class="fas fa-check"></i> اعتماد الأمر
                        </button>
                        @endif
                        
                        @if($status == 'approved')
                        <button type="button" class="btn btn-info" onclick="receiveOrder()">
                            <i class="fas fa-box"></i> تأكيد الاستلام
                        </button>
                        @endif
                        
                        @if(in_array($status, ['draft', 'pending']))
                        <button type="button" class="btn btn-danger" onclick="cancelOrder()">
                            <i class="fas fa-ban"></i> إلغاء الأمر
                        </button>
                        @endif
                        
                        <a href="{{ route('purchases.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> معلومات إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">تاريخ الإنشاء</small>
                        <strong>{{ $order->created_at ?? now()->format('Y-m-d H:i') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">آخر تحديث</small>
                        <strong>{{ $order->updated_at ?? now()->format('Y-m-d H:i') }}</strong>
                    </div>
                    @if(isset($order->created_by))
                    <div class="mb-3">
                        <small class="text-muted d-block">أنشئ بواسطة</small>
                        <strong>{{ $order->creator->name ?? 'المستخدم' }}</strong>
                    </div>
                    @endif
                    @if(isset($order->approved_by))
                    <div class="mb-3">
                        <small class="text-muted d-block">اعتمد بواسطة</small>
                        <strong>{{ $order->approver->name ?? 'المستخدم' }}</strong>
                    </div>
                    @endif
                </div>
            </div>

            <!-- إحصائيات الأمر -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> إحصائيات الأمر</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>عدد الأصناف:</span>
                        <strong class="text-primary">{{ isset($order->items) ? count($order->items) : 1 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>إجمالي الكميات:</span>
                        <strong class="text-primary">{{ isset($order->items) ? $order->items->sum('quantity') : 10 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>متوسط سعر الصنف:</span>
                        <strong class="text-primary">
                            {{ number_format(isset($order->items) && count($order->items) > 0 ? $order->subtotal / count($order->items) : 100, 2) }} ريال
                        </strong>
                    </div>
                </div>
            </div>

            <!-- المستندات المرفقة -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-paperclip"></i> المستندات المرفقة</h5>
                </div>
                <div class="card-body">
                    @if(isset($order->attachments) && count($order->attachments) > 0)
                        <ul class="list-unstyled mb-0">
                            @foreach($order->attachments as $attachment)
                            <li class="mb-2">
                                <a href="{{ $attachment->url }}" target="_blank" class="text-decoration-none">
                                    <i class="fas fa-file-pdf text-danger"></i> {{ $attachment->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0 text-center">
                            <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                            لا توجد مستندات مرفقة
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function approveOrder() {
        Swal.fire({
            title: 'اعتماد الأمر',
            text: 'هل أنت متأكد من اعتماد هذا الأمر؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، اعتماد',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إرسال طلب اعتماد الأمر
                Swal.fire('تم الاعتماد!', 'تم اعتماد الأمر بنجاح', 'success');
            }
        });
    }

    function receiveOrder() {
        Swal.fire({
            title: 'تأكيد الاستلام',
            text: 'هل تم استلام جميع الأصناف؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، تأكيد',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إرسال طلب تأكيد الاستلام
                Swal.fire('تم التأكيد!', 'تم تأكيد استلام الأمر بنجاح', 'success');
            }
        });
    }

    function cancelOrder() {
        Swal.fire({
            title: 'إلغاء الأمر',
            text: 'هل أنت متأكد من إلغاء هذا الأمر؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، إلغاء',
            cancelButtonText: 'تراجع'
        }).then((result) => {
            if (result.isConfirmed) {
                // إرسال طلب إلغاء الأمر
                Swal.fire('تم الإلغاء!', 'تم إلغاء الأمر بنجاح', 'success');
            }
        });
    }

    function deleteOrder() {
        Swal.fire({
            title: 'حذف الأمر',
            text: 'هل أنت متأكد من حذف هذا الأمر؟ لا يمكن التراجع عن هذا الإجراء!',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، حذف',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إرسال طلب حذف الأمر
                // window.location.href = "{{ route('purchases.orders.destroy', $order->id ?? 1) }}";
                Swal.fire('تم الحذف!', 'تم حذف الأمر بنجاح', 'success');
            }
        });
    }

    // تنسيق الطباعة
    window.addEventListener('beforeprint', function() {
        document.querySelectorAll('.btn, .breadcrumb').forEach(el => el.style.display = 'none');
    });

    window.addEventListener('afterprint', function() {
        document.querySelectorAll('.btn, .breadcrumb').forEach(el => el.style.display = '');
    });
</script>
@endpush
@endsection

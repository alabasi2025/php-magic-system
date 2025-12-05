@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تفاصيل إذن الإخراج رقم: {{ $stockOut->number }}</h3>
                    <div class="float-end">
                        @can('cancel', $stockOut)
                            @if ($stockOut->status !== 'canceled')
                                <form action="{{ route('stock_outs.cancel', $stockOut) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('هل أنت متأكد من إلغاء إذن الإخراج هذا؟ سيتم إعادة الكميات إلى المخزون.')">إلغاء الإذن</button>
                                </form>
                            @endif
                        @endcan
                        <a href="{{ route('stock_outs.index') }}" class="btn btn-secondary">العودة للقائمة</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>المخزن:</strong> {{ $stockOut->warehouse->name ?? 'N/A' }}</div>
                        <div class="col-md-4"><strong>العميل:</strong> {{ $stockOut->customer->name ?? 'N/A' }}</div>
                        <div class="col-md-4"><strong>التاريخ:</strong> {{ $stockOut->date->format('Y-m-d') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>المرجع:</strong> {{ $stockOut->reference ?? 'لا يوجد' }}</div>
                        <div class="col-md-4"><strong>الحالة:</strong>
                            <span class="badge bg-{{ $stockOut->status === 'canceled' ? 'danger' : 'success' }}">
                                {{ $stockOut->status === 'canceled' ? 'ملغي' : 'مكتمل' }}
                            </span>
                        </div>
                        <div class="col-md-4"><strong>المنشئ:</strong> {{ $stockOut->creator->name ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12"><strong>ملاحظات:</strong> {{ $stockOut->notes ?? 'لا يوجد' }}</div>
                    </div>

                    <hr>
                    <h4>تفاصيل الأصناف المخرجة</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>الصنف</th>
                                <th>الكمية</th>
                                <th>سعر الوحدة</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockOut->details as $detail)
                                <tr>
                                    <td>{{ $detail->item->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($detail->quantity, 2) }}</td>
                                    <td>{{ number_format($detail->unit_price, 2) }}</td>
                                    <td>{{ number_format($detail->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">الإجمالي الكلي:</th>
                                <th>{{ number_format($stockOut->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

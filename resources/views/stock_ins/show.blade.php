@extends('layouts.app')

@section('title', 'عرض إذن إدخال: ' . $stockIn->number)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>عرض إذن إدخال: {{ $stockIn->number }}</h1>
        <div>
            <a href="{{ route('stock_ins.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            @can('update', $stockIn)
                @if ($stockIn->status === 'Draft')
                    <a href="{{ route('stock_ins.edit', $stockIn) }}" class="btn btn-warning">تعديل</a>
                    <form action="{{ route('stock_ins.complete', $stockIn) }}" method="POST" style="display:inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('هل أنت متأكد من ترحيل الإذن؟')">ترحيل الإذن</button>
                    </form>
                @endif
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h4>بيانات الإذن الرئيسية</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>رقم الإذن:</strong> {{ $stockIn->number }}</p>
                    <p><strong>المخزن:</strong> {{ $stockIn->warehouse->name ?? 'N/A' }}</p>
                    <p><strong>المورد:</strong> {{ $stockIn->supplier->name ?? 'N/A' }}</p>
                    <p><strong>تاريخ الإدخال:</strong> {{ $stockIn->date->format('Y-m-d') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>المرجع الخارجي:</strong> {{ $stockIn->reference ?? '-' }}</p>
                    <p><strong>الحالة:</strong> <span class="badge bg-{{ $stockIn->status === 'Completed' ? 'success' : ($stockIn->status === 'Draft' ? 'warning' : 'danger') }}">{{ $stockIn->status }}</span></p>
                    <p><strong>الإجمالي الكلي:</strong> {{ number_format($stockIn->total_amount, 2) }}</p>
                    <p><strong>المنشئ:</strong> {{ $stockIn->createdBy->name ?? 'N/A' }}</p>
                </div>
            </div>
            <p><strong>ملاحظات:</strong> {{ $stockIn->notes ?? '-' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>تفاصيل الأصناف</h4>
        </div>
        <div class="card-body">
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
                    @foreach ($stockIn->details as $detail)
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
                        <th>{{ number_format($stockIn->total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
